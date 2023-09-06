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

namespace SP\Infrastructure\Config\Repositories;

use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\ConfigData;
use SP\Domain\Config\Ports\ConfigRepositoryInterface;
use SP\Infrastructure\Common\Repositories\Repository;
use SP\Infrastructure\Database\QueryData;
use SP\Infrastructure\Database\QueryResult;

/**
 * Class ConfigRepository
 *
 * @package SP\Infrastructure\Common\Repositories\Config
 */
final class ConfigRepository extends Repository implements ConfigRepositoryInterface
{
    /**
     * @param  ConfigData  $configData
     *
     * @return bool
     * @throws ConstraintException
     * @throws QueryException
     */
    public function update(ConfigData $configData): bool
    {
        $queryData = new QueryData();
        $queryData->setQuery('UPDATE Config SET `value` = ? WHERE parameter = ?');
        $queryData->setParams([$configData->getValue(), $configData->getParameter()]);

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * @param  ConfigData  $configData
     *
     * @return int
     * @throws ConstraintException
     * @throws QueryException
     */
    public function create(ConfigData $configData): int
    {
        $queryData = new QueryData();
        $queryData->setQuery('INSERT INTO Config SET parameter = ?, `value` = ?');
        $queryData->setParams([$configData->getParameter(), $configData->getValue()]);

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Obtener un array con la configuración almacenada en la BBDD.
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getAll(): QueryResult
    {
        $queryData = new QueryData();
        $queryData->setQuery('SELECT parameter, `value` FROM Config ORDER BY parameter');

        return $this->db->doSelect($queryData);
    }

    /**
     * @param  string  $param
     *
     * @return QueryResult
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getByParam(string $param): QueryResult
    {
        $queryData = new QueryData();
        $queryData->setQuery('SELECT parameter, `value` FROM Config WHERE parameter = ? LIMIT 1');
        $queryData->addParam($param);

        return $this->db->doSelect($queryData);
    }

    /**
     * @param  string  $param
     *
     * @return bool
     * @throws ConstraintException
     * @throws QueryException
     */
    public function has(string $param): bool
    {
        $queryData = new QueryData();
        $queryData->setQuery('SELECT parameter FROM Config WHERE parameter = ? LIMIT 1');
        $queryData->addParam($param);

        return $this->db->doSelect($queryData)->getNumRows() === 1;
    }

    /**
     * @param  string  $param
     *
     * @return int
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function deleteByParam(string $param): int
    {
        $queryData = new QueryData();
        $queryData->setQuery('DELETE FROM Config WHERE parameter = ? LIMIT 1');
        $queryData->addParam($param);

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }
}
