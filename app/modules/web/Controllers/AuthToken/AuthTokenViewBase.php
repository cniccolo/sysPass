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

namespace SP\Modules\Web\Controllers\AuthToken;


use SP\Core\Acl\Acl;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Application;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\Core\Exceptions\SPException;
use SP\DataModel\AuthTokenData;
use SP\Domain\Auth\Ports\AuthTokenServiceInterface;
use SP\Domain\Auth\Services\AuthTokenService;
use SP\Domain\CustomField\Ports\CustomFieldServiceInterface;
use SP\Domain\User\Ports\UserServiceInterface;
use SP\Modules\Web\Controllers\ControllerBase;
use SP\Modules\Web\Controllers\Traits\JsonTrait;
use SP\Mvc\Controller\ItemTrait;
use SP\Mvc\Controller\WebControllerHelper;
use SP\Mvc\View\Components\SelectItemAdapter;

/**
 * A base class for all classes that creates viewieable actions
 */
abstract class AuthTokenViewBase extends ControllerBase
{
    use JsonTrait, ItemTrait;

    private AuthTokenServiceInterface   $authTokenService;
    private CustomFieldServiceInterface $customFieldService;
    private UserServiceInterface        $userService;

    public function __construct(
        Application $application,
        WebControllerHelper $webControllerHelper,
        CustomFieldServiceInterface $customFieldService,
        UserServiceInterface $userService
    ) {
        parent::__construct($application, $webControllerHelper);

        $this->checkLoggedIn();

        $this->customFieldService = $customFieldService;
        $this->userService = $userService;
    }

    /**
     * Sets view data for displaying auth token's data
     *
     * @param  int|null  $authTokenId
     *
     * @throws ConstraintException
     * @throws QueryException
     * @throws SPException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    protected function setViewData(?int $authTokenId = null): void
    {
        $this->view->addTemplate('auth_token', 'itemshow');

        $authToken = $authTokenId
            ? $this->authTokenService->getById($authTokenId)
            : new AuthTokenData();

        $this->view->assign('authToken', $authToken);

        $this->view->assign(
            'users',
            SelectItemAdapter::factory($this->userService->getAllBasic())
                ->getItemsFromModelSelected([$authToken->getUserId()])
        );
        $this->view->assign(
            'actions',
            SelectItemAdapter::factory(AuthTokenService::getTokenActions())
                ->getItemsFromArraySelected([$authToken->getActionId()])
        );

        $this->view->assign(
            'nextAction',
            Acl::getActionRoute(ActionsInterface::ACCESS_MANAGE)
        );

        if ($this->view->isView === true) {
            $this->view->assign('disabled', 'disabled');
            $this->view->assign('readonly', 'readonly');
        } else {
            $this->view->assign('disabled', false);
            $this->view->assign('readonly', false);
        }

        $this->view->assign(
            'customFields',
            $this->getCustomFieldsForItem(ActionsInterface::AUTHTOKEN, $authTokenId, $this->customFieldService)
        );
    }
}
