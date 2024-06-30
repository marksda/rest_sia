<?php

namespace MyApp\Services;

use MyApp\Models\NeracaLajur;
use MyApp\Models\DetailNeracaLajur;
use Phalcon\Encryption\Security\Random;

class NeracaLajurService extends AbstractService
{

	/**
	 * Creating a new NeracaLajur
	 *
	 * @param stdClass $perusahaan
	 * @param string $priode
	 * @param associative array $dataNeracaSaldo
	 * @param associative array $dataJurnalPenyesuaian
	 */
    public function createNeracaLajur($perusahaan, $priode, $dataNeracaSaldo, $dataJurnalPenyesuaian)
    {
		try {
			$neracaLajur = NeracaLajur::findFirst(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $priode,						
						'idPerusahaan' => $perusahaan->id,
					]
				]
			); // menggunakan query model
			
			if(!$neracaLajur) {	//neraca lajur belum ada
				$neracaLajur   = new NeracaLajur();
				$random = new Random();
				$idNeracaLajur = $random->base58(12);

				
				$dataNeracaLajur = [];		//data untuk execute raw sql
				$dataAkunNeracaLajur = [];	//data komputasi lokal table neraca lajur
				//1.insert header neraca lajur
				$neracaLajurSQL = "INSERT INTO laporan.tbl_neraca_lajur (id,perusahaan,tanggal,tanggal_insert) VALUES (?,?,?,?);";
				$dataNeracaLajur[] = $idNeracaLajur;
				$dataNeracaLajur[] = $perusahaan->id;
				$dataNeracaLajur[] = $priode;
				$dataNeracaLajur[] = \time();
				
				//2. insert detail neraca lajur				
				// 2.1. insert data neraca saldo
				$idDetailNeracaLajur = null;
				foreach ($dataNeracaSaldo->detail as $detailNeracaSaldo) {
					$neracaLajurSQL = $neracaLajurSQL . $detailNeracaSaldo['debet_kredit'] == true ? "INSERT INTO laporan.tbl_detail_neraca_lajur (id, perusahaan, neraca_lajur, akun, nilai_debet_neraca_sado, tanggal_insert) VALUES (?,?,?,?,?,?);" : "INSERT INTO laporan.tbl_detail_neraca_lajur (id, perusahaan, neraca_lajur, akun, nilai_kredit_neraca_sado, tanggal_insert) VALUES (?,?,?,?,?,?);";

					$idDetailNeracaLajur = $random->base58(12);	
					$dataNeracaLajur[] = $idDetailNeracaLajur;					
					$dataNeracaLajur[] = $perusahaan->id;
					$dataNeracaLajur[] = $idNeracaLajur;
					$dataNeracaLajur[] = $detailNeracaSaldo['akun'];
					$dataNeracaLajur[] = $detailNeracaSaldo['nilai'];
					$dataNeracaLajur[] = \time();

					$dataAkunNeracaLajur[] = array(
						'idDetailNeracaLajur' => $idDetailNeracaLajur,
						'idAkun' => $detailNeracaSaldo['akun'],
						'nilaiNeracaSaldo' => $detailNeracaSaldo['debet_kredit'] == true ? $detailNeracaSaldo['nilai'] : ($detailNeracaSaldo['nilai'] * -1),
						'nilaiJurnalPenyesuaian' => 0
					);
				}
				
				// 2.2. insert data jurnal penyesuaian dan data
				foreach ($dataJurnalPenyesuaian->detail as $detailJurnalPenyesuaian) {
					$isAkunExis = false;
					$nilaiNeracaSaldo = 0;
					$i = 0;
					foreach ($dataAkunNeracaLajur as $akunNeracaLajur) {
						if($akunNeracaLajur['idAkun'] == $detailJurnalPenyesuaian['akun']) {
							$isAkunExis = true;
							$idDetailNeracaLajur = $akunNeracaLajur['idDetailNeracaLajur'];
							$i++;
							break;
						}
					}

					if($isAkunExis) {	//akun sudah ada pada neraca lajur
						$neracaLajurSQL = $neracaLajurSQL . $detailJurnalPenyesuaian['debet_kredit'] == true ? "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_debet_jurnal_penyesuaian = ? WHERE id = ? AND perusahaan = ?;" : "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_kredit_jurnal_penyesuaian = ? WHERE id = ? AND perusahaan = ?;";

						$dataNeracaLajur[] = $detailJurnalPenyesuaian['nilai'];
						$dataNeracaLajur[] = $idDetailNeracaLajur;
						$dataNeracaLajur[] = $perusahaan->id;
						$dataAkunNeracaLajur[$i]['nilaiJurnalPenyesuaian'] = $dataAkunNeracaLajur[$i]['nilaiJurnalPenyesuaian'] + $detailJurnalPenyesuaian['debet_kredit'] == true ? $detailJurnalPenyesuaian['nilai'] : ($detailJurnalPenyesuaian['nilai'] * -1);

					}
					else {	//akun belum ada pafa neraca lajur
						$neracaLajurSQL = $neracaLajurSQL . $detailJurnalPenyesuaian['debet_kredit'] == true ?
						"INSERT INTO laporan.tbl_detail_neraca_lajur (id, perusahaan, neraca_lajur, akun, nilai_debet_jurnal_penyesuaian, tanggal_insert) VALUES (?,?,?,?,?,?);" : "INSERT INTO laporan.tbl_detail_neraca_lajur (id, perusahaan, neraca_lajur, akun, nilai_kredit_jurnal_penyesuaian, tanggal_insert) VALUES (?,?,?,?,?,?);";

						$idDetailNeracaLajur = $random->base58(12);
						$dataNeracaLajur[] = $idDetailNeracaLajur;
						$dataNeracaLajur[] = $perusahaan->id;
						$dataNeracaLajur[] = $idNeracaLajur;
						$dataNeracaLajur[] = $detailJurnalPenyesuaian['akun'];
						$dataNeracaLajur[] = $detailJurnalPenyesuaian['nilai'];
						$dataNeracaLajur[] = \time();

						$dataAkunNeracaLajur[] = array(
							'idDetailNeracaLajur' => $idDetailNeracaLajur,
							'idAkun' => $detailJurnalPenyesuaian['akun'],
							'nilaiNeracaSaldo' => null,
							'nilaiJurnalPenyesuaian' => $detailJurnalPenyesuaian['debet_kredit'] == true ? $detailJurnalPenyesuaian['nilai'] : ($detailJurnalPenyesuaian['nilai'] * -1)
						);
					}
				}

				// 2.3. Insert data neraca saldo disesuaikan
				foreach ($dataAkunNeracaLajur as $akunNeracaLajur) {		
					$nilaiNeracaSaldodisesuaikan = 	$akunNeracaLajur['nilaiNeracaSaldo'] + $akunNeracaLajur['nilaiJurnalPenyesuaian'];			
					$neracaLajurSQL = $neracaLajurSQL . $nilaiNeracaSaldodisesuaikan < 0 ? "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_kredit_neraca_saldo_disesuaikan = ? WHERE id = ? AND perusahaan = ?;" : "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_debet_neraca_saldo_disesuaikan = ? WHERE id = ? AND perusahaan = ?;";

					$dataNeracaLajur[] = $nilaiNeracaSaldodisesuaikan < 0 ? ($nilaiNeracaSaldodisesuaikan * -1) : $nilaiNeracaSaldodisesuaikan;
					$dataNeracaLajur[] = $akunNeracaLajur['idDetailNeracaLajur'];
					$dataNeracaLajur[] = $perusahaan->id;

				}
				

				// eksekusi raw sql untuk tahap 1, 2.1, 2.2, dan 2.3
				$this->db->begin();
				$success = $this->db->execute($neracaLajurSQL, $dataNeracaLajur);			

				if(!$success) {
					$this->db->rollback();
					throw new ServiceException('Unable to create neraca lajur, gagal insert', self::ERROR_UNABLE_CREATE_ITEM);
				}

				$this->db->commit();

				unset($dataNeracaLajur);
				unset($dataAkunNeracaLajur);
				$dataNeracaLajur = [];
				$neracaLajurSQL = "";

				// 2.4. Insert data laba rugi
				$akunNominalNeracaLajur = "SELECT id, nilai_debet_neraca_saldo_disesuaikan, nilai_kredit_neraca_saldo_disesuaikan FROM laporan.tbl_detail_neraca_lajur WHERE jenis_akun = ? AND neraca_lajur = ? AND perusahaan = ?";

				$result = $this->db->query(
					$akunNominalNeracaLajur,
					[
						1 => '2',
						2 => $idNeracaLajur,
						3 => $perusahaan->id
					]
				);
				
				while ($item = $result->fetch()) {
					if($item['nilai_debet_neraca_saldo_disesuaikan'] != null) {
						$neracaLajurSQL = $neracaLajurSQL .  "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_debet_laba_rugi = ? WHERE id = ? AND perusahaan = ?;";
						
						$dataNeracaLajur[] = $item['nilai_debet_neraca_saldo_disesuaikan'];
						$dataNeracaLajur[] = $item['id'];
						$dataNeracaLajur[] = $perusahaan->id;
					}
					else {
						$neracaLajurSQL = $neracaLajurSQL .  "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_kredit_laba_rugi = ? WHERE id = ? AND perusahaan = ?;";
						
						$dataNeracaLajur[] = $item['nilai_kredit_neraca_saldo_disesuaikan'];
						$dataNeracaLajur[] = $item['id'];
						$dataNeracaLajur[] = $perusahaan->id;
					}					
				}				 

				// 2.5. insert data neraca
				$akunRiilNeracaLajur = "SELECT id, nilai_debet_neraca_saldo_disesuaikan, nilai_kredit_neraca_saldo_disesuaikan FROM laporan.tbl_detail_neraca_lajur WHERE jenis_akun = ? AND neraca_lajur = ? AND perusahaan = ?";

				$result = $connection->query(
					$akunRiilNeracaLajur,
					[
						1 => '1',
						2 => $idNeracaLajur,
						3 => $perusahaan->id
					]
				);
				
				while ($item = $result->fetch()) {
					if($item['nilai_debet_neraca_saldo_disesuaikan'] != null) {
						$neracaLajurSQL = $neracaLajurSQL .  "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_debet_neraca = ? WHERE id = ? AND perusahaan = ?;";
						
						$dataNeracaLajur[] = $item['nilai_debet_neraca_saldo_disesuaikan'];
						$dataNeracaLajur[] = $item['id'];
						$dataNeracaLajur[] = $perusahaan->id;
					}
					else {
						$neracaLajurSQL = $neracaLajurSQL .  "UPDATE laporan.tbl_detail_neraca_lajur SET nilai_kredit_neraca = ? WHERE id = ? AND perusahaan = ?;";
						
						$dataNeracaLajur[] = $item['nilai_kredit_neraca_saldo_disesuaikan'];
						$dataNeracaLajur[] = $item['id'];
						$dataNeracaLajur[] = $perusahaan->id;
					}
					
				}	

				$this->db->begin();
				$success = $this->db->execute($neracaLajurSQL, $dataNeracaLajur);			

				if(!$success) {
					$this->db->rollback();

					$deleteNeracaLajur = "DELETE laporan.tbl_detail_neraca_lajur WHERE id = ? AND perusahaan = ?";

					$this->db->execute->query(
						$deleteNeracaLajur,
						[
							1 => $idNeracaLajur,
							2 => $perusahaan->id
						]
					);

					throw new ServiceException('Unable to create neraca lajur, gagal insert kolom laba rugi atau kolom neraca', self::ERROR_UNABLE_CREATE_ITEM);
				}
				$this->db->commit();
			}
			else {	//neraca lajur sudah ada
				$this->db->rollback();
				throw new ServiceException('Unable to create neraca lajur, neraca lajur periode ini sudah ada', self::ERROR_UNABLE_CREATE_ITEM);
			}	
					
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
	 * @param string $idNeracaLajut
	 * @param stdClass $perusahaan
	 */
	public function deleteNeracaLajur($idNeracaLajur, $perusahaan)
	{
		try {
			$dataNeracaLajur = [];	
			$deleteNeracaLajurSQL = "DELETE laporan.tbl_neraca_lajur WHERE id = ? AND perusahaan = ?;";
			$dataNeracaLajur[] = $idNeracaLajur;		
			$dataNeracaLajur[] = $perusahaan->id;	

			$deleteNeracaLajurSQL = $deleteNeracaLajurSQL . "DELETE laporan.tbl_detail_neraca_lajur WHERE neraca_lajur = ? AND perusahaan = ?;";	
			$dataNeracaLajur[] = $idNeracaLajur;		
			$dataNeracaLajur[] = $perusahaan->id;
			

			$this->db->begin();

			$success = $this->db->execute($deleteNeracaLajurSQL, $dataNeracaLajur);	

			if(!$success) {
				$this->db->rollback();
				throw new ServiceException('Unable delete neraca lajur', self::ERROR_UNABLE_DELETE_ITEM);
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
	 * @param string $periode
	 * @param stdClass $perusahaan
	 * @return array
	 */
    public function getNeracaLajurList($periode, $perusahaan)
    {
        try {
			$daftarNeracaLajur = NeracaLajur::find(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $priode,						
						'idPerusahaan' => $perusahaan->id,
					],
					'order' => 'tanggal_insert ASC'
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