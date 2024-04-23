<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use MyApp\Models\Modul;

class HakAkses extends Model
{
    protected $id; 
    protected $nama; 
    protected $modul;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_hak_akses");
        $this->hasOne(
            'modul',
            Modul::class,
            'id',
            [
                'reusable' => false,
                'alias'    => 'detail_modul'
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
     * Get the value of modul
     */ 
    public function getModul()
    {
        return $this->modul;
    }

    /**
     * Set the value of modul
     *
     * @return  self
     */ 
    public function setModul($modul)
    {
        $this->modul = $modul;

        return $this;
    }
}