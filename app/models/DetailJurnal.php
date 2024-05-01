<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class DetailJurnal extends Model
{
    protected $jurnal; 
    protected $perusahaan; 
    protected $akun;
    protected $debet_kredit; 
    protected $nilai;

    public function initialize()
    {
        $this->setSchema("transaksi");
        $this->setSource("tbl_detail_jurnal");
    } 

    /**
     * Get the value of jurnal
     */ 
    public function getJurnal()
    {
        return $this->jurnal;
    }

    /**
     * Set the value of jurnal
     *
     * @return  self
     */ 
    public function setJurnal($jurnal)
    {
        $this->jurnal = $jurnal;

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
     * Get the value of akun
     */ 
    public function getAkun()
    {
        return $this->akun;
    }

    /**
     * Set the value of akun
     *
     * @return  self
     */ 
    public function setAkun($akun)
    {
        $this->akun = $akun;

        return $this;
    }

    /**
     * Get the value of debet_kredit
     */ 
    public function getDebet_kredit()
    {
        return $this->debet_kredit;
    }

    /**
     * Set the value of debet_kredit
     *
     * @return  self
     */ 
    public function setDebet_kredit($debet_kredit)
    {
        $this->debet_kredit = $debet_kredit;

        return $this;
    }

    /**
     * Get the value of nilai
     */ 
    public function getNilai()
    {
        return $this->nilai;
    }

    /**
     * Set the value of nilai
     *
     * @return  self
     */ 
    public function setNilai($nilai)
    {
        $this->nilai = $nilai;

        return $this;
    }
    
}