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

namespace SP\Modules\Web\Controllers\Account;

use SP\Core\Application;
use SP\Core\Context\ContextBase;
use SP\Modules\Web\Controllers\ControllerBase;
use SP\Mvc\Controller\WebControllerHelper;

/**
 * AccountControllerBase
 */
abstract class AccountControllerBase extends ControllerBase
{
    private const LOGIN_NOT_REQUIRED = ['ViewLinkController'];

    /**
     * @param  \SP\Core\Application  $application
     * @param  \SP\Mvc\Controller\WebControllerHelper  $webControllerHelper
     *
     * @throws \SP\Core\Exceptions\SessionTimeout
     * @throws \SP\Domain\Auth\Services\AuthException
     */
    public function __construct(Application $application, WebControllerHelper $webControllerHelper)
    {
        parent::__construct($application, $webControllerHelper);

        $this->initialize();
    }

    /**
     * Initialize class
     *
     * @throws \SP\Core\Exceptions\SessionTimeout
     * @throws \SP\Domain\Auth\Services\AuthException
     */
    private function initialize(): void
    {
        if (in_array(static::class, self::LOGIN_NOT_REQUIRED)) {
            $this->checkLoggedIn();
        }

        if (DEBUG === true && $this->session->getAppStatus() === ContextBase::APP_STATUS_RELOADED) {
            $this->session->resetAppStatus();
        }
    }
}
