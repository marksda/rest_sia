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
	 * @param json $jurnalData
	 */
    public function createNeracaSaldo($idPerusahaan, $periodeAkuntansi)
    {
		try {
			$neracaSaldo = NeracaSaldo::findFirst(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $periodeAkuntansi,						
						'perusahaan' => $idPerusahaan,
					]
				]
			);

			
			if(!$neracaSaldo) {	//neraca saldo belum ada
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

				if(!$lastSaldoAkunBukuBesar) {
					throw new ServiceException('Unable to create neraca saldo', self::ERROR_UNABLE_CREATE_ITEM);
				}
				else {

				}
			}
			else {	//neraca saldo sudah ada
				throw new ServiceException('Unable to create neraca saldo, neraca saldo periode ini sudah ada', self::ERROR_UNABLE_CREATE_ITEM);
			}
			
        } catch (\PDOException $e) {
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