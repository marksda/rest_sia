<?php

namespace MyApp\Services;

use MyApp\Models\JenisJurnal;


class JenisJurnalService extends AbstractService
{

    /**
	 * Creating a new Jenis Jurnal
	 *
	 * @param json $jenisJurnalData
	 */
    public function createJenisJurnal($jenisJurnalData)
    {
        try {
            $JenisJurnal = new JenisJurnal();
            $result = $JenisJurnal->setId($jenisJurnalData->id)
			               ->setNama($jenisJurnalData->nama)
                           ->setKeterangan($jenisJurnalData->keterangan)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create jenis jurnal', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Jenis jurnal already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    /**
	 * Updating jenis jurnal
	 *
     * @param string $jenisJurnalIdLama
	 * @param json $jenisJurnalDataBaru
	 */
	public function updateJenisJurnal($jenisJurnalIdLama, $jenisJurnalDataBaru)
	{
		try {

            $jenisJurnal = JenisJurnal::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $jenisJurnalIdLama
					]
				]
			);

			if($jenisJurnal == null) {
				throw new ServiceException('Unable to update jenis jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($jenisJurnalIdLama != $jenisJurnalDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_jenis_jurnal
				SET 
					id = :idBaru, 
					nama = :nama,
                    keterangan = :keterangan
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $jenisJurnalDataBaru->id,
						'nama' => $jenisJurnalDataBaru->nama,
                        'keterangan' => $jenisJurnalDataBaru->keterangan,
						'idLama' => $jenisJurnalIdLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update jenis jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$jenisJurnal->setNama($jenisJurnalDataBaru->nama);
                $jenisJurnal->setKeterangan($jenisJurnalDataBaru->keterangan);
				$result = $office->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update jenis jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing jenis jurnal
	 *
	 * @param int $jenisJurnalId
	 */
	public function deleteJenisJurnal($jenisJurnalId)
	{
		try {
			$jenisJurnal = JenisJurnal::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $jenisJurnalId
					]
				]
			);

			if($jenisJurnal == null) {
				throw new ServiceException('Jenis jurnal not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $jenisJurnal->delete()) {
				throw new ServiceException('Unable to delete jenis jurnal', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns jenis jurnal list
	 *
	 * @return array
	 */
    public function getJenisJurnalList()
    {
        try {
			$daftarJenisJurnal = JenisJurnal::find(
				[
					'conditions' => '',
					'bind'       => [],
                    'columns'    => "id, nama, keterangan",
				]
			);

			if (!$daftarJenisJurnal) {
				return [];
			}

			return $daftarJenisJurnal->toArray(); 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}