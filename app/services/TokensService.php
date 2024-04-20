<?php

namespace MyApp\Services;

use Phalcon\Db\Column;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Parser;
use Phalcon\Encryption\Security\JWT\Validator;

class TokensService extends AbstractService
{
    public function createToken($dataCredential)
    {        
        $sql = "
            SELECT 
                id, nama, pass,
            FROM
                public.users
            WHERE 
                login = :userName
            ";

        $result = $this->db->fetchOne(
            $sql,
            2,
            [
                'userName' => $dataCredential->userName
            ],
            [
                Column::BIND_PARAM_STR
            ]
        );

        $token = [];

        if($result) {
            if($this->security->checkHash($dataCredential->password, $result['pass']) == true) {
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
                    ->setId($result['id'])                          // JTI id 
                    ->setIssuedAt($issued)                          // iat 
                    ->setIssuer('https://dlhk.ddns.net')            // iss 
                    ->setNotBefore($notBefore)                      // nbf
                    ->setSubject('sia')                             // sub
                    ->setPassphrase($passphrase)                    // password 
                ;

                $tokenObject = $builder->getToken();

                $token['id'] = $result['id'];
                $token['nama'] = $result['nama'];
                $token['token'] = $tokenObject->getToken();
            }                
        }

        return $token;
    }

}