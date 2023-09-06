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


use Exception;
use SP\Core\Application;
use SP\Core\Context\ContextBase;
use SP\Core\Events\Event;
use SP\Modules\Web\Controllers\Helpers\Account\AccountSearchHelper;
use SP\Mvc\Controller\WebControllerHelper;
use SP\Providers\Acl\AclHandler;
use SP\Util\ErrorUtil;

/**
 * Class IndexController
 */
final class IndexController extends AccountControllerBase
{
    private AccountSearchHelper $accountSearchHelper;

    public function __construct(
        Application $application,
        WebControllerHelper $webControllerHelper,
        AccountSearchHelper $accountSearchHelper
    ) {
        parent::__construct(
            $application,
            $webControllerHelper
        );

        $this->accountSearchHelper = $accountSearchHelper;
    }

    /**
     * Index action
     *
     */
    public function indexAction(): void
    {
        try {
            $this->accountSearchHelper->getSearchBox();
            $this->accountSearchHelper->getAccountSearch();

            $this->eventDispatcher->notifyEvent('show.account.search', new Event($this));

            $this->view();
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            ErrorUtil::showExceptionInView($this->view, $e);
        }
    }
}
