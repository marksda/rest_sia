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
		$this->db->begin();
        try {
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
						->create();
            
			if (!$result) {
				$this->db->rollback();
				throw new ServiceException('Unable to create Jurnal', self::ERROR_UNABLE_CREATE_ITEM);
			}

			$daftarItemJurnal = $jurnalData->daftarItemJurnal;

			//insert detail jurnal dan posting ke buku besar
			foreach ($daftarItemJurnal as $itemJurnal) {
				//insert item jurnal
				$detailJurnal = new DetailJurnal();
				$result = $detailJurnal->setJurnal($idJurnal)
			               ->setPerusahaan($itemJurnal->perusahaan->id)
                           ->setAkun($itemJurnal->akun->id)
                           ->setDebet_kredit($itemJurnal->debet_kredit)
                           ->setNilai($itemJurnal->nilai)
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

				$saldoAkhir = 0.00;
				$jenisDebetKredit = null;

				if($lastSaldoAkunBukuBesar->getDebet_kredit_saldo() == $itemJurnal->debet_kredit) {
					$jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
					$saldoAkhir = $lastSaldoAkunBukuBesar->setSaldo() + $itemJurnal->nilai;
				}
				else {
					$saldoAkhir = $lastSaldoAkunBukuBesar->setSaldo();
					if($saldoAkhir >= $itemJurnal->nilai) {
						$jenisDebetKredit = $lastSaldoAkunBukuBesar->getDebet_kredit_saldo();
						$saldoAkhir = $saldoAkhir - $itemJurnal->nilai;
					}
					else {
						$jenisDebetKredit = $itemJurnal->debet_kredit;
						$saldoAkhir = $itemJurnal->nilai - $saldoAkhir;
					}
				}

				$bukuBesar = new BukuBesar();
				$result = $bukuBesar->setJurnal($idJurnal)
						->setPerusahaan($jurnalData->perusahaan->id)
						->setAkun($itemJurnal->akun->id)
						->setTanggal($jurnalData->tanggal)
						->setKeterangan("posting")
						->setDebet_kredit_nilai($itemJurnal->debet_kredit)
						->setNilai($itemJurnal->nilai)
						->setDebet_kredit_saldo($jenisDebetKredit)
						->setSaldo($saldoAkhir)
						->create();
				
				if (!$result) {
					$this->db->rollback();
					throw new ServiceException('Unable to create detailJurnal', self::ERROR_UNABLE_CREATE_ITEM);
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
	public function updateJurnal($idLama, $idPerusahaanLama, $jurnalDataBaru)
	{
		try {
            $jurnal = Jurnal::findFirst(
				[
					'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $idLama,					
						'perusahaan' => $idPerusahaanLama
					]
				]
			);

			if($jurnal == null) {
				throw new ServiceException('Unable to update jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $jurnalDataBaru->id && $idPerusahaanLama != $jurnalDataBaru->perusahaan) {
				$sql     = "
				UPDATE 
					jurnal.tbl_Jurnal
				SET 
					id = :idBaru, 
					keterangan = :keterangan,
                    tanggal = :tanggal,
					jenis_jurnal = : jenisJurnal,
					perusahaan = :perusahaan,
					office_store_outlet = :office,
					ref_bukti = : refBukti
				WHERE
					id = :idLama AND perusahaan = :idPerusahaanLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $jurnalDataBaru->id,
						'keterangan' => $jurnalDataBaru->keterangan,
                        'tanggal' => $jurnalDataBaru->tanggal,
						'jenisJurnal' => $jurnalDataBaru->jenis_jurnal,
						'perusahaan' => $jurnalDataBaru->perusahaan->id,
						'office' => $jurnalDataBaru->office_store_outlet->id,
						'refBukti' => $jurnalDataBaru->ref_bukti,
						'idLama' => $idLama,
						'idPerusahaanLama' => $idPerusahaanLama
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$jurnal->setKeterangan($jurnalDataBaru->keterangan);
                $jurnal->setTanggal($jurnalDataBaru->tanggal);
				$jurnal->setJenis_jurnal($jurnalDataBaru->jenis_jurnal);
				$jurnal->setOffice_store_outlet($jurnalDataBaru->office_store_outlet->id);
				$jurnal->setRef_bukti($jurnalDataBaru->ref_bukti);
				$result = $jurnal->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update jurnal', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Delete an existing jurnal
	 *
	 * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisJurnalLama
	 */
	public function deleteJurnal($idLama, $idPerusahaanLama)
	{
		try {
			$jurnal = Jurnal::findFirst(
				[
					'conditions' => 'id = :id: AND perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $idLama,						
						'perusahaan' => $idPerusahaanLama
					]
				]
			);

			if($jurnal == null) {
				throw new ServiceException('Jurnal not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $jurnal->delete()) {
				throw new ServiceException('Unable to delete jurnal', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns Jurnal list
	 *
	 * @return array
	 */
    public function getJurnalList()
    {
        try {
			$daftarJurnal = Jurnal::find(
				[
					'conditions' => '',
					'bind'       => []
				]
			);

			if (!$daftarJurnal) {
				return [];
			}

			// $i = 0;
			// $hasil = array();
            // foreach ($daftarJurnal as $Jurnal) {
			// 	$detail_office = $Jurnal->getRelated('detail_office_store_outlet');				
            //     $detail_office->setPropinsi($detail_office->getRelated('detail_propinsi'));
			// 	$detail_office->setKabupaten($detail_office->getRelated('detail_kabupaten'));
			// 	$detail_office->setKecamatan($detail_office->getRelated('detail_kecamatan'));
			// 	$detail_office->setDesa($detail_office->getRelated('detail_desa'));

            //     $perusahaan = $detail_office->getRelated('detail_perusahaan');
            //     $perusahaan->setPropinsi($perusahaan->getRelated('detail_propinsi'));
			// 	$perusahaan->setKabupaten($perusahaan->getRelated('detail_kabupaten'));
			// 	$perusahaan->setKecamatan($perusahaan->getRelated('detail_kecamatan'));
			// 	$perusahaan->setDesa($perusahaan->getRelated('detail_desa'));

            //     $detail_office->setPerusahaan($perusahaan);

			// 	$hak_akses = $Jurnal->getRelated('detail_hak_akses');
			// 	$modul = $hak_akses->getRelated('detail_modul');
			// 	$hak_akses->setModul($modul);

			// 	$Jurnal->setPerusahaan($perusahaan);
			// 	$Jurnal->setOffice_store_outlet($detail_office);
			// 	$Jurnal->setHak_akses($hak_akses);
			// 	$Jurnal->setPass(null);

			// 	$hasil[$i] = $Jurnal;
			// 	$i++;
            // }

			return $daftarJurnal->toArray(); 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}