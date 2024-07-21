<?php

namespace MyApp\Services;

use MyApp\Models\KelompokAkun;
use Phalcon\Encryption\Security\Random;


class KelompokAkunService extends AbstractService
{

    /**
	 * Creating a new kelompokAkun
	 *
	 * @param json $kelompokAkunData
	 */
    public function createKelompokAkun($kelompokAkunData)
    {		
        try {
            $kelompokAkun = new KelompokAkun();
            $result = $kelompokAkun->setId($kelompokAkunData->id)
                           ->setNama($kelompokAkunData->nama)
						   ->setJenis_akun($kelompokAkunData->jenis_akun)
			               ->create();	//menggunakan model sql
            
			if (!$result) {
				throw new ServiceException('Unable to create kelompok akun', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (\PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Kelompok akun already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        } 
    }

    /**
	 * Updating kelompokAkun
	 *
     * @param string $kelompokAkunIdLama
	 * @param json $kelompokAkunDataBaru
	 */
	public function updateKelompokAkun($idLama, $kelompokAkunDataBaru)
	{
		try {
            $kelompokAkun = KelompokAkun::findFirst(
				[
					'conditions' => 'id = :idLama:',
					'bind'       => [
						'idLama' => $idLama
					]
				]
			); //menggunakan model sql

			if($kelompokAkun == null) {
				throw new ServiceException('Unable to update kelompok akun', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $kelompokAkunDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_kelompokAkun
				SET 
					id = :idBaru, 
                    nama = :nama,
					jenis_akun = :jenis_akun
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $kelompokAkunDataBaru->id,
						'nama' => $kelompokAkunDataBaru->nama,
						'jenis_akun' => $kelompokAkunDataBaru->jenis_akun,
						'idLama' => $idLama
					]
				);	// menggunakan raw sql

				if(false === $success) {
					throw new ServiceException('Unable to update kelompokAkun', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
                $kelompokAkun->setNama($kelompokAkunDataBaru->nama);
				$kelompokAkun->setJenis_akun($kelompokAkunDataBaru->jenis_akun);
				$result = $kelompokAkun->update();	//menggunakan model sql

				if ( false === $result) {
					throw new ServiceException('Unable to update kelompokAkun', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing kelompokAkun
	 *
	 * @param int $kelompokAkunId
	 */
	public function deleteKelompokAkun($idLama)
	{
		try {
			$kelompokAkun = KelompokAkun::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $idLama
					]
				]
			);

			if($kelompokAkun == null) {
				throw new ServiceException('KelompokAkun not found', self::ERROR_ITEM_NOT_FOUND);
			}

			if (false === $kelompokAkun->delete()) {  //menggunakan model sql
				throw new ServiceException('Unable to delete kelompokAkun', self::ERROR_UNABLE_DELETE_ITEM);
			}
            
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns kelompokAkun list
	 *
	 * @param stdClass $filter
	 * @return array
	 */
    public function getKelompokAkunList()
    { 
        try {
			$daftarKelompokAkun = KelompokAkun::find(
				[
					'conditions' => '',
					'bind'       => [],
					// 'columns'    => "id, nama, npwp, kabupaten, kecamatan, desa, detail_alamat, telepone, email, tanggal_registrasi",
				]
			);  // menggunakan model sql

			if (!$daftarKelompokAkun) {
				return [];
			}
			
			return $daftarKelompokAkun->toArray(); 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }
}