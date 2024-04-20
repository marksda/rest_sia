<?php

namespace MyApp\Services;

use MyApp\Models\Perusahaan;
use Phalcon\Encryption\Security\Random;


class PerusahaanService extends AbstractService
{

    /**
	 * Creating a new perusahaan
	 *
	 * @param json $perusahaanData
	 */
    public function createPerusahaan($perusahaanData)
    {
        try {
            $random = new Random();
            $perusahaan = new Perusahaan();
            $result = $perusahaan->setId($random->base58(12))
			               ->setNama($perusahaanData->nama)
                           ->setNpwp($perusahaanData->npwp)
                           ->setPropinsi($perusahaanData->propinsi)
                           ->setKabupaten($perusahaanData->kabupaten)
                           ->setKecamatan($perusahaanData->kecamatan)
                           ->setDesa($perusahaanData->desa)
                           ->setDetail_alamat($perusahaanData->detail_alamat)
                           ->setTelepone($perusahaanData->telepone)
                           ->setEmail($perusahaanData->email)
                           ->setTanggal_registrasi($perusahaanData->tanggal_registrasi)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create perusahaan', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Perusahaan already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    /**
	 * Updating perusahaan
	 *
     * @param string $perusahaanIdLama
	 * @param json $perusahaanDataBaru
	 */
	public function updatePerusahaan($perusahaanIdLama, $perusahaanDataBaru)
	{
		try {

            $perusahaan = Perusahaan::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $perusahaanIdLama
					]
				]
			);

			if($perusahaan == null) {
				throw new ServiceException('Unable to update perusahaan', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($perusahaanIdLama != $perusahaanDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_perusahaan
				SET 
					id = :idBaru, 
					nama = :nama,
                    npwp = :npwp,
					propinsi = :propinsi,
                    kabupaten = :kabupaten,
                    kecamatan = :kecamatan,
                    desa = :desa,
                    detail_alamat = :detail_alamat,
                    telepone = :telepone,
                    email = :email,
                    tanggal_registrasi = :tanggal_registrasi
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $perusahaanDataBaru->id,
						'nama' => $perusahaanDataBaru->nama,
                        'npwp' => $perusahaanDataBaru->npwp,
						'propinsi' => $perusahaanDataBaru->propinsi->id,
						'kabupaten' => $perusahaanDataBaru->kabupaten->id,
                        'kecamatan' => $perusahaanDataBaru->kecamatan->id,
                        'desa' => $perusahaanDataBaru->desa->id,
                        'detail_alamat' => $perusahaanDataBaru->detail_alamat,
                        'telepone' => $perusahaanDataBaru->telepone,
                        'email' => $perusahaanDataBaru->email,
                        'tanggal_registrasi' => $perusahaanDataBaru->tanggal_registrasi,
						'idLama' => $perusahaanIdLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update perusahaan', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$perusahaan->setNama($perusahaanData->nama);
                $perusahaan->setNpwp($perusahaanDataBaru->npwp);
                $perusahaan->setPropinsi($perusahaanDataBaru->propinsi->id);
                $perusahaan->setKabupaten($perusahaanDataBaru->kabupaten->id);
                $perusahaan->setKecamatan($perusahaanDataBaru->kecamatan->id);
                $perusahaan->setDesa($perusahaanDataBaru->desa->id);
                $perusahaan->setDetail_alamat($perusahaanDataBaru->detail_alamat);
                $perusahaan->setTelepone($perusahaanDataBaru->telepone);
                $perusahaan->setEmail($perusahaanDataBaru->email);
                $perusahaan->setTanggal_registrasi($perusahaanDataBaru->tanggal_registrasi);
				$result = $perusahaan->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update perusahaan', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing perusahaan
	 *
	 * @param int $perusahaanId
	 */
	public function deletePerusahaan($perusahaanId)
	{
		try {
			$perusahaan = Perusahaan::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $perusahaanId
					]
				]
			);

			if($perusahaan == null) {
				throw new ServiceException('Perusahaan not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $perusahaan->delete()) {
				throw new ServiceException('Unable to delete perusahaan', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns perusahaan list
	 *
	 * @return array
	 */
    public function getPerusahaanList()
    {
        try {
			$daftarPerusahaan = Perusahaan::find(
				[
					'conditions' => '',
					'bind'       => [],
					// 'columns'    => "id, nama, npwp, kabupaten, kecamatan, desa, detail_alamat, telepone, email, tanggal_registrasi",
				]
			);

			if (!$daftarPerusahaan) {
				return [];
			}

			$i = 0;
			$hasil = array();
            foreach ($daftarPerusahaan as $perusahaan) {
				// $detailPropinsi = $perusahaan->getRelated('detail_propinsi');
                $perusahaan->setPropinsi($perusahaan->getRelated('detail_propinsi'));
				$perusahaan->setKabupaten($perusahaan->getRelated('detail_kabupaten'));
				$perusahaan->setKecamatan($perusahaan->getRelated('detail_kecamatan'));
				$perusahaan->setDesa($perusahaan->getRelated('detail_desa'));
				$hasil[$i] = $perusahaan;
				$i++;
            }

			return $hasil; 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}