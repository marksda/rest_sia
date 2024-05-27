<?php

namespace MyApp\Services;

use MyApp\Models\NeracaLajur;
use MyApp\Models\DetailNeracaLajur;
use MyApp\Models\BukuBesar;
use Phalcon\Encryption\Security\Random;

class NeracaLajurService extends AbstractService
{

	/**
	 * Creating a new NeracaLajur
	 *
	 * @param stdClass $dataNeracaSaldo
	 * @param stdClass $dataJurnalPenyesuaian
	 */
    public function createNeracaLajur($dataNeracaSaldo, $dataJurnalPenyesuaian)
    {
		try {
			$this->db->begin();
			$neracaLajur = NeracaLajur::findFirst(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $dataNeracaSaldo->tanggal,						
						'idPerusahaan' => $dataNeracaSaldo->perusahaan,
					]
				]
			);
			
			if(!$neracaLajur) {	//neraca lajur belum ada
				$neracaLajur   = new NeracaLajur();
				$random = new Random();
				$idNeracaLajur = $random->base58(12);

				//insert header neraca lajur
				$result = $neracaLajur->setId($idNeracaLajur)
						->setPerusahaan($dataNeracaSaldo->perusahaan)
						->setTanggal($dataNeracaSaldo->tanggal)
						->setTanggal_insert(time())
						->create();

				//insert detail neraca lajur
				
				//insert data neraca saldo
				$data = array();
				$count = 0;
				foreach ($dataNeracaSaldo->detail as $detailNeracaSaldo) {
					$data[] = $random->base58(12);						
					$data[] = $detailNeracaSaldo->getPerusahaan();
					$data[] = $idNeracaLajur;
					$data[] = $detailNeracaSaldo->getAkun();
					$data[] = $detailNeracaSaldo->getDebet_kredit();
					$data[] = $detailNeracaSaldo->getNilai();
					$data[] = time();
					$count++;
				}	

				$values = str_repeat('?,', 6) . '?';
				$sqlInsertDataNeracaSaldo = "INSERT INTO laporan.tbl_detail_neraca_lajur " .
					"(id, perusahaan, neraca_lajur, akun, debet_kredit_neraca_sado, nilai_neraca_saldo, tanggal_insert) VALUES " .
					str_repeat("($values),", $count - 1) . "($values)"; 

				$stmt = $this->db->prepare($sqlInsertDataNeracaSaldo);
				$stmt->execute($data);

			}
			else {	//neraca lajur sudah ada
				$this->db->rollback();
				throw new ServiceException('Unable to create neraca saldo, neraca saldo periode ini sudah ada', self::ERROR_UNABLE_CREATE_ITEM);
			}
			$this->db->commit();
        } catch (\PDOException $e) {
			$this->db->rollback();
            if ($e->getCode() == 23505) {
				throw new ServiceException('Item neraca saldo already exists', self::ERROR_ALREADY_EXISTS, $e);
			} 
			else if ($e->getCode() == 23503){
				throw new ServiceException('Foreign key error', self::ERROR_FOREIGN_KEY_VIOLATION, $e);
			}
			else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }	

	/**
	 * Delete an existing neraca saldo
	 *
	 * @param string $idLama
	 * @param string $idPerusahaanLama
	 */
	public function deleteNeracaLajur($id, $idPerusahaan)
	{
		try {
			$this->db->begin();

			$neracaLajur = NeracaLajur::findFirst(
				[
					'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $id,						
						'perusahaan' => $idPerusahaan
					]
				]
			);

			if($neracaLajur == null) {
				$this->db->rollback();
				throw new ServiceException('Neraca saldo not found', self::ERROR_ITEM_NOT_FOUND);
			}

			$sqlDeleteDetailNeracaLajur = "
				DELETE 
					laporan.tbl_detail_neraca_saldo				
				WHERE 
					neraca_saldo = :idNeracaLajur AND 
					perusahaan = :idPerusahaan
				";

			$success = $this->db->execute(
				$sqlDeleteDetailNeracaLajur, 
				[
					'idNeracaLajur' => $id,
					'idPerusahaan' => $idPerusahaan
				]
			);

			if(!$success) {
				$this->db->rollback();
				throw new ServiceException('Detail neraca saldo not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $neracaLajur->delete()) {
				$this->db->rollback();
				throw new ServiceException('Unable to delete neraca saldo', self::ERROR_UNABLE_DELETE_ITEM);
			}

			$this->db->commit();
		} catch (\PDOException $e) {
			$this->db->rollback();
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns NeracaLajur list
	 *
	 * @return array
	 */
    public function getNeracaLajurList($idPerusahaan, $priodeAkuntansi)
    {
        try {
			$daftarNeracaLajur = NeracaLajur::find(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $periodeAkuntansi,						
						'perusahaan' => $idPerusahaan,
					],
					'order' => 'tanggal DESC'
				]
			);

			if (!$daftarNeracaLajur) {
				return [];
			}

			$i = 0;
			$hasil = array();
            foreach ($daftarNeracaLajur as $neracaLajur) {
				$tmpArrNeraca = array();
				$tmpArrNeraca['id'] = $neracaLajur->getId();
				$tmpArrNeraca['perusahaan'] = $neracaLajur->getPerusahaan();
				$tmpArrNeraca['tanggal'] = $neracaLajur->getTanggal();
				$tmpArrNeraca['detail'] = $neracaLajur->getRelated('detail_neraca_saldo');				
				$hasil[$i] = $tmpArrNeraca;
				$i++;
			}

			return $hasil; 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}