<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class Jurnal extends Model
{
    protected $jurnal; 
    protected $perusahaan; 
    protected $akun;
    protected $tanggal; 
    protected $keterangan;
    protected $debet_kredit_nilai; 
    protected $nilai;
    protected $debet_kredit_saldo;
    protected $saldo;

    public function initialize()
    {
        $this->setSchema("transaksi");
        $this->setSource('tbl_buku_besar');
    } 
}   