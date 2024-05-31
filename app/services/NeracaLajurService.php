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
	 * @param stdClass $dataNeracaSaldo
	 * @param stdClass $dataJurnalPenyesuaian
	 */
    public function createNeracaLajur($perusahaan, $priode, $dataNeracaSaldo, $dataJurnalPenyesuaian)
    {
		try {
			$this->db->begin();
			$neracaLajur = NeracaLajur::findFirst(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $priode,						
						'idPerusahaan' => $dperusahaan->id,
					]
				]
			); // menggunakan query model
			
			if(!$neracaLajur) {	//neraca lajur belum ada
				$neracaLajur   = new NeracaLajur();
				$random = new Random();
				$idNeracaLajur = $random->base58(12);

				//insert header neraca lajur
				$result = $neracaLajur
					->setId($idNeracaLajur)
					->setPerusahaan($perusahaan->id)
					->setTanggal($priode)
					->setTanggal_insert(time())
					->create()
				;  // menggunakan insert model

				//insert detail neraca lajur				
				// 1. insert data neraca saldo
				$idDetailNeracaLajur = null;
				$data = array();
				$dataAkunNeracaLajur = array();
				$count = 0;
				foreach ($dataNeracaSaldo->detail as $detailNeracaSaldo) {
					$idDetailNeracaLajur = $random->base58(12);	
					$data[] = $idDetailNeracaLajur;					
					$data[] = $detailNeracaSaldo->getPerusahaan();
					$data[] = $idNeracaLajur;
					$data[] = $detailNeracaSaldo->getAkun();
					$data[] = $detailNeracaSaldo->getDebet_kredit();
					$data[] = $detailNeracaSaldo->getNilai();
					$data[] = time();					
					$dataAkunNeracaLajur[] = (object) array(
						'idDetailNeracaLajur' => $idDetailNeracaLajur,
						'idAkun' => $detailNeracaSaldo->getAkun()
					);
					$count++;
				}	
				
				$values = str_repeat('?,', 6) . '?';
				$sqlInsertDataNeracaSaldo = "INSERT INTO laporan.tbl_detail_neraca_lajur " .
					"(id, perusahaan, neraca_lajur, akun, debet_kredit_neraca_sado, nilai_neraca_saldo, tanggal_insert) VALUES " .
					str_repeat("($values),", $count - 1) . "($values)"; 

				$stmt = $this->db->prepare($sqlInsertDataNeracaSaldo);
				$success = $stmt->execute($data);  // menggunakan insert raw sql

				if(!$success) {
					$this->db->rollback();
					throw new ServiceException('Unable to create neraca lajur, gagal insert kolom neraca saldo', self::ERROR_UNABLE_CREATE_ITEM);
				}

				// 2. insert data jurnal penyesuaian
				foreach ($dataJurnalPenyesuaian->detail as $detailJurnalPenyesuaian) {
					$isAkunExis = false;
					foreach ($dataAkunNeracaLajur as $akunNeracaLajur) {
						if($akunNeracaLajur->idAkun == $detailJurnalPenyesuaian->getAkun()) {
							$isAkunExis = true;
							$idDetailNeracaLajur = $akunNeracaLajur->idDetailNeracaLajur;
							break;
						}
					}

					if($isAkunExis) {	//akun sudah ada pada neraca lajur
						$updateNeracaLajur = "UPDATE laporan.tbl_detail_neraca_lajur SET debet_kredit_jurnal_penyesuaian = ?, nilai_jurnal_penyesuaian = ? " .
							"WHERE id = ? AND perusahaan = ?";

						$success = $this->db->execute(
							$updateNeracaLajur, 
							[
								1 => $detailJurnalPenyesuaian->getDebet_kredit(),
								2 => $detailJurnalPenyesuaian->getNilai(),
								3 => $idDetailNeracaLajur,
								4 => $detailJurnalPenyesuaian->getPerusahaan()
							]
						);	// menggunakan raw sql

						if(!$success) {
							$this->db->rollback();
							throw new ServiceException('Unable to create neraca lajur, gagal insert kolom saldo penyesuaian', self::ERROR_UNABLE_CREATE_ITEM);
						}
					}
					else {	//akun belum ada pafa neraca lajur
						$sqlInsertDataJurnalPenyesuaian = "INSERT INTO laporan.tbl_detail_neraca_lajur " .
							"(id, perusahaan, neraca_lajur, akun, debet_kredit_jurnal_penyesuaian, " .
							"nilai_jurnal_penyesuaian, tanggal_insert) VALUES (?, ?, ?, ?, ?, ?, ?)";
						
						$success = $this->db->execute(
							$sqlInsertDataJurnalPenyesuaian, 
							[
								1 => $random->base58(12),
								2 => $detailJurnalPenyesuaian->getPerusahaan(),
								3 => $idNeracaLajur,
								4 => $detailJurnalPenyesuaian->getAkun(),
								5 => $detailJurnalPenyesuaian->getDebet_kredit(),
								6 => $detailJurnalPenyesuaian->getNilai(),
								7 => time()
							]
						);	// menggunakan raw sql

						if(!$success) {
							$this->db->rollback();
							throw new ServiceException('Unable to create neraca lajur, gagal insert kolom neraca saldo', self::ERROR_UNABLE_CREATE_ITEM);
						}
					}
				}

				// 3. Insert neraca saldo disesuaikan
				$daftarDetailNeracaLajur = DetailNeracaLajur::query()
					->where('neraca_lajur = :id_neraca_lajur:')
					->andWhere('perusahaan = :id_perusahaan:')
					->bind(
						[
							'id_neraca_lajur' => $idNeracaLajur,
							'id_perusahaan'  => $perusahaan->id,
						]
					)
					->orderBy('akun asc')
					->execute()
				; // menggunakan query model

				$neracaSaldoDiseseuaikan = array();
				$i = 0;
				foreach ($daftarItemDetailNeracaLajur as $detailItemNeracaLajur) {						
					$nilaiNeracaSaldo = $detailItemNeracaLajur->getDebet_kredit_neraca_saldo == true ? 
							$detailItemNeracaLajur-getNilai_neraca_saldo() : $detailItemNeracaLajur-getNilai_neraca_saldo() * -1;

					$nilaiJurnalPenyesuaian = $detailItemNeracaLajur->getDebet_kredit_jurnal_penyesuaian == true ? 
											$detailItemNeracaLajur-getNilai_jurnal_penyesuaian() : $detailItemNeracaLajur-getNilai_jurnal_penyesuaian() * -1;
					
					$nilaiNeracaSaldoDisesuaikan = $nilaiNeracaSaldo + $nilaiJurnalPenyesuaian;

					$debetKreditNeracaSaldoDisesuaikan  = false;

					if($nilaiNeracaSaldoDisesuaikan > 0) {
						$debetKreditNeracaSaldoDisesuaikan = true;
					}
					else {
						$nilaiNeracaSaldoDisesuaikan = $nilaiNeracaSaldoDisesuaikan * -1;
					}

					// $neracaSaldoDiseseuaikan[$i] = array(
					// 	"idAkun" => $detailItemNeracaLajur->getAkun(),
					// 	""
					// 	"nilai" => 
					// );
				}
			}
			else {	//neraca lajur sudah ada
				$this->db->rollback();
				throw new ServiceException('Unable to create neraca lajur, neraca lajur periode ini sudah ada', self::ERROR_UNABLE_CREATE_ITEM);
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