<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

class Barang extends Model
{

    /**
     * @Primary
     * @Column(type='string', nullable=false)
     */
    protected $id; 
    protected $nama; 
    protected $harga_satuan;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_barang");
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
     * Get the value of harga_satuan
     */ 
    public function getHarga_satuan()
    {
        return $this->harga_satuan;
    }

    /**
     * Set the value of harga_satuan
     *
     * @return  self
     */ 
    public function setHarga_satuan($harga_satuan)
    {
        $this->harga_satuan = $harga_satuan;

        return $this;
    }
}