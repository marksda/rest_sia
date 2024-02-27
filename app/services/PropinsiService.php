<?php

namespace MyApp\Services;

use MyApp\Models\Propinsi;

class PropinsiService extends AbstractService
{

    /**
	 * Creating a new propinsi
	 *
	 * @param json $data
	 */
	public function createPropinsi($data)
	{
		try {
			$propinsi   = new Propinsi();
			$result = $barang->setId($data->id)
                            ->setNama($data->nama)
                            ->create();

			if (false === $result) {
				throw new ServiceException('Unable to create propinsi', self::ERROR_UNABLE_CREATE_ITEM);
			}

		} catch (\PDOException $e) {
			if ($e->getCode() == 23505) {
				throw new ServiceException('Propinsi already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

    /**
	 * Updating propinsi
	 *
     * @param string $idLama
	 * @param json $dataBaru
	 */
	public function updatePropinsi($idLama, $dataBaru)
	{
		try {

            $propinsi = Propinsi::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $idLama
					]
				]
			);

			if($propinsi == null) {
				throw new ServiceException('Unable to update propindi', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $dataBaru->id) {
				$sql     = "
				UPDATE 
					public.propinsi
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
					throw new ServiceException('Unable to update propinsi', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$propinsi->setNama($dataBaru->nama);
				$result = $propinsi->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update propinsi', self::ERROR_UNABLE_UPDATE_ITEM);
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
	public function deletePropinsi($id)
	{
		try {
			$propinsi = Propinsi::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $id
					]
				]
			);

			if($propinsi == null) {
				throw new ServiceException('Propinsi not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $propinsi->delete()) {
				throw new ServiceException('Unable to delete propinsi', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns propinsi list
	 *
	 * @return array
	 */
	public function getPropinsiList()
	{
		try {
			$propinsi = Propinsi::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, nama",
				]
			);

			if (false !== $propinsi) {		
				return $propinsi->toArray();
			}
			else {
				return [];
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

}