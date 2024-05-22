<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class NeracaLajur extends Model
{
    protected $id; 
    protected $perusahaan;
    protected $tanggal;
    protected $tanggal_insert; 

    public function initialize()
    {
        $this->setSchema("laporan");
        $this->setSource('tbl_neraca_lajur');
        $this->hasMany(
            'id',
            DetailNeracaLajur::class,
            'neraca_saldo',
            [
                'reusable' => false,
                'alias'    => 'detail_neraca_lajur'
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
}