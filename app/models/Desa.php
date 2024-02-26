<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

class Desa extends Model
{

    protected $id; 
    protected $nama; 
    protected $kecamatan;    

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("desa");
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
}