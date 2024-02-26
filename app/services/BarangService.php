<?php

namespace MyApp\Services;

use MyApp\Models\Barang;

class BarangService extends AbstractService
{

    /**
	 * Creating a new barang
	 *
	 * @param json $barangData
	 */
	public function createBarang($barangData)
	{
		try {
			$barang   = new Barang();
			$result = $barang->setId($barangData->id)
                            ->setNama($barangData->nama)
                            ->create();

			if (false === $result) {
				throw new ServiceException('Unable to create barang', self::ERROR_UNABLE_CREATE_ITEM);
			}

		} catch (\PDOException $e) {
			if ($e->getCode() == 23505) {
				throw new ServiceException('Barang already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

    /**
	 * Updating barang
	 *
     * @param string $barangIdLama
	 * @param json $barangDataBaru
	 */
	public function updateBarang($barangIdLama, $barangDataBaru)
	{
		try {

            $barang = Barang::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $barangIdLama
					]
				]
			);

			if($barang == null) {
				throw new ServiceException('Unable to update barang', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($barangIdLama != $barangDataBaru->id) {
				$sql     = "
				UPDATE 
					public.barang
				SET 
					id = :idBaru, 
					nama = :nama
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $barangDataBaru->id,
						'nama' => $barangDataBaru->nama,
						'idLama' => $barangIdLama,
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update barang', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$barang->setNama($barangDataBaru->nama);
				$result = $barang->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update barang', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing barang
	 *
	 * @param int $barangId
	 */
	public function deleteBarang($barangId)
	{
		try {
			$barang = Barang::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $barangId
					]
				]
			);

			if($barang == null) {
				throw new ServiceException('Barang not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $barang->delete()) {
				throw new ServiceException('Unable to delete barang', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns barang list
	 *
	 * @return array
	 */
	public function getBarangList()
	{
		try {
			$barang = Barang::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, nama",
				]
			);

			if (false !== $barang) {				
				return $barang->toArray();
			}
			else {
				return [];
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

}