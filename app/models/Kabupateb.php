<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

class Kabupaten extends Model
{

    protected $id; 
    protected $nama; 
    protected $propinsi;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("kabupaten");
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
}