<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class JenisJurnal extends Model
{
    protected $id; 
    protected $nama; 
    protected $keterangan;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_jenis_jurnal");
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
}