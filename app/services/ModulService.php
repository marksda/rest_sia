<?php

namespace MyApp\Services;

use MyApp\Models\Modul;
use Phalcon\Encryption\Security\Random;


class ModulService extends AbstractService
{

    /**
	 * Creating a new modul
	 *
	 * @param json $modulData
	 */
    public function createModul($modulData)
    {
        try {
            $random = new Random();
            $modul = new Modul();
            $result = $modul->setId($random->base58(4))
			               ->setNama($modulData->nama)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create modul', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('modul already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    /**
	 * Updating modul
	 *
     * @param string $modulIdLama
	 * @param json $modulDataBaru
	 */
	public function updateModul($modulIdLama, $modulDataBaru)
	{
		try {

            $modul = Modul::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $modulIdLama
					]
				]
			);

			if($modul == null) {
				throw new ServiceException('Unable to update modul', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($modulIdLama != $modulDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_modul
				SET 
					id = :idBaru, 
					nama = :nama
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $modulDataBaru->id,
						'nama' => $modulDataBaru->nama,
						'idLama' => $modulIdLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update modul', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$modul->setNama($modulDataBaru->nama);
				$result = $modul->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update modul', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing modul
	 *
	 * @param int $modulId
	 */
	public function deleteModul($modulId)
	{
		try {
			$modul = Modul::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $modulId
					]
				]
			);

			if($modul == null) {
				throw new ServiceException('Modul not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $Modul->delete()) {
				throw new ServiceException('Unable to delete modul', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Returns modul list
	 *
	 * @return array
	 */
    public function getModulList()
    {
        try {
			$daftarModul = Modul::find(
				[
					'conditions' => '',
					'bind'       => [],
				]
			);

			if (!$daftarModul) {
				return [];
			}

			return $daftarModul->toArray(); 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}