<?php

namespace MyApp\Services;

use MyApp\Models\Akun;
use Phalcon\Encryption\Security\Random;


class AkunService extends AbstractService
{

    /**
	 * Creating a new akun
	 *
	 * @param json $akunData
	 */
    public function createAkun($akunData)
    {		
        try {
            $random = new Random();
            $akun = new Akun();
            $result = $akun->setId($random->base58(6))
			               ->setPerusahaan($akunData->perusahaan->id)
                           ->setHeader($akunData->header)
                           ->setLevel($akunData->level)
                           ->setNama($akunData->nama)
                           ->setKode($akunData->kode)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create akun', self::ERROR_UNABLE_CREATE_ITEM);
			}

        } catch (\PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Akun already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        } 
    }

    /**
	 * Updating akun
	 *
     * @param string $akunIdLama
	 * @param json $akunDataBaru
	 */
	public function updateAkun($idLama, $idPerusahaanLama, $akunDataBaru)
	{
		try {

            $akun = Akun::findFirst(
				[
					'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $akunIdLama,
						'perusahaan' => $idPerusahaanLama
					]
				]
			);

			if($akun == null) {
				throw new ServiceException('Unable to update akun', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $akunDataBaru->id && $idPerusahaanLama != $akunDataBaru->perusahaan->id) {
				$sql     = "
				UPDATE 
					public.tbl_akun
				SET 
					id = :idBaru, 
					perusahaan = :perusahaan,
                    header = :header,
					level = :level,
                    nama = :nama,
                    kode = :kode
				WHERE
					id = :idLama AND perusahaan = :idPerusahaanLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $akunDataBaru->id,
						'perusahaan' => $akunDataBaru->perusahaan->id,
                        'header' => $akunDataBaru->header,
						'level' => $akunDataBaru->level,
						'nama' => $akunDataBaru->nama,
                        'kode' => $akunDataBaru->kode,
						'idLama' => $idLama,
						'idPerusahaanLama' => $idPerusahaanLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update akun', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$akun->setPerusahaan($akunDataBaru->perusahaan->id);
                $akun->setHeader($akunDataBaru->header);
                $akun->setLevel($akunDataBaru->level);
                $akun->setNama($akunDataBaru->nama);
                $akun->setKode($akunDataBaru->kode);
				$result = $akun->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update akun', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing akun
	 *
	 * @param int $akunId
	 */
	public function deleteAkun($idLama, $idPerusahaanLama)
	{
		try {
			$akun = Akun::findFirst(
				[
					'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $idLama,						
						'perusahaan' => $idPerusahaanLama
					]
				]
			);

			if($akun == null) {
				throw new ServiceException('Akun not found', self::ERROR_ITEM_NOT_FOUND);
			}

			if (false === $akun->delete()) {
				throw new ServiceException('Unable to delete akun', self::ERROR_UNABLE_DELETE_ITEM);
			}
            
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns akun list
	 *
	 * @return array
	 */
    public function getAkunList()
    {
        try {
			$daftarAkun = Akun::find(
				[
					'conditions' => '',
					'bind'       => [],
					// 'columns'    => "id, nama, npwp, kabupaten, kecamatan, desa, detail_alamat, telepone, email, tanggal_registrasi",
				]
			);

			if (!$daftarAkun) {
				return [];
			}
			
			return $daftarAkun->toArray(); 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }
}