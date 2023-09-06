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

namespace SP\Domain\Client\Ports;


use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\Domain\Common\Ports\RepositoryInterface;
use SP\Infrastructure\Database\QueryResult;
use SP\Mvc\Model\QueryCondition;

/**
 * Class ClientRepository
 *
 * @package SP\Infrastructure\Common\Repositories\Client
 */
interface ClientRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the item for given name
     *
     * @param  string  $name
     *
     * @return QueryResult
     * @throws QueryException
     * @throws ConstraintException
     */
    public function getByName(string $name): QueryResult;

    /**
     * Devolver los clientes visibles por el usuario
     *
     * @param  QueryCondition  $queryFilter
     *
     * @return QueryResult
     * @throws QueryException
     * @throws ConstraintException
     */
    public function getAllForFilter(QueryCondition $queryFilter): QueryResult;
}
