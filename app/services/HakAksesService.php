<?php

namespace MyApp\Services;

use MyApp\Models\HakAkses;
use Phalcon\Encryption\Security\Random;


class HakAksesService extends AbstractService
{

    /**
	 * Creating a new Hak akses
	 *
	 * @param json $hakAksesData
	 */
    public function createHakAkses($hakAksesData)
    {
        try {
            $random = new Random();
            $HakAkses = new HakAkses();
            $result = $HakAkses->setId($random->base58(4))
			               ->setNama($hakAksesData->nama)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create hak akses', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Hak akses already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    /**
	 * Updating hak akses
	 *
     * @param string $hakAksesIdLama
	 * @param json $hakAksesDataBaru
	 */
	public function updateHakAkses($hakAksesIdLama, $hakAksesDataBaru)
	{
		try {

            $hakAkses = HakAkses::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $hakAksesIdLama
					]
				]
			);

			if($hakAkses == null) {
				throw new ServiceException('Unable to update hak akses', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($hakAksesIdLama != $hakAksesDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_hak_akses
				SET 
					id = :idBaru, 
					nama = :nama
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $hakAksesDataBaru->id,
						'nama' => $hakAksesDataBaru->nama,
						'idLama' => $hakAksesIdLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update hak akses', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$hakAkses->setNama($hakAksesDataBaru->nama);
				$result = $hakAkses->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update hak akses', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing hak akses
	 *
	 * @param int $hakAksesId
	 */
	public function deleteHakAkses($hakAksesId)
	{
		try {
			$hakAkses = HakAkses::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $hakAksesId
					]
				]
			);

			if($hakAkses == null) {
				throw new ServiceException('Hak akses not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $hakAkses->delete()) {
				throw new ServiceException('Unable to delete hak akses', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns hak akses list
	 *
	 * @return array
	 */
    public function getHakAksesList()
    {
        try {
			$daftarHakAkses = HakAkses::find(
				[
					'conditions' => '',
					'bind'       => [],
				]
			);

			if (!$daftarHakAkses) {
				return [];
			}

			return $daftarHakAkses->toArray(); 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}