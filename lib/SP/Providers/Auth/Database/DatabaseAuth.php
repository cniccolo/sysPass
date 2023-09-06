<?php
/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2022, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Providers\Auth\Database;

use Exception;
use SP\Core\Crypt\Hash;
use SP\DataModel\UserLoginData;
use SP\Domain\User\Ports\UserPassServiceInterface;
use SP\Domain\User\Ports\UserServiceInterface;
use SP\Domain\User\Services\UserLoginResponse;
use SP\Domain\User\Services\UserPassService;
use SP\Domain\User\Services\UserService;

/**
 * Class Database
 *
 * Autentificación basada en base de datos
 *
 * @package SP\Providers\Auth\Database
 */
final class DatabaseAuth implements DatabaseAuthInterface
{
    private UserLoginData        $userLoginData;
    private UserServiceInterface $userService;
    private UserPassService      $userPassService;

    /**
     * Database constructor.
     *
     * @param  \SP\Domain\User\Ports\UserServiceInterface  $userService
     * @param  \SP\Domain\User\Ports\UserPassServiceInterface  $userPassService
     */
    public function __construct(UserServiceInterface $userService, UserPassServiceInterface $userPassService)
    {
        $this->userService = $userService;
        $this->userPassService = $userPassService;
    }


    /**
     * Autentificar al usuario
     *
     * @param  UserLoginData  $userLoginData  Datos del usuario
     *
     * @return DatabaseAuthData
     */
    public function authenticate(UserLoginData $userLoginData): DatabaseAuthData
    {
        $this->userLoginData = $userLoginData;

        $authData = new DatabaseAuthData();
        $authData->setAuthoritative($this->isAuthGranted());
        $authData->setAuthenticated($this->authUser());

        return $authData;
    }

    /**
     * Indica si es requerida para acceder a la aplicación
     *
     * @return boolean
     */
    public function isAuthGranted(): bool
    {
        return true;
    }

    /**
     * Autentificación de usuarios con BD.
     *
     * Esta función comprueba la clave del usuario. Si el usuario necesita ser migrado desde phpPMS,
     * se ejecuta el proceso para actualizar la clave.
     *
     * @return bool
     */
    protected function authUser(): bool
    {
        try {
            $userLoginResponse =
                UserService::mapUserLoginResponse($this->userService->getByLogin($this->userLoginData->getLoginUser()));

            $this->userLoginData->setUserLoginResponse($userLoginResponse);

            if ($userLoginResponse->getIsMigrate()
                && $this->checkMigrateUser($userLoginResponse)
            ) {
                $this->userPassService->migrateUserPassById(
                    $userLoginResponse->getId(),
                    $this->userLoginData->getLoginPass()
                );

                return true;
            }

            return Hash::checkHashKey($this->userLoginData->getLoginPass(), $userLoginResponse->getPass());
        } catch (Exception $e) {
            processException($e);
        }

        return false;
    }

    /**
     * @param  UserLoginResponse  $userLoginResponse
     *
     * @return bool
     */
    protected function checkMigrateUser(UserLoginResponse $userLoginResponse): bool
    {
        return ($userLoginResponse->getPass() === sha1(
                $userLoginResponse->getHashSalt().$this->userLoginData->getLoginPass()
            )
                || $userLoginResponse->getPass() === md5($this->userLoginData->getLoginPass())
                || hash_equals(
                    $userLoginResponse->getPass(),
                    crypt($this->userLoginData->getLoginPass(), $userLoginResponse->getHashSalt())
                )
                || Hash::checkHashKey($this->userLoginData->getLoginPass(), $userLoginResponse->getPass()));
    }
}
