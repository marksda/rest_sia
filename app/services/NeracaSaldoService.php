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

				if(!$lastSaldoAkunBukuBesar) {
					$this->db->rollback();
					throw new ServiceException('Unable to create neraca saldo, tidak ada transaksi untuk priode ini', self::ERROR_UNABLE_CREATE_ITEM);
				}
				else {
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

					foreach($daftarSaldoAkunBukuBesar as $lastSaldoAkunBukuBesar) {

					}
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
	 * Returns NeracaSaldo list
	 *
	 * @return array
	 */
    public function getNeracaSaldoList($idPerusahaan, $startPriodeAkuntansi, $endPriodeAkuntansi)
    {
        try {
			$lastSaldoAkunBukuBesar = BukuBesar::findFirst(
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

			return $daftarNeracaSaldo->toArray(); 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}