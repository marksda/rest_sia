<?php

namespace MyApp\Services;

use MyApp\Models\Transaksi;
use Phalcon\Encryption\Security\Random;


class TransaksiService extends AbstractService
{

	/**
	 * Creating a new Transaksi
	 *
	 * @param json $TransaksiData
	 */
    public function createTransaksi($TransaksiData)
    {
        try {
            $random = new Random();
            $Transaksi = new Transaksi();
            $result = $Transaksi->setId($random->base58(10))
			               ->setNama($TransaksiData->nama)
			               ->setPass($this->security->hash($TransaksiData->pass))
                           ->setLogin($TransaksiData->login)
						   ->setPerusahaan($TransaksiData->perusahaan->id)
						   ->setOffice_store_outlet($TransaksiData->office_store_outlet->id)
						   ->setHak_akses($TransaksiData->hak_akses->id)
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
	 * Updating Transaksi
	 *
     * @param string $TransaksiIdLama
	 * @param json $TransaksiDataBaru
	 */
	public function updateTransaksi($TransaksiIdLama, $TransaksiDataBaru)
	{
		try {

            $Transaksi = Transaksi::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $TransaksiIdLama
					]
				]
			);

			if($Transaksi == null) {
				throw new ServiceException('Unable to update Transaksi', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($TransaksiIdLama != $TransaksiDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_Transaksis
				SET 
					id = :idBaru, 
					nama = :nama,
                    pass = :password,
					login = : TransaksiName,
					perusahaan = :perusahaan,
					office_store_outlet = :office,
					hak_akses = : hakAkses
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $TransaksiDataBaru->id,
						'nama' => $TransaksiDataBaru->nama,
                        'password' => $this->security->hash($TransaksiDataBaru->pass),
						'TransaksiName' => $TransaksiDataBaru->nama,
						'perusahaan' => $TransaksiDataBaru->perusahaan->id,
						'office' => $TransaksiDataBaru->office_store_outlet->id,
						'hakAkses' => $TransaksiDataBaru->hak_akses->id
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update Transaksi', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$Transaksi->setNama($TransaksiDataBaru->nama);
                $Transaksi->setPass($this->security->hash($TransaksiDataBaru->pass));
				$Transaksi->setLogin($TransaksiDataBaru->login);
				$Transaksi->setPerusahaan($TransaksiDataBaru->perusahaan->id);
				$Transaksi->setOffice_store_outlet($TransaksiDataBaru->office_store_outlet->id);
				$Transaksi->setHak_akses($TransaksiDataBaru->hak_akses->id);
				$result = $Transaksi->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update Transaksi', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Delete an existing Transaksi
	 *
	 * @param int $TransaksiId
	 */
	public function deleteHakAkses($TransaksiId)
	{
		try {
			$Transaksi = Transaksi::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $TransaksiId
					]
				]
			);

			if($Transaksi == null) {
				throw new ServiceException('Transaksi not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $Transaksi->delete()) {
				throw new ServiceException('Unable to delete Transaksi', self::ERROR_UNABLE_DELETE_ITEM);
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

			$i = 0;
			$hasil = array();
            foreach ($daftarTransaksi as $Transaksi) {
				$detail_office = $Transaksi->getRelated('detail_office_store_outlet');				
                $detail_office->setPropinsi($detail_office->getRelated('detail_propinsi'));
				$detail_office->setKabupaten($detail_office->getRelated('detail_kabupaten'));
				$detail_office->setKecamatan($detail_office->getRelated('detail_kecamatan'));
				$detail_office->setDesa($detail_office->getRelated('detail_desa'));

                $perusahaan = $detail_office->getRelated('detail_perusahaan');
                $perusahaan->setPropinsi($perusahaan->getRelated('detail_propinsi'));
				$perusahaan->setKabupaten($perusahaan->getRelated('detail_kabupaten'));
				$perusahaan->setKecamatan($perusahaan->getRelated('detail_kecamatan'));
				$perusahaan->setDesa($perusahaan->getRelated('detail_desa'));

                $detail_office->setPerusahaan($perusahaan);

				$hak_akses = $Transaksi->getRelated('detail_hak_akses');
				$modul = $hak_akses->getRelated('detail_modul');
				$hak_akses->setModul($modul);

				$Transaksi->setPerusahaan($perusahaan);
				$Transaksi->setOffice_store_outlet($detail_office);
				$Transaksi->setHak_akses($hak_akses);
				$Transaksi->setPass(null);

				$hasil[$i] = $Transaksi;
				$i++;
            }

			return $hasil; 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}