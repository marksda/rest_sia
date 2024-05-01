<?php

namespace MyApp\Services;

use MyApp\Models\DetailJurnal;


class DetailJurnalService extends AbstractService
{

    /**
	 * Creating a new detailJurnal
	 *
	 * @param json $detailJurnalData
	 */
    public function createDetailJurnal($detailJurnalData)
    {		
        try {
            $detailJurnal = new DetailJurnal();
            $result = $detailJurnal->setJurnal($detailJurnalData->jurnal->id)
			               ->setPerusahaan($detailJurnalData->perusahaan->id)
                           ->setAkun($detailJurnalData->akun->id)
                           ->setDebet_kredit($detailJurnalData->debet_kredit)
                           ->setNilai($detailJurnalData->nilai)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create detailJurnal', self::ERROR_UNABLE_CREATE_ITEM);
			}

        } catch (\PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('DetailJurnal already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        } 
    }

    /**
	 * Updating detailJurnal
	 *
     * @param string $detailJurnalIdLama
	 * @param json $detailJurnalDataBaru
	 */
	public function updateDetailJurnal($idJurnalLama, $idPerusahaanLama, $idAkunLama, $detailJurnalDataBaru)
	{
		try {
            $detailJurnal = DetailJurnal::findFirst(
				[
					'conditions' => 'alun = :idakun: AND jurnal = :idJurnal: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'idJurnal' => $idJurnalLama,
						'idPerusahaan' => $idPerusahaanLama,
                        'idakun' => $idAkunLama
					]
				]
			);

			if($detailJurnal == null) {
				throw new ServiceException('Unable to update detailJurnal', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idJurnalLama != $detailJurnalDataBaru->jurnal->id && 
                $idPerusahaanLama != $detailJurnalDataBaru->perusahaan->id &&
                $idAkunLama != $detailJurnalDataBaru->akun->id) {

				$sql = "
				UPDATE 
					public.tbl_detailJurnal
				SET 
                    jurnal = :idJurnal,
                    perusahaan = :idPerusahaan,
                    akun = :idAkun,
					debet_kredit = :debetKredit, 
					nilai = :nilai
				WHERE
					akun = : idAkunLama AND jurnal = :idJurnalLama AND perusahaan = :idPerusahaanLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idJurnal' => $detailJurnalDataBaru->jurnal->id,
						'idPerusahaan' => $detailJurnalDataBaru->perusahaan->id,
                        'idAkun' => $detailJurnalDataBaru->akun->id,
						'debetKredit' => $detailJurnalDataBaru->debet_kredit,
						'nilai' => $detailJurnalDataBaru->nilai
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update detailJurnal', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$detailJurnal->setDebet_kredit($detailJurnalDataBaru->debet_kredit);
                $detailJurnal->setNilai($detailJurnalDataBaru->nilai);
				$result = $detailJurnal->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update detailJurnal', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing detailJurnal
	 *
	 * @param int $detailJurnalId
	 */
	public function deleteDetailJurnal($idJurnalLama, $idPerusahaanLama, $idAkunLama)
	{
		try {
			$detailJurnal = DetailJurnal::findFirst(
				[
					'conditions' => 'akun = :idAkun: AND jurnal = :idJurnal: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'idAkun' => $idAkunLama,						
						'idJurnal' => $idJurnalLama,
                        'idPerusahaan' => $idPerusahaanLama
					]
				]
			);

			if($detailJurnal == null) {
				throw new ServiceException('DetailJurnal not found', self::ERROR_ITEM_NOT_FOUND);
			}

			if (false === $detailJurnal->delete()) {
				throw new ServiceException('Unable to delete detailJurnal', self::ERROR_UNABLE_DELETE_ITEM);
			}
            
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns detailJurnal list
	 *
	 * @return array
	 */
    public function getDetailJurnalList()
    {
        try {
			$daftarDetailJurnal = DetailJurnal::find(
				[
					'conditions' => '',
					'bind'       => [],
				]
			);

			if (!$daftarDetailJurnal) {
				return [];
			}
			
			return $daftarDetailJurnal->toArray(); 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }
}