<?php

namespace MyApp\Services;

use MyApp\Models\Perusahaan;
use Phalcon\Encryption\Security\Random;


class UsersService extends AbstractService
{

    public function createPerusahaan($perusahaanData)
    {
        try {
            $random = new Random();
            $perusahaan = new Perusahaan();
            $result = $perusahaan->setId($random->base58(12))
			               ->setNama($userData->nama)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create user', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('Perusahaan already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    public function getPerusahaanList()
    {
        try {
			$perusahaan = Perusahaan::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, nama",
				]
			);

			if (!$perusahaan) {
				return [];
			}

			return $perusahaan->toArray();
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}