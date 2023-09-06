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

namespace SP\Domain\User\Ports;

use Defuse\Crypto\Exception\CryptoException;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\Core\Exceptions\SPException;
use SP\DataModel\ItemSearchData;
use SP\DataModel\UserData;
use SP\DataModel\UserPreferencesData;
use SP\Domain\User\Services\UserLoginRequest;
use SP\Domain\User\Services\UserService;
use SP\Infrastructure\Common\Repositories\DuplicatedItemException;
use SP\Infrastructure\Common\Repositories\NoSuchItemException;
use SP\Infrastructure\Database\QueryResult;

/**
 * Class UserService
 *
 * @package SP\Domain\User\Services
 */
interface UserServiceInterface
{
    /**
     * Actualiza el último inicio de sesión del usuario en la BBDD.
     *
     * @throws ConstraintException
     * @throws NoSuchItemException
     * @throws QueryException
     */
    public function updateLastLoginById(int $id): int;

    /**
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function checkExistsByLogin(string $login): bool;

    /**
     * Returns the item for given id
     *
     * @throws SPException
     */
    public function getById(int $id): UserData;

    /**
     * Returns the item for given id
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function getByLogin(string $login): UserData;

    /**
     * Deletes an item
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function delete(int $id): UserService;

    /**
     * @param  int[]  $ids
     *
     * @throws \SP\Domain\Common\Services\ServiceException
     * @throws ConstraintException
     * @throws QueryException
     */
    public function deleteByIdBatch(array $ids): int;

    /**
     * Creates an item
     *
     * @throws SPException
     */
    public function createOnLogin(UserLoginRequest $userLoginRequest): int;

    /**
     * Creates an item
     *
     * @throws SPException
     */
    public function create(UserData $itemData): int;

    /**
     * Creates an item
     *
     * @throws SPException
     * @throws CryptoException
     */
    public function createWithMasterPass(UserData $itemData, string $userPass, string $masterPass): int;

    /**
     * Searches for items by a given filter
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function search(ItemSearchData $searchData): QueryResult;

    /**
     * Updates an item
     *
     * @throws ConstraintException
     * @throws QueryException
     * @throws DuplicatedItemException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function update(UserData $userData): void;

    /**
     * Updates a user's pass
     *
     * @throws ConstraintException
     * @throws QueryException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function updatePass(int $userId, string $pass): void;

    /**
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function updatePreferencesById(int $userId, UserPreferencesData $userPreferencesData): int;

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function updateOnLogin(UserLoginRequest $userLoginRequest): int;

    /**
     * Get all items from the service's repository
     *
     * @return UserData[]
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getAllBasic(): array;

    /**
     * Obtener el email de los usuarios de un grupo
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function getUserEmailForGroup(int $groupId): array;

    /**
     * Obtener el email de los usuarios de un grupo
     *
     * @throws ConstraintException
     * @throws QueryException
     **/
    public function getUserEmailForAll(): array;

    /**
     * Return the email of the given user's id
     *
     * @param  int[]  $ids
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getUserEmailById(array $ids): array;

    /**
     * Returns the usage of the given user's id
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getUsageForUser(int $id): array;
}
