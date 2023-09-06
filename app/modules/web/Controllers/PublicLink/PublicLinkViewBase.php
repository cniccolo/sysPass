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

namespace SP\Modules\Web\Controllers\PublicLink;


use SP\Core\Acl\Acl;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Application;
use SP\Core\Bootstrap\BootstrapWeb;
use SP\DataModel\PublicLinkListData;
use SP\Domain\Account\Ports\AccountServiceInterface;
use SP\Domain\Account\Ports\PublicLinkServiceInterface;
use SP\Domain\Account\Services\PublicLinkService;
use SP\Modules\Web\Controllers\ControllerBase;
use SP\Mvc\Controller\WebControllerHelper;
use SP\Mvc\View\Components\SelectItemAdapter;

/**
 * Class PublicLinkViewBase
 */
abstract class PublicLinkViewBase extends ControllerBase
{
    private PublicLinkServiceInterface $publicLinkService;
    private AccountServiceInterface    $accountService;

    public function __construct(
        Application $application,
        WebControllerHelper $webControllerHelper,
        PublicLinkServiceInterface $publicLinkService,
        AccountServiceInterface $accountService
    ) {
        parent::__construct($application, $webControllerHelper);

        $this->checkLoggedIn();

        $this->publicLinkService = $publicLinkService;
        $this->accountService = $accountService;
    }

    /**
     * Sets view data for displaying public link's data
     *
     * @param  int|null  $publicLinkId
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    protected function setViewData(?int $publicLinkId = null): void
    {
        $this->view->addTemplate('public_link', 'itemshow');

        $publicLink = $publicLinkId
            ? $this->publicLinkService->getById($publicLinkId)
            : new PublicLinkListData();

        $this->view->assign('publicLink', $publicLink);
        $this->view->assign('usageInfo', unserialize($publicLink->getUseInfo(), ['allowed_classes' => false]));
        $this->view->assign(
            'accounts',
            SelectItemAdapter::factory($this->accountService->getForUser())
                ->getItemsFromModelSelected([$publicLink->getItemId()])
        );

        $this->view->assign('nextAction', Acl::getActionRoute(ActionsInterface::ACCESS_MANAGE));

        if ($this->view->isView === true) {
            $baseUrl = ($this->configData->getApplicationUrl() ?: BootstrapWeb::$WEBURI).BootstrapWeb::$SUBURI;

            $this->view->assign('publicLinkURL', PublicLinkService::getLinkForHash($baseUrl, $publicLink->getHash()));
            $this->view->assign('disabled', 'disabled');
            $this->view->assign('readonly', 'readonly');
        } else {
            $this->view->assign('disabled', false);
            $this->view->assign('readonly', false);
        }
    }
}
