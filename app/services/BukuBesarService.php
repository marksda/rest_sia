<?php

namespace MyApp\Services;

use MyApp\Models\BukuBesar;
use Phalcon\Encryption\Security\Random;


class BukuBesarService extends AbstractService
{

    /**
	 * Creating a new item buku besar
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
                $result = $bukuBesar->setJurnal($bukuBesarData->jurnal->id)
                            ->setPerusahaan($bukuBesarData->perusahaan->id)
                            ->setAkun($bukuBesarData->akun->id)
                            ->setTanggal($bukuBesarData->tanggal)
                            ->setKeterangan($bukuBesarData->keterangan)
                            ->setDebet_kredit_nilai($bukuBesarData->debet_kredit_nilai)
                            ->setNilai($bukuBesarData->nilai)
                            ->setDebet_kredit_saldo($bukuBesarData->debet_kredit_saldo)
                            ->setSaldo($bukuBesarData->saldo)
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
            
                $result = $bukuBesar->setJurnal($bukuBesarData->jurnal->id)
                            ->setPerusahaan($bukuBesarData->perusahaan->id)
                            ->setAkun($bukuBesarData->akun->id)
                            ->setTanggal($bukuBesarData->tanggal)
                            ->setKeterangan($bukuBesarData->keterangan)
                            ->setDebet_kredit_nilai($bukuBesarData->debet_kredit_nilai)
                            ->setNilai($bukuBesarData->nilai)
                            ->setDebet_kredit_saldo($jenisDebetKredit)
                            ->setSaldo($saldoAkhir)
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
     * @param string $bukuBesarIdLama
	 * @param json $bukuBesarDataBaru
	 */
	public function updateBukuBesar($bukuBesarIdLama, $bukuBesarDataBaru)
	{
		try {

            $bukuBesar = BukuBesar::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $bukuBesarIdLama
					]
				]
			);

			if($bukuBesar == null) {
				throw new ServiceException('Unable to update hak akses', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($bukuBesarIdLama != $bukuBesarDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_hak_akses
				SET 
					id = :idBaru, 
					nama = :nama,
                    modul = :modul
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $bukuBesarDataBaru->id,
						'nama' => $bukuBesarDataBaru->nama,
                        'modul' => $bukuBesarDataBaru->modul->id,
						'idLama' => $bukuBesarIdLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update hak akses', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$bukuBesar->setNama($bukuBesarDataBaru->nama);
                $bukuBesar->setModul($bukuBesarDataBaru->modul->id);
				$result = $bukuBesar->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update hak akses', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
	 * Delete an existing item buku besar
	 *
	 * @param int $bukuBesarId
	 */
	public function deleteBukuBesar($bukuBesarId)
	{
		try {
			$bukuBesar = BukuBesar::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $bukuBesarId
					]
				]
			);

			if($bukuBesar == null) {
				throw new ServiceException('Hak akses not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $bukuBesar->delete()) {
				throw new ServiceException('Unable to delete hak akses', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

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

            $i = 0;
			$hasil = array();
            foreach ($daftarBukuBesar as $bukuBesar) {
                $bukuBesar->setModul($bukuBesar->getRelated('detail_modul'));
				$hasil[$i] = $bukuBesar;
				$i++;
            }

			return $hasil; 
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}