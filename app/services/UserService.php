<?php

namespace MyApp\Services;

use MyApp\Models\User;
use Phalcon\Encryption\Security\Random;


class UserService extends AbstractService
{

	/**
	 * Creating a new user
	 *
	 * @param json $userData
	 */
    public function createUser($userData)
    {
        try {
            $random = new Random();
            $user = new User();
            $result = $user->setId($random->uuid())
			               ->setNama($userData->nama)
			               ->setPass($this->security->hash($userData->pass))
                           ->setLogin($userData->login)
						   ->setPerusahaan($userData->perusahaan->id)
						   ->setOffice_store_outlet($userData->office_outlet_store->id)
						   ->setHak_akses($userData->hak_akses->id)
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

	/**
	 * Updating user
	 *
     * @param string $userIdLama
	 * @param json $userDataBaru
	 */
	public function updateUser($userIdLama, $userDataBaru)
	{
		try {

            $user = User::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $userIdLama
					]
				]
			);

			if($user == null) {
				throw new ServiceException('Unable to update user', self::ERROR_UNABLE_UPDATE_ITEM);
			}		
			
			if($userIdLama != $userDataBaru->id) {
				$sql     = "
				UPDATE 
					public.tbl_users
				SET 
					id = :idBaru, 
					nama = :nama,
                    pass = :password,
					login = : userName,
					perusahaan = :perusahaan,
					office_store_outlet = :office,
					hak_akses = : hakAkses
				WHERE
					id = :idLama
				";

				$success = $this->db->execute(
					$sql,
					[
						'idBaru' => $userDataBaru->id,
						'nama' => $userDataBaru->nama,
                        'password' => $this->security->hash($userDataBaru->pass),
						'userName' => $userDataBaru->nama,
						'perusahaan' => $userDataBaru->perusahaan->id,
						'office' => $userDataBaru->office_store_outlet->id,
						'hakAkses' => $userDataBaru->hak_akses->id
					]
				);

				if(false === $success) {
					throw new ServiceException('Unable to update user', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
			else {
				$user->setNama($userDataBaru->nama);
                $user->setPass($this->security->hash($userDataBaru->pass));
				$user->setLogin($userDataBaru->login);
				$user->setPerusahaan($userDataBaru->perusahaan->id);
				$user->setOffice_store_outlet($userDataBaru->office_store_outlet->id);
				$user->setHak_akses($userDataBaru->hak_akses->id);
				$result = $user->update();

				if ( false === $result) {
					throw new ServiceException('Unable to update user', self::ERROR_UNABLE_UPDATE_ITEM);
				}
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Delete an existing user
	 *
	 * @param int $userId
	 */
	public function deleteHakAkses($userId)
	{
		try {
			$user = User::findFirst(
				[
					'conditions' => 'id = :id:',
					'bind'       => [
						'id' => $userId
					]
				]
			);

			if($user == null) {
				throw new ServiceException('User not found', self::ERROR_ITEM_NOT_FOUND);
			}
			
			if (false === $user->delete()) {
				throw new ServiceException('Unable to delete user', self::ERROR_UNABLE_DELETE_ITEM);
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
	}


	/**
	 * Returns user list
	 *
	 * @return array
	 */
    public function getUserList()
    {
        try {
			$daftarUser = User::find(
				[
					'conditions' => '',
					'bind'       => []
				]
			);

			if (!$daftarUser) {
				return [];
			}

			$i = 0;
			$hasil = array();
            foreach ($daftarUser as $user) {
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

				$user->setPerusahaan($perusahaan);
				$user->setOffice_store_outlet($detail_office);

				$hasil[$i] = $user;
				$i++;
            }

			return $hasil; 
		} catch (PDOException $e) {
			throw new ServiceException($e->getMessage(), $e->getCode(), $e);
		}
    }

}