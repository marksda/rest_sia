<?php

namespace MyApp\Services;

use MyApp\Models\Kabupaten;

class KabupatenService extends AbstractService
{

    /**
	 * Creating a new Kabupaten
	 *
	 * @param json $data
	 */
	public function createKabupaten($data)
	{
		try {
			$kabupaten   = new Kabupaten();
			$result = $kabupaten->setId($data->id)
                            ->setNama($data->nama)
                            ->create();

			if (false === $result) {
				throw new ServiceException('Unable to create Kabupaten', self::ERROR_UNABLE_CREATE_ITEM);
			}

		} catch (\PDOException $e) {
			if ($e->getCode() == 23505) {
				throw new ServiceException('Kabupaten already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

    /**
	 * Updating Kabupaten
	 *
     * @param string $idLama
	 * @param json $dataBaru
	 */
	public function updateKabupaten($idLama, $dataBaru)
	{
		try {

            $kabupaten = Kabupaten::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $idLama
					]
				]
			);

			if($kabupaten == null) {
				throw new ServiceException('Unable to update kabupaten', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $dataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_kabupaten
				SET 
					id = :idBaru, 
					nama = :nama
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $dataBaru->id,
						'nama' => $dataBaru->nama,
						'idLama' => $idLama,
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update Kabupaten', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$kabupaten->setNama($dataBaru->nama);
				$result = $kabupaten->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update Kabupaten', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing propisni
	 *
	 * @param int $id
	 */
	public function deleteKabupaten($id)
	{
		try {
			$kabupaten = Kabupaten::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $id
					]
				]
			);

			if($kabupaten == null) {
				throw new ServiceException('Kabupaten not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $kabupaten->delete()) {
				throw new ServiceException('Unable to delete Kabupaten', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns Kabupaten list
	 *
	 * @return array
	 */
	public function getKabupatenList()
	{
		try {
			$kabupaten = Kabupaten::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, nama, propinsi",
				]
			);

			if (false !== $kabupaten) {		
				return $kabupaten->toArray();
			}
			else {
				return [];
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

}