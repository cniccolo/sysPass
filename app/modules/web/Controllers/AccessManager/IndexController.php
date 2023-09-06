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

namespace SP\Modules\Web\Controllers\AccessManager;

use SP\Core\Acl\Acl;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Application;
use SP\Core\Events\Event;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\ItemSearchData;
use SP\Domain\Account\Ports\PublicLinkServiceInterface;
use SP\Domain\Auth\Ports\AuthTokenServiceInterface;
use SP\Domain\User\Ports\UserGroupServiceInterface;
use SP\Domain\User\Ports\UserProfileServiceInterface;
use SP\Domain\User\Ports\UserServiceInterface;
use SP\Html\DataGrid\DataGridTab;
use SP\Modules\Web\Controllers\ControllerBase;
use SP\Modules\Web\Controllers\Helpers\Grid\AuthTokenGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\PublicLinkGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\UserGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\UserGroupGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\UserProfileGrid;
use SP\Modules\Web\Controllers\Helpers\TabsGridHelper;
use SP\Mvc\Controller\WebControllerHelper;

/**
 * Class IndexController
 *
 * @package SP\Modules\Web\Controllers
 */
final class IndexController extends ControllerBase
{
    protected ItemSearchData            $itemSearchData;
    protected TabsGridHelper            $tabsGridHelper;
    private UserGrid                    $userGrid;
    private UserGroupGrid               $userGroupGrid;
    private UserProfileGrid             $userProfileGrid;
    private AuthTokenGrid               $authTokenGrid;
    private PublicLinkGrid              $publicLinkGrid;
    private UserServiceInterface        $userService;
    private UserGroupServiceInterface   $userGroupService;
    private UserProfileServiceInterface $userProfileService;
    private AuthTokenServiceInterface   $authTokenService;
    private PublicLinkServiceInterface  $publicLinkService;

    public function __construct(
        Application $application,
        WebControllerHelper $webControllerHelper,
        TabsGridHelper $tabsGridHelper,
        UserGrid $userGrid,
        UserGroupGrid $userGroupGrid,
        UserProfileGrid $userProfileGrid,
        AuthTokenGrid $authTokenGrid,
        PublicLinkGrid $publicLinkGrid,
        UserServiceInterface $userService,
        UserGroupServiceInterface $userGroupService,
        UserProfileServiceInterface $userProfileService,
        AuthTokenServiceInterface $authTokenService,
        PublicLinkServiceInterface $publicLinkService
    ) {
        parent::__construct($application, $webControllerHelper);

        $this->checkLoggedIn();

        $this->userGrid = $userGrid;
        $this->userGroupGrid = $userGroupGrid;
        $this->userProfileGrid = $userProfileGrid;
        $this->authTokenGrid = $authTokenGrid;
        $this->publicLinkGrid = $publicLinkGrid;
        $this->userService = $userService;
        $this->userGroupService = $userGroupService;
        $this->userProfileService = $userProfileService;
        $this->authTokenService = $authTokenService;
        $this->publicLinkService = $publicLinkService;
        $this->tabsGridHelper = $tabsGridHelper;
        $this->itemSearchData = new ItemSearchData();
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function indexAction(): void
    {
        $this->getGridTabs();
    }

    /**
     * Returns a tabbed grid with items
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getGridTabs(): void
    {
        $this->itemSearchData->setLimitCount($this->configData->getAccountCount());

        if ($this->checkAccess(ActionsInterface::USER)) {
            $this->tabsGridHelper->addTab($this->getUsersList());
        }

        if ($this->checkAccess(ActionsInterface::GROUP)) {
            $this->tabsGridHelper->addTab($this->getUsersGroupList());
        }

        if ($this->checkAccess(ActionsInterface::PROFILE)) {
            $this->tabsGridHelper->addTab($this->getUsersProfileList());
        }

        if ($this->checkAccess(ActionsInterface::AUTHTOKEN)) {
            $this->tabsGridHelper->addTab($this->getAuthTokensList());
        }

        if ($this->configData->isPublinksEnabled()
            && $this->checkAccess(ActionsInterface::PUBLICLINK)) {
            $this->tabsGridHelper->addTab($this->getPublicLinksList());
        }

        $this->eventDispatcher->notifyEvent(
            'show.itemlist.accesses',
            new Event($this)
        );

        $this->tabsGridHelper->renderTabs(
            Acl::getActionRoute(ActionsInterface::ACCESS_MANAGE),
            $this->request->analyzeInt('tabIndex', 0)
        );

        $this->view();
    }

    /**
     * Returns users' data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getUsersList(): DataGridTab
    {
        return $this->userGrid->getGrid($this->userService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns users group data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getUsersGroupList(): DataGridTab
    {
        return $this->userGroupGrid->getGrid($this->userGroupService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns users profile data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getUsersProfileList(): DataGridTab
    {
        return $this->userProfileGrid->getGrid($this->userProfileService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns API tokens data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getAuthTokensList(): DataGridTab
    {
        return $this->authTokenGrid->getGrid($this->authTokenService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns public links data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getPublicLinksList(): DataGridTab
    {
        return $this->publicLinkGrid->getGrid($this->publicLinkService->search($this->itemSearchData))->updatePager();
    }

    /**
     * @return TabsGridHelper
     */
    public function getTabsGridHelper(): TabsGridHelper
    {
        return $this->tabsGridHelper;
    }
}
