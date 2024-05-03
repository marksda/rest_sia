<?php

namespace MyApp\Services;

// use MyApp\Models\DetailJurnal;


class LaporanKeuanganService extends AbstractService
{

    /**
	 * Creating a new neraca saldo
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createNeracaSaldo($idPerusahaan, $periode) {
        //sumber data berasal dari buku besar
    }

    /**
	 * Creating a new neraca saldo perusahaan dagang
     * Metode Ikhtisar laba rugi
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    private function createNeracaSaldoPerusahaanDagangMetodeLabaRugi($idPerusahaan, $periode) {
        //sumber data berasal dari buku besar
    }

    /**
	 * Creating a new neraca saldo perusahaan jasa
     * Metode Ikhtisar laba rugi
	 *
	 * @param json $detailJurnalData
	 */
    private function createNeracaSaldoPerusahaanJasaMetodeLabaRugi($idPerusahaan) {
        //sumber data berasal dari buku besar
    }

    /**
	 * Creating a new neraca lajur / kertas kerja 
     * Metode Ikhtisar laba rugi
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    private function createNeracaLajurPerusahaanDagang($idPerusahaan, $periode) {
        //sumber data berasal dari neraca saldo dan jurnal penyesuaian
    }

    /**
	 * Creating a new neraca lajur / kertas kerja perusahaan dagang
     * Metode Ikhtisar laba rugi
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    private function createNeracaLajurPerusahaanDagangMetodeLabaRugi($idPerusahaan, $periode) {
        //sumber data berasal dari neraca saldo dan jurnal penyesuaian
    }


}