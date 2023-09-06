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

namespace SP\Infrastructure\ItemPreset\Repositories;

use RuntimeException;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\ItemSearchData;
use SP\Domain\Account\Models\ItemPreset;
use SP\Domain\Common\Ports\RepositoryInterface;
use SP\Domain\ItemPreset\Ports\ItemPresetRepositoryInterface;
use SP\Infrastructure\Common\Repositories\Repository;
use SP\Infrastructure\Common\Repositories\RepositoryItemTrait;
use SP\Infrastructure\Database\QueryData;
use SP\Infrastructure\Database\QueryResult;

/**
 * Class AccountDefaultPermissionRepository
 *
 * @package SP\Infrastructure\Account\Repositories
 */
class ItemPresetRepository extends Repository implements RepositoryInterface, ItemPresetRepositoryInterface
{
    use RepositoryItemTrait;

    /**
     * Creates an item
     *
     * @param  ItemPreset  $itemData
     *
     * @return int
     * @throws ConstraintException
     * @throws QueryException
     */
    public function create($itemData): int
    {
        $queryData = new QueryData();
        $queryData->setQuery(
            'INSERT INTO ItemPreset 
                SET type = ?,
                userId = ?, 
                userGroupId = ?, 
                userProfileId = ?, 
                `fixed` = ?, 
                priority = ?,
                `data` = ?,
                `hash` = ?'
        );
        $queryData->setParams([
            $itemData->getType(),
            $itemData->getUserId(),
            $itemData->getUserGroupId(),
            $itemData->getUserProfileId(),
            $itemData->getFixed(),
            $itemData->getPriority(),
            $itemData->getData(),
            $itemData->getHash(),
        ]);
        $queryData->setOnErrorMessage(__u('Error while creating the permission'));

        return $this->db->doQuery($queryData)->getLastId();
    }

    /**
     * Updates an item
     *
     * @param  ItemPreset  $itemData
     *
     * @return int
     * @throws ConstraintException
     * @throws QueryException
     */
    public function update($itemData): int
    {
        $queryData = new QueryData();
        $queryData->setQuery(
            'UPDATE ItemPreset 
                SET type = ?, 
                userId = ?, 
                userGroupId = ?, 
                userProfileId = ?, 
                `fixed` = ?, 
                priority = ?,
                `data` = ?,
                `hash` = ?
                WHERE id = ? LIMIT 1'
        );
        $queryData->setParams([
            $itemData->getType(),
            $itemData->getUserId(),
            $itemData->getUserGroupId(),
            $itemData->getUserProfileId(),
            $itemData->getFixed(),
            $itemData->getPriority(),
            $itemData->getData(),
            $itemData->getHash(),
            $itemData->getId(),
        ]);
        $queryData->setOnErrorMessage(__u('Error while updating the permission'));

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Deletes an item
     *
     * @param  int  $id
     *
     * @return int
     * @throws ConstraintException
     * @throws QueryException
     */
    public function delete(int $id): int
    {
        $queryData = new QueryData();
        $queryData->setQuery('DELETE FROM ItemPreset WHERE id = ? LIMIT 1');
        $queryData->setParams([$id]);
        $queryData->setOnErrorMessage(__u('Error while removing the permission'));

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Returns the item for given id
     *
     * @param  int  $id
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getById(int $id): QueryResult
    {
        $queryData = new QueryData();
        $queryData->setMapClassName(ItemPreset::class);
        $queryData->setQuery(
            'SELECT id, type, userId, userGroupId, userProfileId, `fixed`, priority, `data` 
        FROM ItemPreset WHERE id = ? LIMIT 1'
        );
        $queryData->setParams([$id]);

        return $this->db->doSelect($queryData);
    }

    /**
     * Returns the item for given id
     *
     * @param  string  $type
     * @param  int  $userId
     * @param  int  $userGroupId
     * @param  int  $userProfileId
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getByFilter(
        string $type,
        int $userId,
        int $userGroupId,
        int $userProfileId
    ): QueryResult {
        $queryData = new QueryData();
        $queryData->setMapClassName(ItemPreset::class);
        $queryData->setQuery(
            'SELECT id, type, userId, userGroupId, userProfileId, `fixed`, priority, `data`,
                    IF(userId IS NOT NULL, priority + 3,
                      IF(userGroupId IS NOT NULL, priority + 2,
                        IF(userProfileId IS NOT NULL, priority + 1, 0))) AS score
                    FROM ItemPreset
                    WHERE type = ?
                    AND (userId = ? 
                      OR userGroupId = ? 
                      OR userProfileId = ? 
                      OR userGroupId IN (SELECT UserToUserGroup.userGroupId
                        FROM UserToUserGroup
                        WHERE UserToUserGroup.userId = ?)
                    )
                    ORDER BY score DESC
                    LIMIT 1'
        );

        $queryData->setParams([$type, $userId, $userGroupId, $userProfileId, $userId]);

        return $this->db->doSelect($queryData);
    }

    /**
     * Returns all the items
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getAll(): QueryResult
    {
        $queryData = new QueryData();
        $queryData->setMapClassName(ItemPreset::class);
        $queryData->setQuery(
            'SELECT id, type, userId, userGroupId, userProfileId, `fixed`, priority, `data` 
        FROM ItemPreset'
        );

        return $this->db->doSelect($queryData);
    }

    /**
     * Returns all the items for given ids
     *
     * @param  array  $ids
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getByIdBatch(array $ids): QueryResult
    {
        if (count($ids) === 0) {
            return new QueryResult();
        }

        $queryData = new QueryData();
        $queryData->setMapClassName(ItemPreset::class);
        $queryData->setQuery(
            'SELECT type, userId, userGroupId, userProfileId, `fixed`, priority, `data`
            FROM ItemPreset WHERE id IN ('.$this->buildParamsFromArray($ids).')'
        );
        $queryData->setParams($ids);

        return $this->db->doSelect($queryData);
    }

    /**
     * Deletes all the items for given ids
     *
     * @param  int[]  $ids
     *
     * @return int
     * @throws ConstraintException
     * @throws QueryException
     */
    public function deleteByIdBatch(array $ids): int
    {
        if (count($ids) === 0) {
            return 0;
        }

        $queryData = new QueryData();
        $queryData->setQuery('DELETE FROM ItemPreset WHERE id IN ('.$this->buildParamsFromArray($ids).')');
        $queryData->setParams($ids);
        $queryData->setOnErrorMessage(__u('Error while removing the permissions'));

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Checks whether the item is in use or not
     *
     * @param $id int
     */
    public function checkInUse(int $id): bool
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Checks whether the item is duplicated on updating
     *
     * @param  mixed  $itemData
     */
    public function checkDuplicatedOnUpdate($itemData): bool
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Checks whether the item is duplicated on adding
     *
     * @param  mixed  $itemData
     */
    public function checkDuplicatedOnAdd($itemData): bool
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Searches for items by a given filter
     *
     * @param  ItemSearchData  $itemSearchData
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function search(ItemSearchData $itemSearchData): QueryResult
    {
        $queryData = new QueryData();
        $queryData->setSelect(
            'ItemPreset.id,
            ItemPreset.type,
            ItemPreset.userId,
            ItemPreset.userGroupId,
            ItemPreset.userProfileId,
            ItemPreset.`fixed`,
            ItemPreset.priority,
            ItemPreset.data,
            User.name AS userName,
            UserProfile.name AS userProfileName,
            UserGroup.name AS userGroupName,
            IF(ItemPreset.userId IS NOT NULL, ItemPreset.priority + 3,
                      IF(ItemPreset.userGroupId IS NOT NULL, ItemPreset.priority + 2,
                        IF(ItemPreset.userProfileId IS NOT NULL, ItemPreset.priority + 1, 0))) AS score'
        );
        $queryData->setFrom(
            '
            ItemPreset
            LEFT JOIN User ON ItemPreset.userId = User.id 
            LEFT JOIN UserProfile ON ItemPreset.userProfileId = UserProfile.id 
            LEFT JOIN UserGroup ON ItemPreset.userGroupId = UserGroup.id'
        );
        $queryData->setOrder(
            'ItemPreset.type, score DESC'
        );

        if (!empty($itemSearchData->getSeachString())) {
            $queryData->setWhere(
                'ItemPreset.type LIKE ? 
                OR User.name LIKE ? 
                OR UserProfile.name LIKE ? 
                OR UserGroup.name LIKE ?'
            );

            $search = '%'.$itemSearchData->getSeachString().'%';
            $queryData->addParam($search);
            $queryData->addParam($search);
            $queryData->addParam($search);
            $queryData->addParam($search);
        }

        $queryData->setLimit(
            '?,?',
            [$itemSearchData->getLimitStart(), $itemSearchData->getLimitCount()]
        );

        return $this->db->doSelect($queryData, true);
    }
}
