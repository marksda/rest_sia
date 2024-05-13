<?php

namespace MyApp\Services;

use MyApp\Models\BukuBesar;
use Phalcon\Encryption\Security\Random;


class BukuBesarService extends AbstractService
{

    /**
	 * Creating a new item buku besar
     * sumber data berasal dari neraca / neraca saldo penutup
     * periode sebelumnya dan jurnal khusus.
	 *
	 * @param json $bukuBesarData
	 */
    public function createBukuBesar($bukuBesarData)
    {
        try {
            $bukuBesar = new BukuBesar();

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

            if(!$lastSaldoAkunBukuBesar) {
                $result = $bukuBesar->setId($random->base58(12))
                            ->setPerusahaan($bukuBesarData->perusahaan->id)
                            ->setTanggal($bukuBesarData->tanggal)
                            ->setKeterangan($bukuBesarData->keterangan)
                            ->setAkun($bukuBesarData->akun->id)
                            ->setDebet_kredit_nilai($bukuBesarData->debet_kredit_nilai)
                            ->setNilai($bukuBesarData->nilai)
                            ->setDebet_kredit_saldo($bukuBesarData->debet_kredit_saldo)
                            ->setSaldo($bukuBesarData->saldo)
                            ->setDetail_jurnal($bukuBesarData->detail_jurnal->id)
                            ->setRef($bukuBesarData->ref)
                            ->create();
                
                if (!$result) {
                    throw new ServiceException('Unable to create item buku besar', self::ERROR_UNABLE_CREATE_ITEM);
                }
            }
            else {
                $saldoAkhir = 0.00;
                $jenisDebetKredit = true;

                if($lastSaldoAkunBukuBesar->getDebet_kredit_saldo() == $bukuBesarData->debet_kredit_saldo) {
                    $jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
                    $saldoAkhir = $lastSaldoAkunBukuBesar->setSaldo() + $bukuBesarData->nilai;
                }
                else {
                    $saldoAkhir = $lastSaldoAkunBukuBesar->setSaldo();
                    if($saldoAkhir >= $bukuBesarData->nilai) {
                        $jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
                        $saldoAkhir = $saldoAkhir - $bukuBesarData->nilai;
                    }
                    else {
                        $jenisDebetKredit = $bukuBesarData->debet_kredit_nilai;
                        $saldoAkhir = $bukuBesarData->nilai - $saldoAkhir;
                    }
                }
            
                $result = $bukuBesar->setId($random->base58(12))
                            ->setPerusahaan($bukuBesarData->perusahaan->id)
                            ->setJurnal($bukuBesarData->jurnal->id)
                            ->setTanggal($bukuBesarData->tanggal)
                            ->setKeterangan($bukuBesarData->keterangan)
                            ->setAkun($bukuBesarData->akun->id)
                            ->setDebet_kredit_nilai($bukuBesarData->debet_kredit_nilai)
                            ->setNilai($bukuBesarData->nilai)
                            ->setDebet_kredit_saldo($jenisDebetKredit)
                            ->setSaldo($saldoAkhir)
                            ->setDetail_jurnal($bukuBesarData->detail_jurnal->id)
                            ->setRef($bukuBesarData->ref)
                            ->create();
                
                if (!$result) {
                    throw new ServiceException('Unable to create item buku besar', self::ERROR_UNABLE_CREATE_ITEM);
                }
            }            
            
        } catch (\PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Item buku besat already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    /**
	 * Updating item buku besar
	 *
     * @param string $jurnalIdLama
     * @param string $perusahaanIdLama
     * @param string $akunIdLama
	 * @param json $bukuBesarDataBaru
	 */
	// public function updateBukuBesar($jurnalIdLama, $perusahaanIdLama, $akunIdLama, $bukuBesarDataBaru)
	// {
	// 	try {

    //         $bukuBesar = BukuBesar::findFirst(
	// 			[
	// 				'conditions' => 'akun = :idAkun: AND jurnal = :idJurnal: AND perusahaan = :idPerusahaan:',
    //                 'bind'       => [
    //                     'idAkun' => $akunIdLama,	
    //                     'idJurnal' => $jurnalIdLama,						
    //                     'perusahaan' => $perusahaanIdLama,
    //                 ]
	// 			]
	// 		);

	// 		if($bukuBesar == null) {
	// 			throw new ServiceException('Unable to update item nuku besar', self::ERROR_UNABLE_UPDATE_ITEM);
	// 		}		
			
	// 		if($jurnalIdLama != $bukuBesarDataBaru->jurnal->id && 
    //                 $perusahaanIdLama != $bukuBesarDataBaru->perusahaan->id &&
    //                 $akunIdLama != $bukuBesarDataBaru->akun->id) {
	// 			$sql     = "
	// 			UPDATE 
	// 				transaksi.tbl_buku_besar
	// 			SET 
	// 				jurnal = :idJurnalBaru, 
	// 				perusahaan = :idPerusahaanBaru,
    //                 akun = :idAkunBaru,
    //                 tanggal = :tanggal, 
	// 				keterangan = :keterangan,
    //                 debet_kredit_nilai = :dbn,
    //                 nilai = :nilai, 
	// 				debet_kredit_saldo = :dbs,
    //                 saldo = :saldo,
	// 			WHERE
	// 				akun = :idAkunLama AND jurnal = :idJurnalLama AND perusahaan = :idPerusahaanLama
	// 			";

	// 			$success = $this->db->execute(
	// 				$sql,
	// 				[
	// 					'idJurnalBaru' => $bukuBesarDataBaru->jurnal->id,
	// 					'idPerusahaanBaru' => $bukuBesarDataBaru->perusahaan->id,
    //                     'idAkunBaru' => $bukuBesarDataBaru->akun->id,
	// 					'tanggal' => $bukuBesarDataBaru->tanggal,
    //                     'keterangan' => $bukuBesarDataBaru->keterangan,
    //                     'dbn' => $bukuBesarDataBaru->debet_kredit_nilai,
    //                     'nilai' => $bukuBesarDataBaru->nilai,
    //                     'dbs' => $bukuBesarDataBaru->debet_kredit_saldo,
    //                     'saldo' => $bukuBesarDataBaru->saldo,
    //                     'idJurnalLama' => $jurnalIdLama,
    //                     'idPerusahaanLama' => $perusahaanIdLama,
    //                     'idAkunLama' => $akunIdLama
	// 				]
	// 			);

	// 			if(false === $success) {
	// 				throw new ServiceException('Unable to update item buku besar', self::ERROR_UNABLE_UPDATE_ITEM);
	// 			}
	// 		}
	// 		else {
	// 			$bukuBesar->setTanggal($bukuBesarDataBaru->tanggal);
    //             $bukuBesar->setKeterangan($bukuBesarDataBaru->keterangan);
    //             $bukuBesar->setDebet_kredit_nilai($bukuBesarDataBaru->debet_kredit_nilai);
    //             $bukuBesar->setNilai($bukuBesarDataBaru->nilai);
    //             $bukuBesar->setDebet_kredit_saldo($bukuBesarDataBaru->debet_kredit_saldo);
    //             $bukuBesar->setSaldo($bukuBesarDataBaru->saldo);
	// 			$result = $bukuBesar->update();

	// 			if ( false === $result) {
	// 				throw new ServiceException('Unable to update item buku besar', self::ERROR_UNABLE_UPDATE_ITEM);
	// 			}
	// 		}
	// 	} catch (\PDOException $e) {
	// 		throw new ServiceException($e->getMessage(), $e->getCode(), $e);
	// 	}
	// }

    /**
	 * Delete an existing item buku besar
	 *
	 * @param string $jurnalIdLama
     * @param string $perusahaanIdLama
     * @param string $akunIdLama
	 */
	// public function deleteBukuBesar($jurnalIdLama, $perusahaanIdLama, $akunIdLama)
	// {
	// 	try {
	// 		$bukuBesar = BukuBesar::findFirst(
	// 			[
	// 				'conditions' => 'akun = :idAkun: AND jurnal = :idJurnal: AND perusahaan = :idPerusahaan:',
    //                 'bind'       => [
    //                     'idAkun' => $akunIdLama,	
    //                     'idJurnal' => $jurnalIdLama,						
    //                     'perusahaan' => $perusahaanIdLama,
    //                 ]
	// 			]
	// 		);

	// 		if($bukuBesar == null) {
	// 			throw new ServiceException('Item buku besar not found', self::ERROR_ITEM_NOT_FOUND);
	// 		}
			
	// 		if (false === $bukuBesar->delete()) {
	// 			throw new ServiceException('Unable to delete item buku besar', self::ERROR_UNABLE_DELETE_ITEM);
	// 		}
	// 	} catch (\PDOException $e) {
	// 		throw new ServiceException($e->getMessage(), $e->getCode(), $e);
	// 	}
	// }

    /**
	 * Returns hak akses list
	 *
	 * @return array
	 */
    public function getBukuBesarList()
    {
        try {
			$daftarBukuBesar = BukuBesar::find(
				[
					'conditions' => '',
					'bind'       => [],
				]
			);

			if (!$daftarBukuBesar) {
				return [];
			}

			return $daftarBukuBesar->toArray(); 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}