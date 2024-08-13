<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class TransaksiController extends Controller
{
    /**
     * Returns void
     * 
     * catatan:
     * Traksaksi pembelian barang dagangan secara kredit melibatkan
     * akun pembelian(+) dan akun utang dagang(+) yang dicatat kedalam 
     * jurnal khusus pembelian. 
     *
     * @param string $idPerusahaan
     * @param string $priodeAkuntansi
     * @return array
     */
    public function pembelianBarangDaganganKredit() {

    }
}