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

namespace SP\Domain\CustomField\Ports;

use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\CustomFieldDefinitionData;
use SP\DataModel\ItemSearchData;
use SP\Domain\Common\Services\ServiceException;
use SP\Domain\CustomField\Services\CustomFieldDefService;
use SP\Infrastructure\Database\QueryResult;

/**
 * Class CustomFieldDefService
 *
 * @package SP\Domain\CustomField\Services
 */
interface CustomFieldDefServiceInterface
{
    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function search(ItemSearchData $itemSearchData): QueryResult;

    /**
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function delete(int $id): CustomFieldDefService;

    /**
     * Deletes all the items for given ids
     *
     * @param  int[]  $ids
     *
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function deleteByIdBatch(array $ids): void;

    /**
     * @param  \SP\DataModel\CustomFieldDefinitionData  $itemData
     *
     * @return int
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function create(CustomFieldDefinitionData $itemData): int;

    /**
     * @throws ServiceException
     */
    public function update(CustomFieldDefinitionData $itemData);

    /**
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function getById(int $id): CustomFieldDefinitionData;

    /**
     * @throws \SP\Domain\Common\Services\ServiceException
     * @throws ConstraintException
     * @throws QueryException
     */
    public function updateRaw(CustomFieldDefinitionData $itemData): void;

    /**
     * Get all items from the service's repository
     *
     * @return CustomFieldDefinitionData[]
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getAllBasic(): array;
}
