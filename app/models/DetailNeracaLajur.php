<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class DetailNeracaLajur extends Model
{
    protected $id; 
    protected $perusahaan;
    protected $neraca_lajur;
    protected $akun;
    protected $debet_kredit_neraca_saldo;
    protected $nilai_neraca_saldo;
    protected $debet_kredit_jurnal_penyesuaian;
    protected $nilai_jurnal_penyesuaian;
    protected $debet_kredit_laba_rugi;
    protected $nilai_laba_rugi;
    protected $debet_kredit_neraca;
    protected $nilai_neraca;
    protected $tanggal_insert; 

    public function initialize()
    {
        $this->setSchema("laporan");
        $this->setSource("tbl_detail_neraca_lajur");
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
     * Get the value of neraca_lajur
     */ 
    public function getNeraca_lajur()
    {
        return $this->neraca_lajur;
    }

    /**
     * Set the value of neraca_lajur
     *
     * @return  self
     */ 
    public function setNeraca_lajur($neraca_lajur)
    {
        $this->neraca_lajur = $neraca_lajur;

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
     * Get the value of debet_kredit_neraca_saldo
     */ 
    public function getDebet_kredit_neraca_saldo()
    {
        return $this->debet_kredit_neraca_saldo;
    }

    /**
     * Set the value of debet_kredit_neraca_saldo
     *
     * @return  self
     */ 
    public function setDebet_kredit_neraca_saldo($debet_kredit_neraca_saldo)
    {
        $this->debet_kredit_neraca_saldo = $debet_kredit_neraca_saldo;

        return $this;
    }

    /**
     * Get the value of nilai_neraca_saldo
     */ 
    public function getNilai_neraca_saldo()
    {
        return $this->nilai_neraca_saldo;
    }

    /**
     * Set the value of nilai_neraca_saldo
     *
     * @return  self
     */ 
    public function setNilai_neraca_saldo($nilai_neraca_saldo)
    {
        $this->nilai_neraca_saldo = $nilai_neraca_saldo;

        return $this;
    }

    /**
     * Get the value of debet_kredit_jurnal_penyesuaian
     */ 
    public function getDebet_kredit_jurnal_penyesuaian()
    {
        return $this->debet_kredit_jurnal_penyesuaian;
    }

    /**
     * Set the value of debet_kredit_jurnal_penyesuaian
     *
     * @return  self
     */ 
    public function setDebet_kredit_jurnal_penyesuaian($debet_kredit_jurnal_penyesuaian)
    {
        $this->debet_kredit_jurnal_penyesuaian = $debet_kredit_jurnal_penyesuaian;

        return $this;
    }

    /**
     * Get the value of nilai_jurnal_penyesuaian
     */ 
    public function getNilai_jurnal_penyesuaian()
    {
        return $this->nilai_jurnal_penyesuaian;
    }

    /**
     * Set the value of nilai_jurnal_penyesuaian
     *
     * @return  self
     */ 
    public function setNilai_jurnal_penyesuaian($nilai_jurnal_penyesuaian)
    {
        $this->nilai_jurnal_penyesuaian = $nilai_jurnal_penyesuaian;

        return $this;
    }

    /**
     * Get the value of debet_kredit_laba_rugi
     */ 
    public function getDebet_kredit_laba_rugi()
    {
        return $this->debet_kredit_laba_rugi;
    }

    /**
     * Set the value of debet_kredit_laba_rugi
     *
     * @return  self
     */ 
    public function setDebet_kredit_laba_rugi($debet_kredit_laba_rugi)
    {
        $this->debet_kredit_laba_rugi = $debet_kredit_laba_rugi;

        return $this;
    }

    /**
     * Get the value of nilai_laba_rugi
     */ 
    public function getNilai_laba_rugi()
    {
        return $this->nilai_laba_rugi;
    }

    /**
     * Set the value of nilai_laba_rugi
     *
     * @return  self
     */ 
    public function setNilai_laba_rugi($nilai_laba_rugi)
    {
        $this->nilai_laba_rugi = $nilai_laba_rugi;

        return $this;
    }

    /**
     * Get the value of debet_kredit_neraca
     */ 
    public function getDebet_kredit_neraca()
    {
        return $this->debet_kredit_neraca;
    }

    /**
     * Set the value of debet_kredit_neraca
     *
     * @return  self
     */ 
    public function setDebet_kredit_neraca($debet_kredit_neraca)
    {
        $this->debet_kredit_neraca = $debet_kredit_neraca;

        return $this;
    }

    /**
     * Get the value of nilai_neraca
     */ 
    public function getNilai_neraca()
    {
        return $this->nilai_neraca;
    }

    /**
     * Set the value of nilai_neraca
     *
     * @return  self
     */ 
    public function setNilai_neraca($nilai_neraca)
    {
        $this->nilai_neraca = $nilai_neraca;

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