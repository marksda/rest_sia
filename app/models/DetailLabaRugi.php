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

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of perusahaan
     */ 
    public function getPerusahaan()
    {
        return $this->perusahaan;
    }

    /**
     * Set the value of perusahaan
     *
     * @return  self
     */ 
    public function setPerusahaan($perusahaan)
    {
        $this->perusahaan = $perusahaan;

        return $this;
    }

    /**
     * Get the value of laba_rugi
     */ 
    public function getLaba_rugi()
    {
        return $this->laba_rugi;
    }

    /**
     * Set the value of laba_rugi
     *
     * @return  self
     */ 
    public function setLaba_rugi($laba_rugi)
    {
        $this->laba_rugi = $laba_rugi;

        return $this;
    }

    /**
     * Get the value of penjualan_bersih
     */ 
    public function getPenjualan_bersih()
    {
        return $this->penjualan_bersih;
    }

    /**
     * Set the value of penjualan_bersih
     *
     * @return  self
     */ 
    public function setPenjualan_bersih($penjualan_bersih)
    {
        $this->penjualan_bersih = $penjualan_bersih;

        return $this;
    }

    /**
     * Get the value of laba_kotor
     */ 
    public function getLaba_kotor()
    {
        return $this->laba_kotor;
    }

    /**
     * Set the value of laba_kotor
     *
     * @return  self
     */ 
    public function setLaba_kotor($laba_kotor)
    {
        $this->laba_kotor = $laba_kotor;

        return $this;
    }

    /**
     * Get the value of pendapatan_jasa
     */ 
    public function getPendapatan_jasa()
    {
        return $this->pendapatan_jasa;
    }

    /**
     * Set the value of pendapatan_jasa
     *
     * @return  self
     */ 
    public function setPendapatan_jasa($pendapatan_jasa)
    {
        $this->pendapatan_jasa = $pendapatan_jasa;

        return $this;
    }

    /**
     * Get the value of total_beban
     */ 
    public function getTotal_beban()
    {
        return $this->total_beban;
    }

    /**
     * Set the value of total_beban
     *
     * @return  self
     */ 
    public function setTotal_beban($total_beban)
    {
        $this->total_beban = $total_beban;

        return $this;
    }
}