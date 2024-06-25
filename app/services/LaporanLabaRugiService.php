<?php

namespace MyApp\Services;

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
	 * metode ikhtiat laba rugi
	 *
	 * @param stdClass $perusahaan
	 * @param string $priode
	 * @param associative array $dataNeracaSaldo
	 * @param associative array $dataJurnalPenyesuaian
	 */
    public function generateBaseIkhtiarLabaRugi($periode, $perusahaan) {

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