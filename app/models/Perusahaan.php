<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

class Perusahaan extends Model
{

    protected $id; 
    protected $nama; 
    protected $npwp;
    protected $propinsi;
    protected $kabupaten;
    protected $kecamatan;
    protected $desa;
    protected $detail_alamat;
    protected $telepone;
    protected $email;
    protected $tanggal_registrasi;
    

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("tbl_perusahaan");
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
     * Get the value of npwp
     */ 
    public function getNpwp()
    {
        return $this->npwp;
    }

    /**
     * Set the value of npwp
     *
     * @return  self
     */ 
    public function setNpwp($npwp)
    {
        $this->npwp = $npwp;

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

    /**
     * Get the value of desa
     */ 
    public function getDesa()
    {
        return $this->desa;
    }

    /**
     * Set the value of desa
     *
     * @return  self
     */ 
    public function setDesa($desa)
    {
        $this->desa = $desa;

        return $this;
    }

    /**
     * Get the value of detail_alamat
     */ 
    public function getDetail_alamat()
    {
        return $this->detail_alamat;
    }

    /**
     * Set the value of detail_alamat
     *
     * @return  self
     */ 
    public function setDetail_alamat($detail_alamat)
    {
        $this->detail_alamat = $detail_alamat;

        return $this;
    }

    /**
     * Get the value of telepone
     */ 
    public function getTelepone()
    {
        return $this->telepone;
    }

    /**
     * Set the value of telepone
     *
     * @return  self
     */ 
    public function setTelepone($telepone)
    {
        $this->telepone = $telepone;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of tanggal_registrasi
     */ 
    public function getTanggal_registrasi()
    {
        return $this->tanggal_registrasi;
    }

    /**
     * Set the value of tanggal_registrasi
     *
     * @return  self
     */ 
    public function setTanggal_registrasi($tanggal_registrasi)
    {
        $this->tanggal_registrasi = $tanggal_registrasi;

        return $this;
    }
}