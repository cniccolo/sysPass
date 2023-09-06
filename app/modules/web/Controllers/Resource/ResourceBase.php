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

namespace SP\Modules\Web\Controllers\Resource;

use SP\Core\Application;
use SP\Html\Minify;
use SP\Modules\Web\Controllers\SimpleControllerBase;
use SP\Mvc\Controller\SimpleControllerHelper;

/**
 * Class ResourceBase
 */
abstract class ResourceBase extends SimpleControllerBase
{
    protected Minify $minify;

    /**
     * @throws \SP\Core\Exceptions\SessionTimeout
     * @throws \SP\Core\Exceptions\SPException
     */
    public function __construct(
        Application $application,
        SimpleControllerHelper $simpleControllerHelper,
        Minify $minify
    ) {
        parent::__construct($application, $simpleControllerHelper);

        $this->minify = $minify;

        $this->request->verifySignature($this->configData->getPasswordSalt());
    }
}