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
		$this->db->begin();
        try {
            $random = new Random();
            $perusahaan = new Perusahaan();
			$id = $random->base58(12);
            $result = $perusahaan->setId($id)
			               ->setNama($perusahaanData->nama)
                           ->setNpwp($perusahaanData->npwp)
                           ->setPropinsi($perusahaanData->propinsi->id)
                           ->setKabupaten($perusahaanData->kabupaten->id)
                           ->setKecamatan($perusahaanData->kecamatan->id)
                           ->setDesa($perusahaanData->desa->id)
                           ->setDetail_alamat($perusahaanData->detail_alamat)
                           ->setTelepone($perusahaanData->telepone)
                           ->setEmail($perusahaanData->email)
                           ->setTanggal_registrasi($perusahaanData->tanggal_registrasi)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create perusahaan', self::ERROR_UNABLE_CREATE_ITEM);
			}

			$sqlCreateTablePartition = "CREATE TABLE public.akun_".$id." PARTITION OF public.tbl_akun FOR VALUES IN ('".$id."')";
			
			$success = $this->db->execute($sqlCreateTablePartition);	// menggunakan raw sql

			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to create table partition', self::ERROR_UNABLE_UPDATE_ITEM);
			}

			$sqlFetchAkunTemplate = "
				SELECT 
					'" . $id . "' as perusahaan, header::int, level, nama, id as kode, jenis_akun, urutan " .
				"FROM public.tbl_akun_template " .
				"ORDER BY urutan ASC";

			$rows = $this->db->fetchAll($sqlFetchAkunTemplate);	// menggunakan raw sql

			$data = array();
			foreach ($rows as $rowData) {
				$data[] = $random->base58(6);
				foreach ($rowData as $rowField) {					
					$data[] = $rowField;
				}
			}

			$values = str_repeat('?,', 7) . '?';
			$sqlInsertAkun = "INSERT INTO public.tbl_akun VALUES " .
							str_repeat("($values),", count($rows) - 1) . "($values)"; 

			$stmt = $this->db->prepare($sqlInsertAkun);
			$stmt->execute($data);	// menggunakan raw sql

			$sqlCreateTablePartition = "CREATE TABLE transaksi.jurnal_".$id." PARTITION OF transaksi.tbl_jurnal FOR VALUES IN ('".$id."')";
			
			$success = $this->db->execute($sqlCreateTablePartition);	// menggunakan raw sql

			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to create table partition', self::ERROR_UNABLE_UPDATE_ITEM);
			}

			$sqlCreateTablePartition = "CREATE TABLE transaksi.detail_jurnal_".$id." PARTITION OF transaksi.tbl_detail_jurnal FOR VALUES IN ('".$id."')";
			
			$success = $this->db->execute($sqlCreateTablePartition);	// menggunakan raw sql

			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to create table partition', self::ERROR_UNABLE_UPDATE_ITEM);
			}

			$sqlCreateTablePartition = "CREATE TABLE transaksi.buku_besar_".$id." PARTITION OF transaksi.tbl_buku_besar FOR VALUES IN ('".$id."')";
			
			$success = $this->db->execute($sqlCreateTablePartition);	// menggunakan raw sql

			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to create table partition', self::ERROR_UNABLE_UPDATE_ITEM);
			}
			
			$this->db->commit();
        } catch (\PDOException $e) {
			$this->db->rollback();
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
				$perusahaan->setNama($perusahaanDataBaru->nama);
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
		$this->db->begin();
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
			
			$sqlDropTablePartition = "DROP TABLE transaksi.detail_jurnal_".$perusahaanId." CASCADE";			
			$success = $this->db->execute($sqlDropTablePartition);
			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to delete table partition', self::ERROR_UNABLE_DELETE_ITEM);
			}

			$sqlDropTablePartition = "DROP TABLE transaksi.jurnal_".$perusahaanId." CASCADE";			
			$success = $this->db->execute($sqlDropTablePartition);
			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to delete table partition', self::ERROR_UNABLE_DELETE_ITEM);
			}

			$sqlDropTablePartition = "DROP TABLE transaksi.buku_besar_".$perusahaanId." CASCADE";			
			$success = $this->db->execute($sqlDropTablePartition);
			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to delete table partition', self::ERROR_UNABLE_DELETE_ITEM);
			}	

			$sqlDropTablePartition = "DROP TABLE public.akun_".$perusahaanId." CASCADE";			
			$success = $this->db->execute($sqlDropTablePartition);
			if(false === $success) {
				$this->db->rollback();
				throw new ServiceException('Unable to delete table partition', self::ERROR_UNABLE_DELETE_ITEM);
			}	

			if (false === $perusahaan->delete()) {
				$this->db->rollback();
				throw new ServiceException('Unable to delete perusahaan', self::ERROR_UNABLE_DELETE_ITEM);
			}

			$this->db->commit();
		} catch (\PDOException $e) {
			$this->db->rollback();
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
                $perusahaan->setPropinsi($perusahaan->getRelated('detail_propinsi'));
				$perusahaan->setKabupaten($perusahaan->getRelated('detail_kabupaten'));
				$perusahaan->setKecamatan($perusahaan->getRelated('detail_kecamatan'));
				$perusahaan->setDesa($perusahaan->getRelated('detail_desa'));
				$hasil[$i] = $perusahaan;
				$i++;
            }

			return $hasil; 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }
}