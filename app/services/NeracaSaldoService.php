<?php

namespace MyApp\Services;

use MyApp\Models\NeracaSaldo;
use MyApp\Models\DetailNeracaSaldo;
use MyApp\Models\BukuBesar;
use Phalcon\Encryption\Security\Random;

class NeracaSaldoService extends AbstractService
{

	/**
	 * Creating a new NeracaSaldo
	 *
	 * @param json $neracaSaldoData
	 */
    public function createNeracaSaldo($neracaSaldoData)
    {
		try {
			$this->db->begin();
			$neracaSaldo = NeracaSaldo::findFirst(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $neracaSaldoData->tanggal,						
						'perusahaan' => $neracaSaldoData->perusahaan->id,
					]
				]
			);
			
			if(!$neracaSaldo) {	//neraca saldo belum ada
				$lastSaldoAkunBukuBesar = BukuBesar::findFirst(
					[
						'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
						'bind'       => [
							'periodeAkuntansi' => $neracaSaldoData->tanggal,					
							'perusahaan' => $neracaSaldoData->perusahaan->id,
						],
						'order' => 'tanggal_insert DESC, tanggal DESC'
					]
				);

				if(!$lastSaldoAkunBukuBesar) {	//tidak ada data transaksi pada buku besar
					$this->db->rollback();
					throw new ServiceException('Unable to create neraca saldo, tidak ada transaksi untuk priode ini', self::ERROR_UNABLE_CREATE_ITEM);
				}
				else {	//ada data transaksi pada buku besar
					$neracaSaldo   = new NeracaSaldo();
					$random = new Random();
					$idNeracaSaldo = $random->base58(12);
					
					$result = $neracaSaldo->setId($idNeracaSaldo)
							->setPerusahaan($neracaSaldoData->perusahaan->id)
							->setTanggal($neracaSaldoData->tanggal)
							->setTanggal_insert(time())
                            ->create();

					$daftarSaldoAkunBukuBesar = BukuBesar::find(
						[
							'distinct' => 'akun',
							'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
							'bind'       => [
								'periodeAkuntansi' => $neracaSaldoData->tanggal,					
								'perusahaan' => $neracaSaldoData->perusahaan->id
							],
							'order' => 'tanggal_insert DESC, tanggal DESC'
						]
					);

					//insert data kedalam detail neraca saldo
					$data = array();
					$count = 0;
					foreach($daftarSaldoAkunBukuBesar as $lastSaldoAkunBukuBesar) {
						$data[] = $random->base58(12);	
						$data[] = $neracaSaldoData->perusahaan->id;
						$data[] = $idNeracaSaldo;
						$data[] = $lastSaldoAkunBukuBesar->getAkun();
						$data[] = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
						$data[] = $lastSaldoAkunBukuBesar->getSaldo();
						$data[] = time();
						$count++;
					}

					$values = str_repeat('?,', 6) . '?';
					$sqlInsertDetailNeracaSaldo = "INSERT INTO laporan.tbl_detail_neraca_saldo VALUES " .
									str_repeat("($values),", $count - 1) . "($values)"; 

					$stmt = $this->db->prepare($sqlInsertDetailNeracaSaldo);
					$stmt->execute($data);
				}
			}
			else {	//neraca saldo sudah ada
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
	public function deleteNeracaSaldo($id, $idPerusahaan)
	{
		try {
			$this->db->begin();

			$neracaSaldo = NeracaSaldo::findFirst(
				[
					'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $id,						
						'perusahaan' => $idPerusahaan
					]
				]
			);

			if($neracaSaldo == null) {
				$this->db->rollback();
				throw new ServiceException('Neraca saldo not found', self::ERROR_ITEM_NOT_FOUND);
			}

			$sqlDeleteDetailNeracaSaldo = "
				DELETE 
					laporan.tbl_detail_neraca_saldo				
				WHERE 
					neraca_saldo = :idNeracaSaldo AND 
					perusahaan = :idPerusahaan
				";

			$success = $this->db->execute(
				$sqlDeleteDetailNeracaSaldo, 
				[
					'idNeracaSaldo' => $id,
					'idPerusahaan' => $idPerusahaan
				]
			);

			if(!$success) {
				$this->db->rollback();
				throw new ServiceException('Detail neraca saldo not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $neracaSaldo->delete()) {
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
	 * Returns NeracaSaldo list
	 *
	 * @return array
	 */
    public function getNeracaSaldoList($idPerusahaan, $priodeAkuntansi)
    {
        try {
			$daftarNeracaSaldo = NeracaSaldo::find(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $periodeAkuntansi,						
						'perusahaan' => $idPerusahaan,
					],
					'order' => 'tanggal DESC'
				]
			);

			if (!$daftarNeracaSaldo) {
				return [];
			}

			$i = 0;
			$hasil = array();
            foreach ($daftarNeracaSaldo as $neracaSaldo) {
				$tmpArrNeraca = array();
				$tmpArrNeraca['id'] = $neracaSaldo->getId();
				$tmpArrNeraca['perusahaan'] = $neracaSaldo->getPerusahaan();
				$tmpArrNeraca['tanggal'] = $neracaSaldo->getTanggal();
				$tmpArrNeraca['detail'] = $neracaSaldo->getRelated('detail_neraca_saldo');				
				$hasil[$i] = $tmpArrNeraca;
				$i++;
			}

			return $hasil; 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}