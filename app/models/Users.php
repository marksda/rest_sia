<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

class Users extends Model
{
    protected $id; 
    protected $nama; 
    protected $pass;
    protected $login;  

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

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource('users');
    }

    public static function find($parameters = null): ResultsetInterface
    {
        return parent::find($parameters);
    }
}