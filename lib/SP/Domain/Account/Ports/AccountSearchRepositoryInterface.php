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

namespace SP\Domain\Account\Ports;

use Aura\SqlQuery\Common\SelectInterface;
use SP\Domain\Account\Search\AccountSearchFilter;
use SP\Domain\Common\Ports\RepositoryInterface;
use SP\Infrastructure\Database\QueryResult;

/**
 * Class AccountSearchRepository
 */
interface AccountSearchRepositoryInterface extends RepositoryInterface
{
    /**
     * Obtener las cuentas de una búsqueda.
     *
     * @param  AccountSearchFilter  $accountSearchFilter
     *
     * @return QueryResult
     */
    public function getByFilter(AccountSearchFilter $accountSearchFilter): QueryResult;

    /**
     * @param  int  $userId
     * @param  int  $userGroupId
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForUser(int $userId, int $userGroupId): SelectInterface;

    /**
     * @param  int  $userGroupId
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForGroup(int $userGroupId): SelectInterface;

    /**
     * @param  string  $userGroupName
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForMainGroup(string $userGroupName): SelectInterface;

    /**
     * @param  string  $owner
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForOwner(string $owner): SelectInterface;

    /**
     * @param  string  $fileName
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForFile(string $fileName): SelectInterface;

    /**
     * @param  int  $accountId
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForAccountId(int $accountId): SelectInterface;

    /**
     * @param  string  $clientName
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForClient(string $clientName): SelectInterface;

    /**
     * @param  string  $categoryName
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForCategory(string $categoryName): SelectInterface;

    /**
     * @param  string  $accountName
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForAccountNameRegex(string $accountName): SelectInterface;

    public function withFilterForIsExpired(): SelectInterface;

    public function withFilterForIsNotExpired(): SelectInterface;

    /**
     * @param  int  $userId
     * @param  int  $userGroupId
     *
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    public function withFilterForIsPrivate(int $userId, int $userGroupId): SelectInterface;

    public function withFilterForIsNotPrivate(): SelectInterface;
}
