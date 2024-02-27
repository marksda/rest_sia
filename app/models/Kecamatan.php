<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

class Kecamatan extends Model
{

    protected $id; 
    protected $nama; 
    protected $kabupaten;    

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_kecamatan");
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
}