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
	 * //@param associative array $dataNeracaSaldo
	 * //@param associative array $dataJurnalPenyesuaian
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
				$labaRugi   = new LabaRugi();
				$random = new Random();
				$idLabaRugi = $random->base58(12);

				
				$dataLabaRugi = [];		//data untuk execute raw sql
				$dataAkunLabaRugi = [];	//data komputasi lokal table laba rugi
				//1.insert header neraca lajur
				$labaRugiSQL = "INSERT INTO laporan.tbl_laba_rugi(id,perusahaan,tanggal,tanggal_insert,metode_pendekatan_akutansi) VALUES (?,?,?,?,?);";
				$dataLabaRugi[] = $idLabaRugi;
				$dataLabaRugi[] = $perusahaan->id;
				$dataLabaRugi[] = $priode;
				$dataLabaRugi[] = time();
				$dataLabaRugi[] = $metodePendekatanAkutansi;

				//2. insert detail laba rugi
				$idDetailLabaRugi = null;
				$penjualanBersih = null;
				$pendapatanJasa = null;
				$labaKotor = null;				
				$totalBeban = null;
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