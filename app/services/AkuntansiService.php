<?php

namespace MyApp\Services;

// use MyApp\Models\DetailJurnal;


class AkutansiService extends AbstractService
{

    /**
	 * Creating a new bukti transaksi.
     * untuk menentukan status akun sebagai debet atau kredit 
     * setelah melakukan analisa terhadap bukti transaksi.
     * 
     * sumber data berasal dari bukti transaksi / formulir (faktur, nota, dll)
     * 
     * formulir dibuat dengan cara :
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createBuktiTransaksi($idPerusahaan, $periode) {
        //dfgdf
    }

    /**
	 * Creating a new jurnal.
     * 
     * terdiri atas jurnal umum dan jurnal khusus,
     * setelah melakukan analisa terhadap bukti transaksi 
     * hasilnya akan dimasukkan kedalam jurnal umum atau khusus.
     * 
     * jurnal umum biasanya digunakan oleh perusahan jasa sedangkan 
     * jurnal khusus biasanya digunakan oleh perusahaan dagang.
     * 
     * sumber data / input berasal dari bukti transaksi / formulir (faktur, nota, dll)
     * 
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createJurnal($idPerusahaan, $periode) {
        //asdasd
    }

    /**
	 * Creating a new buku besar.
     * 
     * sumber data / input berasal dari jurnal
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createBukuBesar($idPerusahaan, $periode) {
        //asdasd
    }

    /**
	 * Creating a new neraca saldo.
     * 
     * sumber data /input berasal dari buku besar
     * 
     * neraca saldo dibuat dengan cara :
     * memindahkan saldo akhir setiap akun/rekening
     * pada buku besar ke dalam neraca saldo 
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createNeracaSaldo($idPerusahaan, $periode) {
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
	 * Creating a new jurnal penyesuaian.
     * 
     * sumber data berasal dari neraca saldo dan data penyesuaian pada akhir priode
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createJurnalPenyesuaian($idPerusahaan, $periode) {
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
     * 
     * sumber data / input berasal dari neraca saldo dan jurnal penyesuaian.
     * 
     * neraca lajur dibuat dengan cara :
     * 1. memindahkan data sumber 'neraca saldo' ke dalam 'kolom neraca saldo' pada neraca lajur
     * 2. memindahkan data sumber 'jurnal penyesuaian' ke dalam 'kolom jurnal penyesuaian' pada neraca lajur
     *    ada 2 hal yang harus diperhatikan dalam memindahkan jurnal penyesuaian kedalam neraca lajur:
     *    1. apabila nama akun pada jurnal penyesuaian sudah ada dalam 'kolom nama akun' pada neraca lajur maka cukup menuliskan
     *       saldo debet atau kredit dalam 'kolom jurnal penyesuaian' pada neraca lajur.
     *    2. apabila nama akun pada jurnal penyesuaian belum ada dalam 'kolom nama akun' pada neraca lajur maka nama akun tersebut
     *       harus dituliskan dalam 'kolom nama akun' pada neraca lajur baru kemudian mengisi saldo debet
     *       atau kredit dalam 'kolom jurnal penyesuaian' pada neraca lajur.
     * 3. mengisi kolom 'neraca saldo disesuaikan' dalam neraca lajur. ada 4 kondisi didalam mengisi kolom ini:
     *    1. apabila akun hanya memiliki saldo pada 'kolom neraca saldo' maka kolom ini diisi dengan saldo yang
     *       ada pada 'kolom neraca saldo'.
     *    2. apabila akun memiliki saldo pada 'kolom neraca saldo' dan 'kolom jurnal penyesuaian' serta debet/kreditnya berlainan maka kolom ini diisi
     *       dengan selisih saldo dari kedua kolom tersebut.
     *    3. apabila akun memiliki saldo pada 'kolom neraca saldo' dan 'kolom jurnal penyesuaian' serta debet/kreditnya sama maka kolom ini diisi
     *       dengan jumlah saldo dari kedua kolom tersebut.
     *    4. apabila akun hanya memiliki saldo pada 'kolom jurnal penyesuaian' maka kolom ini diisi dengan saldo yang
     *       ada pada 'kolom neraca saldo'.
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createNeracaLajur($idPerusahaan, $periode) {
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
    }

    /**
	 * Creating a new laporan laba rugi
     * sumber data berasal dari kertas kerja / neraca lajur
     * 
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
     * yang ditampilkan pada laporan ini adalah saldo modal awal, saldo laba atau rugi, 
     * saldo prive bila ada, saldo penambahan modal bila ada, dan saldo modal akhir
     * 
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createLaporanPerubahanModal($idPerusahaan, $periode) {
         //sumber data berasal dari neraca lajur
    }

    /**
	 * Creating a new laporan neraca
     * 
     * 
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createLaporanNeraca($idPerusahaan, $periode) {
        //sumber data berasal dari neraca lajur
    }

    /**
	 * Creating a new laporan arus kas
     * 
     * 
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createLaporanArusKas($idPerusahaan, $periode) {
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

    /**
	 * Creating a new neraca saldo penutup
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createNeracaSaldoPenutup($idPerusahaan, $periode) {
        //sumber data berasal dari laporan laba rugi
        //neraca saldo dibuat dengan cara memindahkan saldo akhir setiap akun pada buku besat ke dalam neraca saldo 
    }

    /**
	 * Creating a new jurnal pembalik
     * 
     * dibuat pada tahap awal periode akutansi selanjutnya
	 *
	 * @param json $idPerusahaan
     * @param int $periode 
	 */
    public function createJurnalPembalik($idPerusahaan, $periode) {
    }

}