<?php

namespace MyApp\Services;

use MyApp\Models\Users;
use Phalcon\Encryption\Security\Random;


class UsersService extends AbstractService
{

    public function createUser($userData)
    {
        try {
            $random = new Random();
            $user = new Users();
            $result = $user->setId($random->uuid())
			               ->setNama($userData->nama)
			               ->setPass($this->security->hash($userData->pass))
                           ->setLogin($userData->login)
			               ->create();
            
			if (!$result) {
				throw new ServiceException('Unable to create user', self::ERROR_UNABLE_CREATE_ITEM);
			}
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
				throw new ServiceException('User already exists', self::ERROR_ALREADY_EXISTS, $e);
			} else {
				throw new ServiceException($e->getMessage(), $e->getCode(), $e);
			}
        }
    }

    public function getUserList()
    {
        try {
			$users = Users::find(
				[
					'conditions' => '',
					'bind'       => [],
					'columns'    => "id, nama, pass, login",
				]
			);

			if (!$users) {
				return [];
			}

			return $users->toArray();
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}