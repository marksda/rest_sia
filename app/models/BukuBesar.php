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
     * Get the value of tanggal
     */ 
    public function getTanggal()
    {
        return $this->tanggal;
    }

    /**
     * Set the value of tanggal
     *
     * @return  self
     */ 
    public function setTanggal($tanggal)
    {
        $this->tanggal = $tanggal;

        return $this;
    }

    /**
     * Get the value of keterangan
     */ 
    public function getKeterangan()
    {
        return $this->keterangan;
    }

    /**
     * Set the value of keterangan
     *
     * @return  self
     */ 
    public function setKeterangan($keterangan)
    {
        $this->keterangan = $keterangan;

        return $this;
    }

    /**
     * Get the value of debet_kredit_nilai
     */ 
    public function getDebet_kredit_nilai()
    {
        return $this->debet_kredit_nilai;
    }

    /**
     * Set the value of debet_kredit_nilai
     *
     * @return  self
     */ 
    public function setDebet_kredit_nilai($debet_kredit_nilai)
    {
        $this->debet_kredit_nilai = $debet_kredit_nilai;

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
     * Get the value of debet_kredit_saldo
     */ 
    public function getDebet_kredit_saldo()
    {
        return $this->debet_kredit_saldo;
    }

    /**
     * Set the value of debet_kredit_saldo
     *
     * @return  self
     */ 
    public function setDebet_kredit_saldo($debet_kredit_saldo)
    {
        $this->debet_kredit_saldo = $debet_kredit_saldo;

        return $this;
    }

    /**
     * Get the value of saldo
     */ 
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set the value of saldo
     *
     * @return  self
     */ 
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }
}   