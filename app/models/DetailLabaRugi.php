<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class DetailLabarugi extends Model
{
    protected $id; 
    protected $perusahaan;
    protected $laba_rugi;
    protected $penjualan_bersih; 
    protected $laba_kotor;
    protected $pendapatan_jasa;
    protected $total_beban;

    public function initialize()
    {
        $this->setSchema("laporan");
        $this->setSource("tbl_detail_laba_rugi");
    }     

    

}