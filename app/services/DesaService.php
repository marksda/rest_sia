<?php

namespace MyApp\Services;

use MyApp\Models\Desa;

class DesaService extends AbstractService
{

    /**
	 * Creating a new Desa
	 *
	 * @param json $data
	 */
	public function createDesa($data)
	{
		try {
			$desa   = new Desa();
			$result = $desa->setId($data->id)
                            ->setNama($data->nama)
                            ->setKecamatan($data->kecamatan)
                            ->create();

			if (false === $result) {
				throw new ServiceException('Unable to create Desa', self::ERROR_UNABLE_CREATE_ITEM);
			}

		} catch (\PDOException $e) {
			if ($e->getCode() == 23505) {
				throw new ServiceException('Desa already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

    /**
	 * Updating Desa
	 *
     * @param string $idLama
	 * @param json $dataBaru
	 */
	public function updateDesa($idLama, $dataBaru)
	{
		try {

            $desa = Desa::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $idLama
					]
				]
			);

			if($desa == null) {
				throw new ServiceException('Unable to update Desa', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $dataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_Desa
				SET 
					id = :idBaru, 
					nama = :nama,
                    kecamatan = :idKecamatan
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $dataBaru->id,
						'nama' => $dataBaru->nama,
                        'idKecamatan' => $dataBaru->kecamatan,
						'idLama' => $idLama,
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update Desa', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$desa->setNama($dataBaru->nama);
                $desa->setKecamatan($dataBaru->kecamatan);
				$result = $Desa->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update Desa', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing Desa
	 *
	 * @param int $id
	 */
	public function deleteDesa($id)
	{
		try {
			$desa = Desa::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $id
					]
				]
			);

			if($desa == null) {
				throw new ServiceException('Desa not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $desa->delete()) {
				throw new ServiceException('Unable to delete Desa', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns Desa list
	 *
	 * @return array
	 */
	public function getDesaList()
	{
		try {
			$desa = Desa::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, nama, kecamatan",
				]
			);

			if (false !== $desa) {		
				return $desa->toArray();
			}
			else {
				return [];
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

}