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

namespace SP\Infrastructure\Category\Repositories;

use RuntimeException;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\Core\Exceptions\SPException;
use SP\DataModel\CategoryData;
use SP\DataModel\ItemSearchData;
use SP\Domain\Category\Ports\CategoryRepositoryInterface;
use SP\Infrastructure\Common\Repositories\DuplicatedItemException;
use SP\Infrastructure\Common\Repositories\Repository;
use SP\Infrastructure\Common\Repositories\RepositoryItemTrait;
use SP\Infrastructure\Database\QueryData;
use SP\Infrastructure\Database\QueryResult;

/**
 * Class CategoryRepository
 *
 * @package SP\Infrastructure\Common\Repositories\Category
 */
final class CategoryRepository extends Repository implements CategoryRepositoryInterface
{
    use RepositoryItemTrait;

    /**
     * Creates an item
     *
     * @param  CategoryData  $itemData
     *
     * @return int
     * @throws SPException
     * @throws DuplicatedItemException
     */
    public function create($itemData): int
    {
        if ($this->checkDuplicatedOnAdd($itemData)) {
            throw new DuplicatedItemException(__u('Duplicated category'), SPException::WARNING);
        }

        $queryData = new QueryData();
        $queryData->setQuery('INSERT INTO Category SET `name` = ?, description = ?, `hash` = ?');
        $queryData->setParams([
            $itemData->getName(),
            $itemData->getDescription(),
            $this->makeItemHash($itemData->getName(), $this->db->getDbHandler()),
        ]);
        $queryData->setOnErrorMessage(__u('Error while creating the category'));

        return $this->db->doQuery($queryData)->getLastId();
    }

    /**
     * Checks whether the item is duplicated on adding
     *
     * @param  CategoryData  $itemData
     *
     * @return bool
     * @throws ConstraintException
     * @throws QueryException
     */
    public function checkDuplicatedOnAdd($itemData): bool
    {
        $queryData = new QueryData();
        $queryData->setQuery('SELECT id FROM Category WHERE `hash` = ? OR `name` = ?');
        $queryData->setParams([
            $this->makeItemHash($itemData->getName(), $this->db->getDbHandler()),
            $itemData->getName(),
        ]);

        return $this->db->doQuery($queryData)->getNumRows() > 0;
    }

    /**
     * Updates an item
     *
     * @param  CategoryData  $itemData
     *
     * @return int
     * @throws DuplicatedItemException
     * @throws ConstraintException
     * @throws QueryException
     */
    public function update($itemData): int
    {
        if ($this->checkDuplicatedOnUpdate($itemData)) {
            throw new DuplicatedItemException(__u('Duplicated category name'), SPException::WARNING);
        }

        $query = /** @lang SQL */
            'UPDATE Category
              SET `name` = ?,
              description = ?,
              `hash` = ?
              WHERE id = ? LIMIT 1';

        $queryData = new QueryData();
        $queryData->setQuery($query);
        $queryData->setParams([
            $itemData->getName(),
            $itemData->getDescription(),
            $this->makeItemHash($itemData->getName(), $this->db->getDbHandler()),
            $itemData->getId(),
        ]);
        $queryData->setOnErrorMessage(__u('Error while updating the category'));

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Checks whether the item is duplicated on updating
     *
     * @param  CategoryData  $itemData
     *
     * @return bool
     * @throws ConstraintException
     * @throws QueryException
     */
    public function checkDuplicatedOnUpdate($itemData): bool
    {
        $queryData = new QueryData();
        $queryData->setQuery('SELECT id FROM Category WHERE (`hash` = ? OR `name` = ?) AND id <> ?');
        $queryData->setParams([
            $this->makeItemHash($itemData->getName(), $this->db->getDbHandler()),
            $itemData->getName(),
            $itemData->getId(),
        ]);

        return $this->db->doQuery($queryData)->getNumRows() > 0;
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
        $queryData->setMapClassName(CategoryData::class);
        $queryData->setQuery('SELECT id, `name`, description FROM Category WHERE id = ? LIMIT 1');
        $queryData->addParam($id);

        return $this->db->doSelect($queryData);
    }

    /**
     * Returns the item for given id
     *
     * @param  string  $name
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getByName(string $name): QueryResult
    {
        $queryData = new QueryData();
        $queryData->setMapClassName(CategoryData::class);
        $queryData->setQuery('SELECT id, `name`, description FROM Category WHERE `name` = ? OR `hash` = ? LIMIT 1');
        $queryData->setParams([
            $name,
            $this->makeItemHash($name, $this->db->getDbHandler()),
        ]);

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
        $queryData->setMapClassName(CategoryData::class);
        $queryData->setQuery('SELECT id, `name`, description, `hash` FROM Category ORDER BY `name`');

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

        $query = /** @lang SQL */
            'SELECT id, `name`, description FROM Category WHERE id IN ('.$this->buildParamsFromArray($ids).')';

        $queryData = new QueryData();
        $queryData->setMapClassName(CategoryData::class);
        $queryData->setQuery($query);
        $queryData->setParams($ids);

        return $this->db->doSelect($queryData);
    }

    /**
     * Deletes all the items for given ids
     *
     * @param  array  $ids
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
        $queryData->setQuery('DELETE FROM Category WHERE id IN ('.$this->buildParamsFromArray($ids).')');
        $queryData->setParams($ids);
        $queryData->setOnErrorMessage(__u('Error while deleting the categories'));

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
        $query = /** @lang SQL */
            'DELETE FROM Category WHERE id = ? LIMIT 1';

        $queryData = new QueryData();
        $queryData->setQuery($query);
        $queryData->addParam($id);
        $queryData->setOnErrorMessage(__u('Error while deleting the category'));

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Checks whether the item is in use or not
     *
     * @param $id int
     *
     * @return void
     */
    public function checkInUse(int $id): bool
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
        $queryData->setSelect('id, name, description');
        $queryData->setFrom('Category');
        $queryData->setOrder('name');

        if (!empty($itemSearchData->getSeachString())) {
            $queryData->setWhere('name LIKE ? OR description LIKE ?');

            $search = '%'.$itemSearchData->getSeachString().'%';
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
