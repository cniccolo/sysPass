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

use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\Domain\Common\Services\ServiceException;

/**
 * Class UserPassRecoverService
 *
 * @package SP\Domain\Common\Services\UserPassRecover
 */
interface UserPassRecoverServiceInterface
{
    /**
     * @throws \SP\Core\Exceptions\SPException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function toggleUsedByHash(string $hash): void;

    /**
     * @throws ConstraintException
     * @throws QueryException
     * @throws ServiceException
     * @throws EnvironmentIsBrokenException
     */
    public function requestForUserId(int $id): string;

    /**
     * Comprobar el límite de recuperaciones de clave.
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function checkAttemptsByUserId(int $userId): bool;

    /**
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function add(int $userId, string $hash): bool;

    /**
     * Comprobar el hash de recuperación de clave.
     *
     * @throws \SP\Domain\Common\Services\ServiceException
     * @throws ConstraintException
     * @throws QueryException
     */
    public function getUserIdForHash(string $hash): int;
}
