<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class DetailNeracaSaldo extends Model
{
    protected $id; 
    protected $perusahaan;
    protected $neraca_saldo;
    protected $akun;
    protected $debet_kredit;
    protected $nilai;
    protected $tanggal_insert; 

    public function initialize()
    {
        $this->setSchema("laporan");
        $this->setSource("tbl_detail_neraca_saldo");
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
     * Get the value of neraca_saldo
     */ 
    public function getNeraca_saldo()
    {
        return $this->neraca_saldo;
    }

    /**
     * Set the value of neraca_saldo
     *
     * @return  self
     */ 
    public function setNeraca_saldo($neraca_saldo)
    {
        $this->neraca_saldo = $neraca_saldo;

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

    /**
     * Get the value of tanggal_insert
     */ 
    public function getTanggal_insert()
    {
        return $this->tanggal_insert;
    }

    /**
     * Set the value of tanggal_insert
     *
     * @return  self
     */ 
    public function setTanggal_insert($tanggal_insert)
    {
        $this->tanggal_insert = $tanggal_insert;

        return $this;
    }
}  