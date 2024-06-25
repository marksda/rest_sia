<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class LabaRugi extends Model
{
    protected $id; 
    protected $perusahaan;
    protected $tanggal;
    protected $tanggal_insert; 
    protected $metode_pendekatan_akutansi;


    public function initialize()
    {
        $this->setSchema("laporan");
        $this->setSource('tbl_laba_rugi');
        $this->hasMany(
            'id',
            DetailLabarugi::class,
            'neraca_saldo',
            [
                'reusable' => false,
                'alias'    => 'detail_neraca_saldo'
            ]
        );
        $this->hasOne(
            'metode_pendekatan_akutansi',
            MetodePendekatanAkutansi::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'metode_pendekatan_akutansi'
            ]
        );
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

    /**
     * Get the value of metode_pendekatan_akutansi
     */ 
    public function getMetode_pendekatan_akutansi()
    {
        return $this->metode_pendekatan_akutansi;
    }

    /**
     * Set the value of metode_pendekatan_akutansi
     *
     * @return  self
     */ 
    public function setMetode_pendekatan_akutansi($metode_pendekatan_akutansi)
    {
        $this->metode_pendekatan_akutansi = $metode_pendekatan_akutansi;

        return $this;
    }
}