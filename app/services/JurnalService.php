<?php

namespace MyApp\Services;

use MyApp\Models\Jurnal;
use MyApp\Models\DetailJurnal;
use MyApp\Models\BukuBesar;
use Phalcon\Encryption\Security\Random;

class JurnalService extends AbstractService
{

	/**
	 * Creating a new Jurnal
	 *
	 * @param json $jurnalData
	 */
    public function createJurnal($jurnalData)
    {
		try {
			$this->db->begin();
        
            $random = new Random();
            $jurnal = new Jurnal();

			//insert row to jurnal
			$idJurnal = $random->base58(12);
            $result = $jurnal->setId($idJurnal)
						->setKeterangan($jurnalData->keterangan)
						->setTanggal($jurnalData->tanggal)
						->setJenis_jurnal($jurnalData->jenis_jurnal)
						->setPerusahaan($jurnalData->perusahaan->id)
						->setOffice_store_outlet($jurnalData->office_store_outlet->id)
						->setRef_bukti($jurnalData->ref_bukti)
						->setTanggal_insert(time())
						->create();
            
			if (!$result) {
				$this->db->rollback();
				throw new ServiceException('Unable to create Jurnal', self::ERROR_UNABLE_CREATE_ITEM);
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

			$daftarItemJurnal = $jurnalData->daftarItemJurnal;

			//insert detail jurnal dan posting ke buku besar
			foreach ($daftarItemJurnal as $itemJurnal) {
				//insert item detail jurnal
				$detailJurnal = new DetailJurnal();
				$idDetailJurnal = $random->base58(12);
				$result = $detailJurnal->setId($idDetailJurnal)
							->setJurnal($idJurnal)
							->setPerusahaan($itemJurnal->perusahaan->id)
							->setAkun($itemJurnal->akun->id)
							->setDebet_kredit($itemJurnal->debet_kredit)
							->setNilai($itemJurnal->nilai)
							->setTanggal_insert(time())
							->create();
            
				if (!$result) {
					$this->db->rollback();
					throw new ServiceException('Unable to create detailJurnal', self::ERROR_UNABLE_CREATE_ITEM);
				}

				//insert posting buku besar
				$lastSaldoAkunBukuBesar = BukuBesar::findFirst(
					[
						'conditions' => 'akun = :idAkun: AND perusahaan = :idPerusahaan:',
						'bind'       => [
							'idAkun' => $itemJurnal->akun->id,						
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
							->setAkun($itemJurnal->akun->id)
							->setDebet_kredit_nilai($itemJurnal->debet_kredit)
							->setNilai($itemJurnal->nilai)
							->setDebet_kredit_saldo($itemJurnal->debet_kredit)
							->setSaldo($itemJurnal->nilai)
							->setDetail_jurnal($idDetailJurnal)
							->setRef($jurnalData->jenis_jurnal->singkatan)
							->setTanggal_insert(time())
							->create();
					
					if (!$result) {
						$this->db->rollback();
						throw new ServiceException('Unable to create detailJurnal', self::ERROR_UNABLE_CREATE_ITEM);
					}
				}
				else {
					$saldoAkhir = 0.00;
					$jenisDebetKredit = true;

					if($lastSaldoAkunBukuBesar->getDebet_kredit_saldo() == $itemJurnal->debet_kredit) {
						$jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
						$saldoAkhir = $lastSaldoAkunBukuBesar->setSaldo() + $itemJurnal->nilai;
					}
					else {
						$saldoAkhir = $lastSaldoAkunBukuBesar->getSaldo();
						if($saldoAkhir >= $itemJurnal->nilai) {
							$jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
							$saldoAkhir = $saldoAkhir - $itemJurnal->nilai;
						}
						else {
							$jenisDebetKredit = $itemJurnal->debet_kredit;
							$saldoAkhir = $itemJurnal->nilai - $saldoAkhir;
						}
					}

					$result = $bukuBesar->setId($random->base58(12))
							->setPerusahaan($jurnalData->perusahaan->id)
							->setTanggal($jurnalData->tanggal)
							->setKeterangan($keteranganPostingBukuBesar)
							->setAkun($itemJurnal->akun->id)
							->setDebet_kredit_nilai($itemJurnal->debet_kredit)
							->setNilai($itemJurnal->nilai)
							->setDebet_kredit_saldo($jenisDebetKredit)
							->setSaldo($saldoAkhir)
							->setDetail_jurnal($idDetailJurnal)
							->setRef($jurnalData->jenis_jurnal->singkatan)
							->setTanggal_insert(time())
							->create();
					
					if (!$result) {
						$this->db->rollback();
						throw new ServiceException('Unable to create detailJurnal', self::ERROR_UNABLE_CREATE_ITEM);
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
	 * Updating jurnal
	 *
     * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisJurnalLama
	 * @param json $jurnalDataBaru
	 */
	// public function updateJurnal($idLama, $idPerusahaanLama, $jurnalDataBaru)
	// {
	// 	try {
    //         $jurnal = Jurnal::findFirst(
	// 			[
	// 				'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
	// 				'bind'       => [
	// 					'id' => $idLama,					
	// 					'perusahaan' => $idPerusahaanLama
	// 				]
	// 			]
	// 		);

	// 		if($jurnal == null) {
	// 			throw new ServiceException('Unable to update jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
	// 		}		
			
	// 		if($idLama != $jurnalDataBaru->id && $idPerusahaanLama != $jurnalDataBaru->perusahaan) {
	// 			$sql     = "
	// 			UPDATE 
	// 				jurnal.tbl_Jurnal
	// 			SET 
	// 				id = :idBaru, 
	// 				keterangan = :keterangan,
    //                 tanggal = :tanggal,
	// 				jenis_jurnal = : jenisJurnal,
	// 				perusahaan = :perusahaan,
	// 				office_store_outlet = :office,
	// 				ref_bukti = : refBukti
	// 			WHERE
	// 				id = :idLama AND perusahaan = :idPerusahaanLama
	// 			";

	// 			$success = $this->db->execute(
	// 				$sql,
	// 				[
	// 					'idBaru' => $jurnalDataBaru->id,
	// 					'keterangan' => $jurnalDataBaru->keterangan,
    //                     'tanggal' => $jurnalDataBaru->tanggal,
	// 					'jenisJurnal' => $jurnalDataBaru->jenis_jurnal,
	// 					'perusahaan' => $jurnalDataBaru->perusahaan->id,
	// 					'office' => $jurnalDataBaru->office_store_outlet->id,
	// 					'refBukti' => $jurnalDataBaru->ref_bukti,
	// 					'idLama' => $idLama,
	// 					'idPerusahaanLama' => $idPerusahaanLama
	// 				]
	// 			);

	// 			if(false === $success) {
	// 				throw new ServiceException('Unable to update jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
	// 			}
	// 		}
	// 		else {
	// 			$jurnal->setKeterangan($jurnalDataBaru->keterangan);
    //             $jurnal->setTanggal($jurnalDataBaru->tanggal);
	// 			$jurnal->setJenis_jurnal($jurnalDataBaru->jenis_jurnal);
	// 			$jurnal->setOffice_store_outlet($jurnalDataBaru->office_store_outlet->id);
	// 			$jurnal->setRef_bukti($jurnalDataBaru->ref_bukti);
	// 			$result = $jurnal->update();

	// 			if ( false === $result) {
	// 				throw new ServiceException('Unable to update jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
	// 			}
	// 		}
	// 	} catch (\PDOException $e) {
	// 		throw new ServiceException($e->getMessage(), $e->getCode(), $e);
	// 	}
	// }

	/**
	 * Delete an existing jurnal
	 *
	 * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisJurnalLama
	 */
	// public function deleteJurnal($idLama, $idPerusahaanLama)
	// {
	// 	try {
	// 		$jurnal = Jurnal::findFirst(
	// 			[
	// 				'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
	// 				'bind'       => [
	// 					'id' => $idLama,						
	// 					'perusahaan' => $idPerusahaanLama
	// 				]
	// 			]
	// 		);

	// 		if($jurnal == null) {
	// 			throw new ServiceException('Jurnal not found', self::ERROR_ITEM_NOT_FOUND);
	// 		}
			
	// 		if (false === $jurnal->delete()) {
	// 			throw new ServiceException('Unable to delete jurnal', self::ERROR_UNABLE_DELETE_ITEM);
	// 		}
	// 	} catch (\PDOException $e) {
	// 		throw new ServiceException($e->getMessage(), $e->getCode(), $e);
	// 	}
	// }

	/**
	 * Returns Jurnal list
	 *
	 * @param stdClass $filterJurnal
	 * @return array
	 */
    public function getJurnalList($filterJurnal)
    {
        try {
			$daftarJurnal = Jurnal::find(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND jenis_jurnal = :jenisJurnal AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $filterJurnal->tanggal,	
						'jenisJurnal' => $filterJurnal->jenisJurnal->id,			
						'perusahaan' => $filterJurnal->perusahaan->id
					],
					'order' => 'tanggal ASC AND office_store_outlet ASC'
				]
			);

			if (!$daftarJurnal) {
				return [];
			}

			$i = 0;
			$hasil = array();
            foreach ($daftarJurnal as $Jurnal) {
				$tmpArrNeraca = array();
				$tmpArrNeraca['id'] = $neracaSaldo->getId();
				$tmpArrNeraca['keterangan'] = $neracaSaldo->getKeterangan();
				$tmpArrNeraca['tanggal'] = $neracaSaldo->getTanggal();
				$tmpArrNeraca['jenis_jurnal'] = $neracaSaldo->getJenis_jurnal();
				$tmpArrNeraca['perusahaan'] = $neracaSaldo->getPerusahaan();
				$tmpArrNeraca['office_store_outlet'] = $neracaSaldo->getOffice_store_outlet();
				$tmpArrNeraca['ref_bukti'] = $neracaSaldo->getRef_bukti();
				$tmpArrNeraca['tanggal_insert'] = $neracaSaldo->getTanggal_insert();
				$tmpArrNeraca['detail'] = $neracaSaldo->getRelated('detail_jurnal');				
				$hasil[$i] = $tmpArrNeraca;
				$i++;
            }

			return $hasil; 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}