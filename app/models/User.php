<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use MyApp\Models\OfficeStoreOutlet;
use MyApp\Models\HakAkses;

class User extends Model
{
    protected $id; 
    protected $nama; 
    protected $pass;
    protected $login; 
    protected $perusahaan;
    protected $office_store_outlet; 
    protected $hak_akses;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource('users');
        $this->hasOne(
            ['office_store_outlet', 'perusahaan'],
            OfficeStoreOutlet::class,
            ['id', 'perusahaan'],
            [
                'reusable' => true,
                'alias'    => 'detail_office_store_outlet'
            ]
        );
        $this->hasOne(
            'hak_akses', 
            HakAkses::class,
            'id',
            [
                'reusable' => true,
                'alias'    => 'detail_hak_akses'
            ]
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function setNama($nama)
    {
        $this->nama = $nama;

        return $this;
    }
    
    public function getPass()
    {
        return $this->pass;
    }
    
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }
    
    public function getLogin()
    {
        return $this->login;
    }
    
    public function setLogin($login)
    {
        $this->login = $login;

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
     * Get the value of office_store_outlet
     */ 
    public function getOffice_store_outlet()
    {
        return $this->office_store_outlet;
    }

    /**
     * Set the value of office_store_outlet
     *
     * @return  self
     */ 
    public function setOffice_store_outlet($office_store_outlet)
    {
        $this->office_store_outlet = $office_store_outlet;

        return $this;
    }

    /**
     * Get the value of hak_akses
     */ 
    public function getHak_akses()
    {
        return $this->hak_akses;
    }

    /**
     * Set the value of hak_akses
     *
     * @return  self
     */ 
    public function setHak_akses($hak_akses)
    {
        $this->hak_akses = $hak_akses;

        return $this;
    }
}