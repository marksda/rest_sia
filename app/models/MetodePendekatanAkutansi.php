<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class MetodePendekatanAkutansi extends Model
{
    protected $id; 
    protected $keterangan;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource('tbl_metode_pendekatan_akutansi');
    }   

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
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