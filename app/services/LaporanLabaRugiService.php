<?php

namespace MyApp\Services;

use MyApp\Models\LabaRugi;
use Phalcon\Encryption\Security\Random;

/**
 * Class laporan laba rugi
 * 
 * sumber data berasal dari kertas kerja / neraca lajur (kolom neraca saldo, laba rugi, dan neraca)
 * 
 */
class LaporanLabaRugiService extends AbstractService
{
	/**
	 * Creating Laporan laba rugi menggunakan 
	 * metode ikhtiar laba rugi
	 *
	 * @param string $priode
	 * @param stdClass $perusahaan
	 * @param stdClass $metodePendekatanAkutansi
	 */
    public function generateBaseIkhtiarLabaRugi($periode, $perusahaan, $metodePendekatanAkutansi) {
		try {
			$labaRugi = LabaRugi::findFirst(
				[
					'conditions' => 'tanggal = :periodeAkuntansi: AND perusahaan = :idPerusahaan:',
					'bind'       => [
						'periodeAkuntansi' => $priode,						
						'idPerusahaan' => $perusahaan->id,
					]
				]
			); // menggunakan query model

			if(!$labaRugi) {	//laporan laba rugi belum ada
				$neracaLajurSQL = "SELECT id FROM laporan.tbl_neraca_lajur WHERE periode = ? AND perusahaan = ?";
				$result = $this->db->query(
					$neracaLajurSQL,
					[
						1 => $periode,
						2 => $perusahaan->id
					]
				);

				if(!$result) {
					throw new ServiceException('Neraca lajur tidak ditemukan', self::ERROR_ITEM_NOT_FOUND);
				}

				$item = $result->fetch();
				$idNeracaLajur = $item['id'];

				$labaRugi   = new LabaRugi();
				$random = new Random();
				$idLabaRugi = $random->base58(12);
				
				$dataLabaRugi = [];		//data untuk execute raw sql
				//$dataAkunLabaRugi = [];	//data komputasi lokal table laba rugi

				//1.insert header laba rugi
				$labaRugiSQL = "INSERT INTO laporan.tbl_laba_rugi(id,perusahaan,tanggal,tanggal_insert,metode_pendekatan_akutansi) VALUES (?,?,?,?,?);";

				$dataLabaRugi[] = $idLabaRugi;
				$dataLabaRugi[] = $perusahaan->id;
				$dataLabaRugi[] = $priode;
				$dataLabaRugi[] = \time();
				$dataLabaRugi[] = $metodePendekatanAkutansi;

				//2. insert detail laba rugi
				$idDetailLabaRugi = null;
				$penjualanBersih = [];
				$pendapatanJasa = [];
				$labaKotor = [];				
				$totalBeban = [];	
				
				$random = new Random();
				$idDetailLabaRugi = $random->base58(12);

				//nilai kolom penjualan_bersih
				$akunPenjualanBersih = "SELECT penjualan, retur_penjualan, potongan_penjualan FROM public.tbl_map_akun_penjualan_bersih WHERE perusahaan = ?";

				$result = $this->db->query(
					$akunPenjualanBersih,
					[
						1 => $perusahaan->id
					]
				);

				if($result) {
					while ($item = $result->fetch()) {
						//nilai penjualan
						$nilaiPenjualan = "SELECT nilai_kredit_laba_rugi as penjualan FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

						$resultNilai = $this->db->query(
							$nilaiPenjualan,
							[
								1 => $item['penjualan'],
								2 => $idNeracaLajur,
								3 => $perusahaan->id
							]
						);
	
						if($resultNilai) {
							$hasil = $resultNilai->fetch();
							$penjualanBersih['penjualan'] = $hasil['penjualan'];
						}

						//nilai retur penjualan
						$nilaiReturPenjualan = "SELECT nilai_debet_laba_rugi as retur_penjualan FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

						$resultNilai = $this->db->query(
							$nilaiReturPenjualan,
							[
								1 => $item['retur_penjualan'],
								2 => $idNeracaLajur,
								3 => $perusahaan->id
							]
						);

						$tot_retur_potongan_penjualan = 0;
	
						if($resultNilai) {
							$hasil = $resultNilai->fetch();
							$penjualanBersih['retur_penjualan'] = $hasil['retur_penjualan'];
							$tot_retur_potongan_penjualan = $hasil['retur_penjualan'];
						}

						//nilai potongan penjualan
						$nilaiPotonganPenjualan = "SELECT nilai_debet_laba_rugi as potongan_penjualan FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

						$resultNilai = $this->db->query(
							$nilaiPotonganPenjualan,
							[
								1 => $item['potongan_penjualan'],
								2 => $idNeracaLajur,
								3 => $perusahaan->id
							]
						);
	
						if($resultNilai) {
							$hasil = $resultNilai->fetch();
							$penjualanBersih['potongan_penjualan'] = $hasil['potongan_penjualan'];	
							$tot_retur_potongan_penjualan += $hasil['potongan_penjualan'];
						}

						if($tot_retur_potongan_penjualan > 0) {
							$penjualanBersih['tot_retur_potongan_penjualan'] = $tot_retur_potongan_penjualan;
						}

						$penjualanBersih['penjualan_bersih'] = $penjualanBersih['penjualan'] - $tot_retur_potongan_penjualan;
					}
				}

				//nilai kolom pendapatan_jasa
				$akunPendapatanJasa = "SELECT pendapatan_jasa FROM public.tbl_map_akun_pendapatan_jasa WHERE perusahaan = ?";

				$result = $this->db->query(
					$akunPendapatanJasa,
					[
						1 => $perusahaan->id
					]
				);

				if($result) {
					while ($item = $result->fetch()) {
						//nilai pendapatan jasa
						$nilaiPendapatanJasa = "SELECT nilai_kredit_laba_rugi as pendapatan_jasa FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

						$resultNilai = $this->db->query(
							$nilaiPendapatanJasa,
							[
								1 => $item['pendapatan_jasa'],
								2 => $idNeracaLajur,
								3 => $perusahaan->id
							]
						);
	
						if($resultNilai) {
							$hasil = $resultNilai->fetch();
							$pendapatanJasa['pendapatan_jasa'] = $hasil['pendapatan_jasa'];
						}
					}
				}

				//nilai kolom laba_kotor
				if($metodePendekatanAkutansi->id == '1') {		//metode ikhtiar laba rugi
					$akunLabaKotor = "SELECT persedian_barang_dagangan, pembelian, retur_pembelian, potongan_pembelian, biaya_angkut_pembelian FROM public.tbl_map_akun_pendapatan_jasa WHERE perusahaan = ?";	
					
					$result = $this->db->query(
						$akunLabaKotor,
						[
							1 => $perusahaan->id
						]
					);

					if($result) {
						while ($item = $result->fetch()) {
							//nilai persedian barang dagang (awal) 
							$npbd_awal = "SELECT nilai_debet_neraca_saldo as npbb_awal FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

							$resultNilai = $this->db->query(
								$npbd_awal,
								[
									1 => $item['persedian_barang_dagangan'],
									2 => $idNeracaLajur,
									3 => $perusahaan->id
								]
							);
		
							if($resultNilai) {
								$hasil = $resultNilai->fetch();
								$labaKotor['persedian_barang_dagangan_awal'] = $hasil['npbb_awal'];
							}

							//pembelian
							$pembelian_bersih = 0.0;
							$nilai_pembelian = "SELECT nilai_debet_laba_rugi as pembelian FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

							$resultNilai = $this->db->query(
								$nilai_pembelian,
								[
									1 => $item['pembelian'],
									2 => $idNeracaLajur,
									3 => $perusahaan->id
								]
							);
		
							if($resultNilai) {
								$hasil = $resultNilai->fetch();
								$labaKotor['pembelian'] = $hasil['pembelian'];
								$pembelian_bersih = $hasil['pembelian'];
							}

							//retur pembelian
							$retur_pembelian = "SELECT nilai_kredit_laba_rugi as retur_pembelian FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

							$resultNilai = $this->db->query(
								$retur_pembelian,
								[
									1 => $item['retur_pembelian'],
									2 => $idNeracaLajur,
									3 => $perusahaan->id
								]
							);
		
							if($resultNilai) {
								$hasil = $resultNilai->fetch();
								$labaKotor['retur_pembelian'] = $hasil['retur_pembelian'];
								$pembelian_bersih = $pembelian_bersih - $hasil['retur_pembelian'];
							}

							//potongan pembelian
							$potongan_pembelian = "SELECT nilai_kredit_laba_rugi as potongan_pembelian FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

							$resultNilai = $this->db->query(
								$potongan_pembelian,
								[
									1 => $item['potongan_pembelian'],
									2 => $idNeracaLajur,
									3 => $perusahaan->id
								]
							);
		
							if($resultNilai) {
								$hasil = $resultNilai->fetch();
								$labaKotor['potongan_pembelian'] = $hasil['potongan_pembelian'];
								$pembelian_bersih = $pembelian_bersih - $hasil['potongan_pembelian'];
							}

							//biaya angkut pembelian
							$biaya_angkut_pembelian = "SELECT nilai_debet_laba_rugi as biaya_angkut_pembelian FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

							$resultNilai = $this->db->query(
								$biaya_angkut_pembelian,
								[
									1 => $item['biaya_angkut_pembelian'],
									2 => $idNeracaLajur,
									3 => $perusahaan->id
								]
							);
		
							if($resultNilai) {
								$hasil = $resultNilai->fetch();
								$labaKotor['biaya_angkut_pembelian'] = $hasil['biaya_angkut_pembelian'];
								$pembelian_bersih = $pembelian_bersih + $hasil['biaya_angkut_pembelian'];
							}

							$labaKotor['pembelian_bersih'] = $pembelian_bersih;
							$labaKotor['btud'] = $labaKotor['persedian_barang_dagangan_awal'] + $labaKotor['pembelian_bersih'];

							//persedian_barang_dagangan_akhir
							$npbd_akhir = "SELECT nilai_debet_neraca as npbd_akhir FROM laporan.tbl_detail_neraca_lajur WHERE akun = ? AND neraca_lajur = ? AND perusahaan = ?";

							$resultNilai = $this->db->query(
								$npbd_akhir,
								[
									1 => $item['persedian_barang_dagangan'],
									2 => $idNeracaLajur,
									3 => $perusahaan->id
								]
							);
		
							if($resultNilai) {
								$hasil = $resultNilai->fetch();
								$labaKotor['persedian_barang_dagangan_akhir'] = $hasil['npbd_akhir'];
							}

							//HPP
							$labaKotor['harga_pokok_penjualan'] = $labaKotor['btud'] - $labaKotor['persedian_barang_dagangan_akhir'];

							//laba kotor
							$labaKotor['laba_kotor'] = $penjualanBersih['penjualan_bersih'] - $labaKotor['harga_pokok_penjualan'];
						}
					}
				}
				else {	// metode HPP
					// dsfsdfsdf
				}

				//nilai kolom total_beban

				//1.insert detail laba rugi
				$labaRugiSQL = $labaRugiSQL . "INSERT INTO laporan.tbl_detail_laba_rugi(id, perusahaan, laba_rugi, penjualan_bersih, pendapatan_jasa, laba_kotor, total_beban) VALUES(?,?,?,?,?,?,?);";

				$dataLabaRugi[] = $idDetailLabaRugi;
				$dataLabaRugi[] = $perusahaan->id;
				$dataLabaRugi[] = $idLabaRugi;
				$dataLabaRugi[] = \count($penjualanBersih) > 0 ? json_encode($penjualanBersih) : null;
				$dataLabaRugi[] = \count($pendapatanJasa) > 0 ? json_encode($pendapatanJasa) : null;
				$dataLabaRugi[] = \count($labaKotor) > 0 ? json_encode($pendapatanJasa) : null;
			}
			else {
				throw new ServiceException('Unable to create laporan laba rugi, laporan laba rugi periode ini sudah ada', self::ERROR_UNABLE_CREATE_ITEM);
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
    }

	/**
	 * Creating laporan laba rugi menggunakan
	 * metode hpp
	 *
	 * @param stdClass $perusahaan
	 * @param string $priode
	 * @param associative array $dataNeracaSaldo
	 * @param associative array $dataJurnalPenyesuaian
	 */
    public function generateBaseHPP($periode, $perusahaan) {

    }
}