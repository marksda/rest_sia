<?php

namespace MyApp\Services;

use MyApp\Models\OfficeStoreOutlet;
use Phalcon\Encryption\Security\Random;


class OfficeStoreOutletService extends AbstractService
{

    /**
	 * Creating a new office
	 *
	 * @param json $officeData
	 */
    public function createOffice($officeData)
    {
        try {
            $random = new Random();
            $office = new OfficeStoreOutlet();
            $result = $office->setId($random->base58(5))
			               ->setNama($officeData->nama)
                           ->setPropinsi($officeData->propinsi->id)
                           ->setKabupaten($officeData->kabupaten->id)
                           ->setKecamatan($officeData->kecamatan->id)
                           ->setDesa($officeData->desa->id)
                           ->setDetail_alamat($officeData->detail_alamat)
                           ->setPerusahaan($officeData->perusahaan->id)
                           ->setTelepone($officeData->telepone)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create office', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Office already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    /**
	 * Updating office
	 *
     * @param string $officeIdLama
	 * @param json $officeDataBaru
	 */
	public function updateOffice($officeIdLama, $officeDataBaru)
	{
		try {

            $office = OfficeStoreOutlet::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $officeIdLama
					]
				]
			);

			if($office == null) {
				throw new ServiceException('Unable to update office', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($officeIdLama != $officeDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_office_store_outlet
				SET 
					id = :idBaru, 
					nama = :nama,
					propinsi = :propinsi,
                    kabupaten = :kabupaten,
                    kecamatan = :kecamatan,
                    desa = :desa,
                    detail_alamat = :detail_alamat,
                    perusahaan = :perusahaan, 
                    telepone = :telepone
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $officeDataBaru->id,
						'nama' => $officeDataBaru->nama,
						'propinsi' => $officeDataBaru->propinsi->id,
						'kabupaten' => $officeDataBaru->kabupaten->id,
                        'kecamatan' => $officeDataBaru->kecamatan->id,
                        'desa' => $officeDataBaru->desa->id,
                        'detail_alamat' => $officeDataBaru->detail_alamat,
                        'perusahaan' => $officeDataBaru->perusahaan->id,
                        'telepone' => $perusahaanDataBaru->telepone,
						'idLama' => $perusahaanIdLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update office', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$office->setNama($officeDataBaru->nama);
                $office->setPropinsi($officeDataBaru->propinsi->id);
                $office->setKabupaten($officeDataBaru->kabupaten->id);
                $office->setKecamatan($officeDataBaru->kecamatan->id);
                $office->setDesa($officeDataBaru->desa->id);
                $office->setDetail_alamat($officeDataBaru->detail_alamat);                
                $office->setPerusahaan($officeDataBaru->perusahaan->id);
                $office->setTelepone($officeDataBaru->telepone);
				$result = $office->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update office', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing office
	 *
	 * @param int $officeId
	 */
	public function deleteOffice($officeId)
	{
		try {
			$office = OfficeStoreOutlet::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $officeId
					]
				]
			);

			if($office == null) {
				throw new ServiceException('Office not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $office->delete()) {
				throw new ServiceException('Unable to delete office', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns office list
	 *
	 * @return array
	 */
    public function getOfficeList()
    {
        try {
			$daftarOffice = OfficeStoreOutlet::find(
				[
					'conditions' => '',
					'bind'       => []
				]
			);

			if (!$daftarOffice) {
				return [];
			}

			$i = 0;
			$hasil = array();
            foreach ($daftarOffice as $office) {
                $office->setPropinsi($office->getRelated('detail_propinsi'));
				$office->setKabupaten($office->getRelated('detail_kabupaten'));
				$office->setKecamatan($office->getRelated('detail_kecamatan'));
				$office->setDesa($office->getRelated('detail_desa'));

                $perusahaan = $office->getRelated('detail_perusahaan');
                $perusahaan->setPropinsi($perusahaan->getRelated('detail_propinsi'));
				$perusahaan->setKabupaten($perusahaan->getRelated('detail_kabupaten'));
				$perusahaan->setKecamatan($perusahaan->getRelated('detail_kecamatan'));
				$perusahaan->setDesa($perusahaan->getRelated('detail_desa'));

                $office->setPerusahaan($perusahaan);
				$hasil[$i] = $office;
				$i++;
            }

			return $hasil; 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}