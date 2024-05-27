<?php

namespace MyApp\Services;

use MyApp\Models\User;
use Phalcon\Db\Column;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Parser;
use Phalcon\Encryption\Security\JWT\Validator;

class TokenService extends AbstractService
{
    public function createToken($dataCredential)
    {   
        
        try {
            $user = User::findFirst(
				[
					'conditions' => 'login = :login:',
					'bind'       => [
                        'login' => $dataCredential->userName
                    ]
				]
			);

            $token = [];

			if (!$user) {
				return $token;
			}

            if($this->security->checkHash($dataCredential->password, $user->getPass()) == true) {
                $detail_office = $user->getRelated('detail_office_store_outlet');				
                $detail_office->setPropinsi($detail_office->getRelated('detail_propinsi'));
                $detail_office->setKabupaten($detail_office->getRelated('detail_kabupaten'));
                $detail_office->setKecamatan($detail_office->getRelated('detail_kecamatan'));
                $detail_office->setDesa($detail_office->getRelated('detail_desa'));

                $perusahaan = $detail_office->getRelated('detail_perusahaan');
                $perusahaan->setPropinsi($perusahaan->getRelated('detail_propinsi'));
                $perusahaan->setKabupaten($perusahaan->getRelated('detail_kabupaten'));
                $perusahaan->setKecamatan($perusahaan->getRelated('detail_kecamatan'));
                $perusahaan->setDesa($perusahaan->getRelated('detail_desa'));

                $detail_office->setPerusahaan($perusahaan);

                $hak_akses = $user->getRelated('detail_hak_akses');
                $modul = $hak_akses->getRelated('detail_modul');
                $hak_akses->setModul($modul);

                $user->setPerusahaan($perusahaan);
                $user->setOffice_store_outlet($detail_office);
                $user->setHak_akses($hak_akses);
                $user->setPass(null);

                // Defaults to 'sha512'
                $signer  = new Hmac();

                // Builder object
                $builder = new Builder($signer);

                $now        = new \DateTimeImmutable();
                $issued     = $now->getTimestamp();
                $notBefore  = $now->modify('-1 minute')->getTimestamp();
                $expires    = $now->modify('+1 day')->getTimestamp();
                $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

                // Setup
                $builder
                    ->setAudience('account')                        // aud
                    ->setContentType('application/json')            // cty - header
                    ->setExpirationTime($expires)                   // exp 
                    ->setId($user->getId())                          // JTI id 
                    ->setIssuedAt($issued)                          // iat 
                    ->setIssuer('https://dlhk.ddns.net')            // iss 
                    ->setNotBefore($notBefore)                      // nbf
                    ->setSubject('sia')                             // sub
                    ->setPassphrase($passphrase)                    // password 
                ;

                $tokenObject = $builder->getToken();

                
                $token['nama'] = $user->getNama();
                $token['office'] = $user->getOffice_store_outlet();
                $token['akses'] = $user->getHak_akses();
                $token['token'] = $tokenObject->getToken();
            }

			return $token; 
        }
        catch (PDOException $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

}