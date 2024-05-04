<?php

namespace MyApp\Services;

// use MyApp\Models\DetailJurnal;


class LaporanKeuanganService extends AbstractService
{

    /**
	 * Creating a new neraca saldo
     * neraca saldo dibuat dengan cara :
     * memindahkan saldo akhir setiap akun/rekening
     * pada buku besar ke dalam neraca saldo 
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createNeracaSaldo($idPerusahaan, $periode) {
        //sumber data berasal dari rekening/akun buku besar
        createNeracaSaldoMetodeIkhtiarLabaRugi($idPerusahaan, $periode);
    }

    /**
	 * Creating a new neraca saldo metode Ikhtisar laba rugi
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    private function createNeracaSaldoMetodeIkhtiarLabaRugi($idPerusahaan, $periode) {
        //sumber data berasal dari buku besar
        //neraca saldo dibuat dengan cara memindahkan saldo akhir setiap akun pada buku besat ke dalam neraca saldo 
    }

    /**
	 * Creating a new jurnal penyesuaian
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createJurnalPenyesuaian($idPerusahaan, $periode) {
        //sumber data berasal dari neraca saldo dan data penyesuaian pada akhir priode
        createJurnalPenyesuaianPerusahaanDagangIkhtiarLabaRugi($idPerusahaan, $periode);
    }

    /**
	 * Creating a new jurnal penyesuaian
     * MetodeLabaRugi
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    private function createJurnalPenyesuaianMetodeIkhtiarLabaRugi($idPerusahaan, $periode) {
        //sumber data berasal dari neraca saldo dan data penyesuaian pada akhir priode
    }

    /**
	 * Creating a new neraca lajur / kertas kerja metode Ikhtisar laba rugi
     * neraca lajur dibuat dengan cara :
     * 1. memindahkan data sumber 'neraca saldo' ke dalam 'kolom neraca saldo' pada neraca lajur
     * 2. memindahkan data sumber 'jurnal penyesuaian' ke dalam 'kolom jurnal penyesuaian' pada neraca lajur
     *    ada 2 hal yang harus diperhatikan dalam memindahkan jurnal penyesuaian kedalam neraca lajur:
     *    1. apabila nama akun dalam jurnal penyesuaian sudah ada dalam neraca lajur maka cukup menuliskan
     *       saldo akun debet atau kredit pada kolom jurnal penyesuaian dalam neraca lajur.
     *    2. apabila nama akun dalam jurnal penyesuaian belum ada pada neraca lajur maka nama akun tersebut
     *       harus dituliskan pada kolom nama akun dalam neraca lajur baru kemudian mengisi saldo akun debet
     *       atau kredit pada kolom jurnal penyesuaian dalam neraca lajur.
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createNeracaLajur($idPerusahaan, $periode) {
        //sumber data berasal dari neraca saldo dan jurnal penyesuaian
        createNeracaLajurPerusahaanDagangMetodeIkhtiarLabaRugi($idPerusahaan, $periode);
    }

    /**
	 * Creating a new neraca lajur / kertas kerja perusahaan dagang
     * Metode Ikhtisar laba rugi
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    private function createNeracaLajurMetodeIkhtiarLabaRugi($idPerusahaan, $periode) {
        //sumber data berasal dari neraca saldo dan jurnal penyesuaian
    }

    /**
	 * Creating a new laporan laba rugi
     * 
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createLaporanLabaRugi($idPerusahaan, $periode) {
        //sumber data berasal dari neraca lajur kolom neraca saldo, laba rugi dan neraca
        //neraca saldo dibuat dengan cara memindahkan saldo akhir setiap akun pada buku besat ke dalam neraca saldo 
    }

    /**
	 * Creating a new laporan perubahan modal
     * yang fitampilkan pada laporan saldo modal awal, salfo laba atau rugi, 
     * saldo prive bila ada, saldo penambahan modal bila ada, dan saldo modal akhir
     * 
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createLaporanPerubahanModal($idPerusahaan, $periode) {
         //sumber data berasal dari neraca lajur
    }


    /**
	 * Creating a new jurnal penutup
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createJurnalPenutup($idPerusahaan, $periode) {
        //sumber data berasal dari laporan laba rugi
        //neraca saldo dibuat dengan cara memindahkan saldo akhir setiap akun pada buku besat ke dalam neraca saldo 
    }

}