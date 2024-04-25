<?php

namespace MyApp\Services;

use MyApp\Models\Transaksi;
use Phalcon\Encryption\Security\Random;


class TransaksiService extends AbstractService
{

	/**
	 * Creating a new Transaksi
	 *
	 * @param json $transaksiData
	 */
    public function createTransaksi($transaksiData)
    {
        try {
            $random = new Random();
            $transaksi = new Transaksi();
            $result = $transaksi->setId($random->base58(10))
			               ->setNama($transaksiData->nama)
			               ->setKeterangan($transaksiData->keterangan)
                           ->setTanggal($transaksiData->tanggal)
						   ->setJenis_jurnal($transaksiData->jenis_jurnal)
						   ->setPerusahaan($transaksiData->perusahaan->id)
						   ->setOffice_store_outlet($transaksiData->office_store_outlet->id)
						   ->setRef_bukti($transaksiData->ref_bukti)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create Transaksi', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Transaksi already exists', self::ERROR_ALREADY_EXISTS, $e);
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
	 * Updating transaksi
	 *
     * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisJurnalLama
	 * @param json $transaksiDataBaru
	 */
	public function updateTransaksi($idLama, $idPerusahaanLama, $idJenisJurnalLama, $transaksiDataBaru)
	{
		try {
            $transaksi = Transaksi::findFirst(
				[
					'conditions' => 'id = :id: AND ' .
									'jenis_jurnal > :jenisJurnal: AND ' .
									'perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $idLama,
						'jenisJurnal' => $idJenisJurnalLama,						
						'perusahaan' => $idPerusahaanLama
					]
				]
			);

			if($transaksi == null) {
				throw new ServiceException('Unable to update transaksi', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($idLama != $transaksiDataBaru->id && 
						$idJenisJurnalLama != $transaksiDataBaru->jenis_jurnal && $idPerusahaanLama != $transaksiDataBaru->perusahaan) {
				$sql     = "
				UPDATE 
					transaksi.tbl_Transaksi
				SET 
					id = :idBaru, 
					keterangan = :keterangan,
                    tanggal = :tanggal,
					jenis_jurnal = : jenisJurnal,
					perusahaan = :perusahaan,
					office_store_outlet = :office,
					ref_bukti = : refBukti
				WHERE
					id = :idLama AND jenis_jurnal = :idJenisJurnal AND perusahaan = :idPerusahaan
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $transaksiDataBaru->id,
						'keterangan' => $transaksiDataBaru->keterangan,
                        'tanggal' => $transaksiDataBaru->tanggal,
						'jenisJurnal' => $transaksiDataBaru->jenis_jurnal,
						'perusahaan' => $transaksiDataBaru->perusahaan->id,
						'office' => $transaksiDataBaru->office_store_outlet->id,
						'refBukti' => $transaksiDataBaru->ref_bukti
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update transaksi', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$transaksi->setKeterangan($transaksiDataBaru->keterangan);
                $transaksi->setTanggal($transaksiDataBaru->tanggal);
				$transaksi->setJenis_jurnal($transaksiDataBaru->jenis_jurnal);
				$transaksi->setPerusahaan($transaksiDataBaru->perusahaan->id);
				$transaksi->setOffice_store_outlet($transaksiDataBaru->office_store_outlet->id);
				$transaksi->setRef_bukti($transaksiDataBaru->ref_bukti);
				$result = $transaksi->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update transaksi', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Delete an existing transaksi
	 *
	 * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisJurnalLama
	 */
	public function deleteHakAkses($idLama, $idPerusahaanLama, $idJenisJurnalLama)
	{
		try {
			$transaksi = Transaksi::findFirst(
				[
					'conditions' => 'id = :id: AND ' .
									'jenis_jurnal > :jenisJurnal: AND ' .
									'perusahaan = :perusahaan:',
					'bind'       => [
						'id' => $idLama,
						'jenisJurnal' => $idJenisJurnalLama,						
						'perusahaan' => $idPerusahaanLama
					]
				]
			);

			if($transaksi == null) {
				throw new ServiceException('Transaksi not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $transaksi->delete()) {
				throw new ServiceException('Unable to delete transaksi', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Returns Transaksi list
	 *
	 * @return array
	 */
    public function getTransaksiList()
    {
        try {
			$daftarTransaksi = Transaksi::find(
				[
					'conditions' => '',
					'bind'       => []
				]
			);

			if (!$daftarTransaksi) {
				return [];
			}

			// $i = 0;
			// $hasil = array();
            // foreach ($daftarTransaksi as $Transaksi) {
			// 	$detail_office = $Transaksi->getRelated('detail_office_store_outlet');				
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

			// 	$hak_akses = $Transaksi->getRelated('detail_hak_akses');
			// 	$modul = $hak_akses->getRelated('detail_modul');
			// 	$hak_akses->setModul($modul);

			// 	$Transaksi->setPerusahaan($perusahaan);
			// 	$Transaksi->setOffice_store_outlet($detail_office);
			// 	$Transaksi->setHak_akses($hak_akses);
			// 	$Transaksi->setPass(null);

			// 	$hasil[$i] = $Transaksi;
			// 	$i++;
            // }

			return $daftarTransaksi->toArray(); 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}