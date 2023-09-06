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
use SP\Core\Acl\ActionsInterface;
use SP\Core\Events\Event;
use SP\Util\ErrorUtil;

/**
 * Class CopyController
 */
final class CopyController extends AccountViewBase
{
    /**
     * Copy action
     *
     * @param  int  $id  Account's ID
     */
    public function copyAction(int $id): void
    {
        try {
            $accountEnrichedDto = $this->accountService->getByIdEnriched($id);
            $accountEnrichedDto = $this->accountService->withUsers($accountEnrichedDto);
            $accountEnrichedDto = $this->accountService->withUserGroups($accountEnrichedDto);
            $accountEnrichedDto = $this->accountService->withTags($accountEnrichedDto);

            $this->accountHelper->setViewForAccount($accountEnrichedDto, ActionsInterface::ACCOUNT_COPY);

            $this->view->addTemplate('account');
            $this->view->assign(
                'title',
                [
                    'class' => 'titleGreen',
                    'name'  => __('New Account'),
                    'icon'  => $this->icons->getIconAdd()->getIcon(),
                ]
            );
            $this->view->assign('formRoute', 'account/saveCopy');

            $this->eventDispatcher->notifyEvent('show.account.copy', new Event($this));

            if ($this->isAjax === false) {
                $this->upgradeView();
            }

            $this->view();
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            if ($this->isAjax === false && !$this->view->isUpgraded()) {
                $this->upgradeView();
            }

            ErrorUtil::showExceptionInView($this->view, $e, 'account');
        }
    }
}
