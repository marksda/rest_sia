<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use MyApp\Models\Perusahaan;
use MyApp\Models\KelompokAkun;

class Akun extends Model
{
    protected $id; 
    protected $perusahaan;
    protected $header;
    protected $level;
    protected $nama;
    protected $kode;
    protected $kelompok_akun;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_akun");
        $this->hasOne(
            'perusahaan',
            Perusahaan::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_perusahaan'
            ]
        );
        $this->hasOne(
            'kelompok_akun',
            KelompokAkun::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_kelompok_akun'
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
     * Get the value of header
     */ 
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set the value of header
     *
     * @return  self
     */ 
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get the value of level
     */ 
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set the value of level
     *
     * @return  self
     */ 
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get the value of nama
     */ 
    public function getNama()
    {
        return $this->nama;
    }

    /**
     * Set the value of nama
     *
     * @return  self
     */ 
    public function setNama($nama)
    {
        $this->nama = $nama;

        return $this;
    }

    /**
     * Get the value of kode
     */ 
    public function getKode()
    {
        return $this->kode;
    }

    /**
     * Set the value of kode
     *
     * @return  self
     */ 
    public function setKode($kode)
    {
        $this->kode = $kode;

        return $this;
    }

    /**
     * Get the value of jenis_akun
     */ 
    public function getKelompok_akun()
    {
        return $this->kelompok_akun;
    }

    /**
     * Set the value of jenis_akun
     *
     * @return  self
     */ 
    public function setKelompok_akun($kelompok_akun)
    {
        $this->kelompok_akun = $kelompok_akun;

        return $this;
    }
}