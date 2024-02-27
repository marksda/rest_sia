<?php

namespace MyApp\Services;

use MyApp\Models\Kecamatan;

class KecamatanService extends AbstractService
{

    /**
	 * Creating a new Kecamatan
	 *
	 * @param json $data
	 */
	public function createKecamatan($data)
	{
		try {
			$kecamatan   = new Kecamatan();
			$result = $kecamatan->setId($data->id)
                            ->setNama($data->nama)
                            ->create();

			if (false === $result) {
				throw new ServiceException('Unable to create Kecamatan', self::ERROR_UNABLE_CREATE_ITEM);
			}

		} catch (\PDOException $e) {
			if ($e->getCode() == 23505) {
				throw new ServiceException('Kecamatan already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

    /**
	 * Updating Kecamatan
	 *
     * @param string $idLama
	 * @param json $dataBaru
	 */
	public function updateKecamatan($idLama, $dataBaru)
	{
		try {

            $kecamatan = Kecamatan::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $idLama
					]
				]
			);

			if($kecamatan == null) {
				throw new ServiceException('Unable to update Kecamatan', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $dataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_kecamatan
				SET 
					id = :idBaru, 
					nama = :nama,
                    kabupaten = :idKabupaten
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $dataBaru->id,
						'nama' => $dataBaru->nama,
                        'idKabupaten' => $dataBaru->kabupaten,
						'idLama' => $idLama,
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update Kecamatan', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$kecamatan->setNama($dataBaru->nama);
                $kecamatan->setKabupaten($dataBaru->kabupaten);
				$result = $kecamatan->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update Kecamatan', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing Kecamatan
	 *
	 * @param int $id
	 */
	public function deleteKecamatan($id)
	{
		try {
			$kecamatan = Kecamatan::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $id
					]
				]
			);

			if($kecamatan == null) {
				throw new ServiceException('Kecamatan not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $kecamatan->delete()) {
				throw new ServiceException('Unable to delete Kecamatan', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns Kecamatan list
	 *
	 * @return array
	 */
	public function getKecamatanList()
	{
		try {
			$kecamatan = Kecamatan::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, nama, kabupaten",
				]
			);

			if (false !== $kecamatan) {		
				return $kecamatan->toArray();
			}
			else {
				return [];
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

}