<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use MyApp\Models\Propinsi;
use MyApp\Models\Kabupaten;
use MyApp\Models\Kecamatan;
use MyApp\Models\Desa;
use MyApp\Models\Perusahaan;


class OfficeStoreOutlet extends Model
{
    protected $id; 
    protected $nama; 
    protected $propinsi;
    protected $kabupaten;
    protected $kecamatan;
    protected $desa;
    protected $detail_alamat;
    protected $perusahaan;
    protected $telepone;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_office_store_outlet");
        $this->hasOne(
            'propinsi',
            Propinsi::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_propinsi'
            ]
        );
        $this->hasOne(
            'kabupaten',
            Kabupaten::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_kabupaten'
            ]
        );
        $this->hasOne(
            'kecamatan',
            Kecamatan::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_kecamatan'
            ]
        );
        $this->hasOne(
            'desa',
            Desa::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_desa'
            ]
        );
        $this->hasOne(
            'perusahaan',
            Perusahaan::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_perusahaan'
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
     * Get the value of propinsi
     */ 
    public function getPropinsi()
    {
        return $this->propinsi;
    }

    /**
     * Set the value of propinsi
     *
     * @return  self
     */ 
    public function setPropinsi($propinsi)
    {
        $this->propinsi = $propinsi;

        return $this;
    }

    /**
     * Get the value of kabupaten
     */ 
    public function getKabupaten()
    {
        return $this->kabupaten;
    }

    /**
     * Set the value of kabupaten
     *
     * @return  self
     */ 
    public function setKabupaten($kabupaten)
    {
        $this->kabupaten = $kabupaten;

        return $this;
    }

    /**
     * Get the value of kecamatan
     */ 
    public function getKecamatan()
    {
        return $this->kecamatan;
    }

    /**
     * Set the value of kecamatan
     *
     * @return  self
     */ 
    public function setKecamatan($kecamatan)
    {
        $this->kecamatan = $kecamatan;

        return $this;
    }

    /**
     * Get the value of desa
     */ 
    public function getDesa()
    {
        return $this->desa;
    }

    /**
     * Set the value of desa
     *
     * @return  self
     */ 
    public function setDesa($desa)
    {
        $this->desa = $desa;

        return $this;
    }

    /**
     * Get the value of detail_alamat
     */ 
    public function getDetail_alamat()
    {
        return $this->detail_alamat;
    }

    /**
     * Set the value of detail_alamat
     *
     * @return  self
     */ 
    public function setDetail_alamat($detail_alamat)
    {
        $this->detail_alamat = $detail_alamat;

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
     * Get the value of telepone
     */ 
    public function getTelepone()
    {
        return $this->telepone;
    }

    /**
     * Set the value of telepone
     *
     * @return  self
     */ 
    public function setTelepone($telepone)
    {
        $this->telepone = $telepone;

        return $this;
    }
}