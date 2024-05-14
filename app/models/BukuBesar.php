<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class Jurnal extends Model
{
    protected $id; 
    protected $perusahaan; 
    protected $tanggal; 
    protected $keterangan;
    protected $akun;
    protected $debet_kredit_nilai; 
    protected $nilai;
    protected $debet_kredit_saldo;
    protected $saldo;
    protected $detail_jurnal;
    protected $neraca; 
    protected $ref;
    protected $tanggal_insert; 

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
     * Get the value of neraca
     */ 
    public function getNeraca()
    {
        return $this->neraca;
    }

    /**
     * Set the value of neraca
     *
     * @return  self
     */ 
    public function setNeraca($neraca)
    {
        $this->neraca = $neraca;

        return $this;
    }

    /**
     * Get the value of ref
     */ 
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set the value of ref
     *
     * @return  self
     */ 
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get the value of detail_jurnal
     */ 
    public function getDetail_jurnal()
    {
        return $this->detail_jurnal;
    }

    /**
     * Set the value of detail_jurnal
     *
     * @return  self
     */ 
    public function setDetail_jurnal($detail_jurnal)
    {
        $this->detail_jurnal = $detail_jurnal;

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