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

namespace SP\Domain\Notification\Ports;

use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\ItemSearchData;
use SP\DataModel\NotificationData;
use SP\Infrastructure\Common\Repositories\NoSuchItemException;
use SP\Infrastructure\Database\QueryResult;

/**
 * Class NotificationService
 *
 * @package SP\Domain\Notification\Services
 */
interface NotificationServiceInterface
{
    /**
     * Creates an item
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function create(NotificationData $itemData): int;

    /**
     * Updates an item
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function update(NotificationData $itemData): int;

    /**
     * Devolver los elementos con los ids especificados
     *
     * @param  int[]  $ids
     *
     * @return NotificationData[]
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getByIdBatch(array $ids): array;

    /**
     * Deletes an item preserving the sticky ones
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function delete(int $id): NotificationServiceInterface;

    /**
     * Deletes an item
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function deleteAdmin(int $id): NotificationServiceInterface;

    /**
     * Deletes an item
     *
     * @param  int[]  $ids
     *
     * @throws ConstraintException
     * @throws QueryException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function deleteAdminBatch(array $ids): int;

    /**
     * Deletes all the items for given ids
     *
     * @param  int[]  $ids
     *
     * @throws ConstraintException
     * @throws QueryException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function deleteByIdBatch(array $ids): int;

    /**
     * Returns the item for given id
     *
     * @throws ConstraintException
     * @throws QueryException
     * @throws NoSuchItemException
     */
    public function getById(int $id): NotificationData;

    /**
     * Returns all the items
     *
     * @return NotificationData[]
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getAll(): array;

    /**
     * Marcar una notificación como leída
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function setCheckedById(int $id): void;

    /**
     * Devolver las notificaciones de un usuario para una fecha y componente determinados
     *
     * @return NotificationData[]
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function getForUserIdByDate(string $component, int $id): array;

    /**
     * @return NotificationData[]
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getAllForUserId(int $id): array;

    /**
     * @return NotificationData[]
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function getAllActiveForUserId(int $id): array;

    /**
     * Searches for items by a given filter
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function search(ItemSearchData $itemSearchData): QueryResult;

    /**
     * Searches for items by a given filter
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function searchForUserId(ItemSearchData $itemSearchData, int $userId): QueryResult;
}
