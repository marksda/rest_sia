<?php

namespace MyApp\Services;

use MyApp\Models\MetodePendekatanAkutansi;

class MetodePendekatanAkutansiService extends AbstractService
{

    /**
	 * Creating a new MetodePendekatanAkutansi
	 *
	 * @param stdClass $data
	 */
	public function createMetodePendekatanAkutansi($data)
	{
		try {
			$metodePendekatanAkutansi   = new MetodePendekatanAkutansi();
			$result = $metodePendekatanAkutansi->setId($data->id)
                            ->setKeterangan($data->keterangan)
                            ->create();

			if (false === $result) {
				throw new ServiceException('Unable to create metodePendekatanAkutansi', self::ERROR_UNABLE_CREATE_ITEM);
			}

		} catch (\PDOException $e) {
			if ($e->getCode() == 23505) {
				throw new ServiceException('MetodePendekatanAkutansi already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

    /**
	 * Updating metodePendekatanAkutansi
	 *
     * @param string $idLama
	 * @param stdClass $dataBaru
	 */
	public function updateMetodePendekatanAkutansi($idLama, $dataBaru)
	{
		try {

            $metodePendekatanAkutansi = MetodePendekatanAkutansi::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $idLama
					]
				]
			);

			if($metodePendekatanAkutansi == null) {
				throw new ServiceException('Unable to update propindi', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $dataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_metode_pendekatan_akutansi
				SET 
					id = :idBaru, 
					keterangan = :keterangan
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $dataBaru->id,
						'keterangan' => $dataBaru->keterangan,
						'idLama' => $idLama,
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update metodePendekatanAkutansi', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$metodePendekatanAkutansi->setKeterangan($dataBaru->keterangan);
				$result = $metodePendekatanAkutansi->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update metodePendekatanAkutansi', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing metodePendekatanAkutansi
	 *
	 *
	 * @param int $id
	 */
	public function deleteMetodePendekatanAkutansi($idMetodePendekatanAkutansi)
	{
		try {
			$metodePendekatanAkutansi = MetodePendekatanAkutansi::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $idMetodePendekatanAkutansi
					]
				]
			);

			if($metodePendekatanAkutansi == null) {
				throw new ServiceException('MetodePendekatanAkutansi not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $metodePendekatanAkutansi->delete()) {
				throw new ServiceException('Unable to delete metodePendekatanAkutansi', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns metodePendekatanAkutansi list
	 *
	 * @return array
	 */
	public function getMetodePendekatanAkutansiList()
	{
		try {
			$metodePendekatanAkutansi = MetodePendekatanAkutansi::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, keterangan",
				]
			);

			if (false !== $metodePendekatanAkutansi) {		
				return $metodePendekatanAkutansi->toArray();
			}
			else {
				return [];
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

}