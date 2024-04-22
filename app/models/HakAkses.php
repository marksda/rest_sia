<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class HakAkses extends Model
{
    protected $id; 
    protected $nama; 

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_hak_akses");
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
}