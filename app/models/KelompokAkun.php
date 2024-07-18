<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class KelompokAkun extends Model
{
    protected $id; 
    protected $nama; 
    protected $jenis_akun; 

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_kelompok_akun");
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
     * Get the value of jenis_akun
     */ 
    public function getJenis_akun()
    {
        return $this->jenis_akun;
    }

    /**
     * Set the value of jenis_akun
     *
     * @return  self
     */ 
    public function setJenis_akun($jenis_akun)
    {
        $this->jenis_akun = $jenis_akun;

        return $this;
    }
}