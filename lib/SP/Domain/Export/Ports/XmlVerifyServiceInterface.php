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

namespace SP\Domain\Export\Ports;

use SP\Domain\Common\Services\ServiceException;
use SP\Domain\Export\Services\VerifyResult;
use SP\Domain\Import\Services\ImportException;
use SP\Infrastructure\File\FileException;

/**
 * Class XmlVerifyService
 *
 * Verifies a sysPass exported file format
 *
 * @package SP\Domain\Export\Services
 */
interface XmlVerifyServiceInterface
{
    /**
     * @throws FileException
     * @throws ImportException
     * @throws ServiceException
     */
    public function verify(string $xmlFile): VerifyResult;

    /**
     * @throws FileException
     * @throws ImportException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function verifyEncrypted(string $xmlFile, string $password): VerifyResult;
}
