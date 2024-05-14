<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;

class Jurnal extends Model
{
    protected $id; 
    protected $keterangan; 
    protected $tanggal;
    protected $jenis_jurnal; 
    protected $perusahaan;
    protected $office_store_outlet; 
    protected $ref_bukti;
    protected $tanggal_insert; 

    public function initialize()
    {
        $this->setSchema("transaksi");
        $this->setSource('tbl_jurnal');
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

    /**
     * Get the value of tanggal
     */ 
    public function getTanggal()
    {
        return $this->tanggal;
    }

    /**
     * Set the value of tanggal
     *
     * @return  self
     */ 
    public function setTanggal($tanggal)
    {
        $this->tanggal = $tanggal;

        return $this;
    }

    /**
     * Get the value of jenis_jurnal
     */ 
    public function getJenis_jurnal()
    {
        return $this->jenis_jurnal;
    }

    /**
     * Set the value of jenis_jurnal
     *
     * @return  self
     */ 
    public function setJenis_jurnal($jenis_jurnal)
    {
        $this->jenis_jurnal = $jenis_jurnal;

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
     * Get the value of ref_bukti
     */ 
    public function getRef_bukti()
    {
        return $this->ref_bukti;
    }

    /**
     * Set the value of ref_bukti
     *
     * @return  self
     */ 
    public function setRef_bukti($ref_bukti)
    {
        $this->ref_bukti = $ref_bukti;

        return $this;
    }

    /**
     * Get the value of tanggal_insert
     */ 
    public function getTanggal_insert()
    {
        return $this->tanggal_insert;
    }

    /**
     * Set the value of tanggal_insert
     *
     * @return  self
     */ 
    public function setTanggal_insert($tanggal_insert)
    {
        $this->tanggal_insert = $tanggal_insert;

        return $this;
    }
}