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
    public function createNeracaSaldo($jurnalData)
    {
		try {
			$this->db->begin();
        
            $random = new Random();
            $jurnal = new NeracaSaldo();

			//insert row to jurnal
			$idNeracaSaldo = $random->base58(12);
            $result = $jurnal->setId($idNeracaSaldo)
						->setKeterangan($jurnalData->keterangan)
						->setTanggal($jurnalData->tanggal)
						->setJenis_jurnal($jurnalData->jenis_jurnal)
						->setPerusahaan($jurnalData->perusahaan->id)
						->setOffice_store_outlet($jurnalData->office_store_outlet->id)
						->setRef_bukti($jurnalData->ref_bukti)
						->create();
            
			if (!$result) {
				$this->db->rollback();
				throw new ServiceException('Unable to create NeracaSaldo', self::ERROR_UNABLE_CREATE_ITEM);
			}

			$keteranganPostingBukuBesar = "";

			switch ($jurnalData->jenis_jurnal) {
				case '01':
					$keteranganPostingBukuBesar = "";
					break;
				case '02':
					$keteranganPostingBukuBesar = "";
					break;
				case '03':
					$keteranganPostingBukuBesar = "";
					break;
				case '04':
					$keteranganPostingBukuBesar = $jurnalData->keterangan;
					break;
				case '05':
					$keteranganPostingBukuBesar = $jurnalData->keterangan;
					break;
				case '06':
					$keteranganPostingBukuBesar = $jurnalData->keterangan;
					break;
				case '07':
					$keteranganPostingBukuBesar = $jurnalData->keterangan;
					break;
				case '08':
					$keteranganPostingBukuBesar = $jurnalData->keterangan;
					break;
				case '09':
					$keteranganPostingBukuBesar = $jurnalData->keterangan;
					break;
				case '10':
					$keteranganPostingBukuBesar = $jurnalData->keterangan;
					break;
				default:
					$keteranganPostingBukuBesar = "Posting";
					break;
			}

			$daftarItemNeracaSaldo = $jurnalData->daftarItemNeracaSaldo;

			//insert detail jurnal dan posting ke buku besar
			foreach ($daftarItemNeracaSaldo as $itemNeracaSaldo) {
				//insert item detail jurnal
				$detailNeracaSaldo = new DetailNeracaSaldo();
				$idDetailNeracaSaldo = $random->base58(12);
				$result = $detailNeracaSaldo->setId($idDetailNeracaSaldo)
							->setNeracaSaldo($idNeracaSaldo)
							->setPerusahaan($itemNeracaSaldo->perusahaan->id)
							->setAkun($itemNeracaSaldo->akun->id)
							->setDebet_kredit($itemNeracaSaldo->debet_kredit)
							->setNilai($itemNeracaSaldo->nilai)
							->create();
            
				if (!$result) {
					$this->db->rollback();
					throw new ServiceException('Unable to create detailNeracaSaldo', self::ERROR_UNABLE_CREATE_ITEM);
				}

				//insert posting buku besar
				$lastSaldoAkunBukuBesar = BukuBesar::findFirst(
					[
						'conditions' => 'akun = :idAkun: AND perusahaan = :idPerusahaan:',
						'bind'       => [
							'idAkun' => $itemNeracaSaldo->akun->id,						
							'perusahaan' => $jurnalData->perusahaan->id,
						],
						'order' => 'tanggal DESC'
					]
				);
				
				$bukuBesar = new BukuBesar();

				if(!$lastSaldoAkunBukuBesar) {
					$result = $bukuBesar->setId($random->base58(12))
							->setPerusahaan($jurnalData->perusahaan->id)
							->setTanggal($jurnalData->tanggal)
							->setKeterangan($keteranganPostingBukuBesar)
							->setAkun($itemNeracaSaldo->akun->id)
							->setDebet_kredit_nilai($itemNeracaSaldo->debet_kredit)
							->setNilai($itemNeracaSaldo->nilai)
							->setDebet_kredit_saldo($itemNeracaSaldo->debet_kredit)
							->setSaldo($itemNeracaSaldo->nilai)
							->setDetail_jurnal($idDetailNeracaSaldo)
							->setRef($jurnalData->jenis_jurnal->singkatan)
							->create();
					
					if (!$result) {
						$this->db->rollback();
						throw new ServiceException('Unable to create detailNeracaSaldo', self::ERROR_UNABLE_CREATE_ITEM);
					}
				}
				else {
					$saldoAkhir = 0.00;
					$jenisDebetKredit = true;

					if($lastSaldoAkunBukuBesar->getDebet_kredit_saldo() == $itemNeracaSaldo->debet_kredit) {
						$jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
						$saldoAkhir = $lastSaldoAkunBukuBesar->setSaldo() + $itemNeracaSaldo->nilai;
					}
					else {
						$saldoAkhir = $lastSaldoAkunBukuBesar->setSaldo();
						if($saldoAkhir >= $itemNeracaSaldo->nilai) {
							$jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
							$saldoAkhir = $saldoAkhir - $itemNeracaSaldo->nilai;
						}
						else {
							$jenisDebetKredit = $itemNeracaSaldo->debet_kredit;
							$saldoAkhir = $itemNeracaSaldo->nilai - $saldoAkhir;
						}
					}

					$result = $bukuBesar->setId($random->base58(12))
							->setPerusahaan($jurnalData->perusahaan->id)
							->setTanggal($jurnalData->tanggal)
							->setKeterangan($keteranganPostingBukuBesar)
							->setAkun($itemNeracaSaldo->akun->id)
							->setDebet_kredit_nilai($itemNeracaSaldo->debet_kredit)
							->setNilai($itemNeracaSaldo->nilai)
							->setDebet_kredit_saldo($jenisDebetKredit)
							->setSaldo($saldoAkhir)
							->setDetail_jurnal($idDetailNeracaSaldo)
							->setRef($jurnalData->jenis_jurnal->singkatan)
							->create();
					
					if (!$result) {
						$this->db->rollback();
						throw new ServiceException('Unable to create detailNeracaSaldo', self::ERROR_UNABLE_CREATE_ITEM);
					}
				}	
			}
			
			$this->db->commit();
        } catch (\PDOException $e) {
			$this->db->rollback();
            if ($e->getCode() == 23505) {
				throw new ServiceException('Item jurnal atau buku besar already exists', self::ERROR_ALREADY_EXISTS, $e);
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
    public function getNeracaSaldoList()
    {
        try {
			$daftarNeracaSaldo = NeracaSaldo::find(
				[
					'conditions' => '',
					'bind'       => []
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