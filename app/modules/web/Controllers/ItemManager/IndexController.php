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

namespace SP\Modules\Web\Controllers\ItemManager;

use SP\Core\Acl\Acl;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Application;
use SP\Core\Events\Event;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\ItemSearchData;
use SP\Domain\Account\Ports\AccountFileServiceInterface;
use SP\Domain\Account\Ports\AccountHistoryServiceInterface;
use SP\Domain\Account\Ports\AccountServiceInterface;
use SP\Domain\Category\Ports\CategoryServiceInterface;
use SP\Domain\Client\Ports\ClientServiceInterface;
use SP\Domain\CustomField\Ports\CustomFieldDefServiceInterface;
use SP\Domain\ItemPreset\Ports\ItemPresetServiceInterface;
use SP\Domain\Tag\Ports\TagServiceInterface;
use SP\Html\DataGrid\DataGridTab;
use SP\Modules\Web\Controllers\ControllerBase;
use SP\Modules\Web\Controllers\Helpers;
use SP\Modules\Web\Controllers\Helpers\Grid\AccountGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\AccountHistoryGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\CategoryGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\ClientGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\CustomFieldGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\FileGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\ItemPresetGrid;
use SP\Modules\Web\Controllers\Helpers\Grid\TagGrid;
use SP\Modules\Web\Controllers\Helpers\TabsGridHelper;
use SP\Mvc\Controller\WebControllerHelper;

/**
 * Class ItemManagerController
 *
 * @package SP\Modules\Web\Controllers
 */
final class IndexController extends ControllerBase
{
    protected ?ItemSearchData              $itemSearchData = null;
    private TabsGridHelper                 $tabsGridHelper;
    private CategoryServiceInterface       $categoryService;
    private TagServiceInterface            $tagService;
    private ClientServiceInterface         $clientService;
    private CustomFieldDefServiceInterface $customFieldDefService;
    private AccountFileServiceInterface    $accountFileService;
    private AccountServiceInterface        $accountService;
    private AccountHistoryServiceInterface $accountHistoryService;
    private ItemPresetServiceInterface     $itemPresetService;
    private CategoryGrid                   $categoryGrid;
    private TagGrid                        $tagGrid;
    private ClientGrid                     $clientGrid;
    private CustomFieldGrid                $customFieldGrid;
    private FileGrid                       $fileGrid;
    private AccountGrid                    $accountGrid;
    private AccountHistoryGrid             $accountHistoryGrid;
    private ItemPresetGrid                 $itemPresetGrid;

    public function __construct(
        Application $application,
        WebControllerHelper $webControllerHelper,
        Helpers\TabsGridHelper $tabsGridHelper,
        CategoryServiceInterface $categoryService,
        TagServiceInterface $tagService,
        ClientServiceInterface $clientService,
        CustomFieldDefServiceInterface $customFieldDefService,
        AccountFileServiceInterface $accountFileService,
        AccountServiceInterface $accountService,
        AccountHistoryServiceInterface $accountHistoryService,
        ItemPresetServiceInterface $itemPresetService,
        Helpers\Grid\CategoryGrid $categoryGrid,
        Helpers\Grid\TagGrid $tagGrid,
        Helpers\Grid\ClientGrid $clientGrid,
        Helpers\Grid\CustomFieldGrid $customFieldGrid,
        Helpers\Grid\FileGrid $fileGrid,
        Helpers\Grid\AccountGrid $accountGrid,
        Helpers\Grid\AccountHistoryGrid $accountHistoryGrid,
        Helpers\Grid\ItemPresetGrid $itemPresetGrid
    ) {
        $this->tabsGridHelper = $tabsGridHelper;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
        $this->clientService = $clientService;
        $this->customFieldDefService = $customFieldDefService;
        $this->accountFileService = $accountFileService;
        $this->accountService = $accountService;
        $this->accountHistoryService = $accountHistoryService;
        $this->itemPresetService = $itemPresetService;
        $this->categoryGrid = $categoryGrid;
        $this->tagGrid = $tagGrid;
        $this->clientGrid = $clientGrid;
        $this->customFieldGrid = $customFieldGrid;
        $this->fileGrid = $fileGrid;
        $this->accountGrid = $accountGrid;
        $this->accountHistoryGrid = $accountHistoryGrid;
        $this->itemPresetGrid = $itemPresetGrid;

        parent::__construct($application, $webControllerHelper);

        $this->checkLoggedIn();
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
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    protected function getGridTabs(): void
    {
        $this->itemSearchData = new ItemSearchData(null, 0, $this->configData->getAccountCount());

        if ($this->checkAccess(ActionsInterface::CATEGORY)) {
            $this->tabsGridHelper->addTab($this->getCategoriesList());
        }

        if ($this->checkAccess(ActionsInterface::TAG)) {
            $this->tabsGridHelper->addTab($this->getTagsList());
        }

        if ($this->checkAccess(ActionsInterface::CLIENT)) {
            $this->tabsGridHelper->addTab($this->getClientsList());
        }

        if ($this->checkAccess(ActionsInterface::CUSTOMFIELD)) {
            $this->tabsGridHelper->addTab($this->getCustomFieldsList());
        }

        if ($this->configData->isFilesEnabled()
            && $this->checkAccess(ActionsInterface::FILE)) {
            $this->tabsGridHelper->addTab($this->getAccountFilesList());
        }

        if ($this->checkAccess(ActionsInterface::ACCOUNTMGR)) {
            $this->tabsGridHelper->addTab($this->getAccountsList());
        }

        if ($this->checkAccess(ActionsInterface::ACCOUNTMGR_HISTORY)) {
            $this->tabsGridHelper->addTab($this->getAccountsHistoryList());
        }

        if ($this->checkAccess(ActionsInterface::ITEMPRESET)) {
            $this->tabsGridHelper->addTab($this->getItemPresetList());
        }

        $this->eventDispatcher->notifyEvent(
            'show.itemlist.items',
            new Event($this)
        );

        $this->tabsGridHelper->renderTabs(
            Acl::getActionRoute(ActionsInterface::ITEMS_MANAGE),
            $this->request->analyzeInt('tabIndex', 0)
        );

        $this->view();
    }

    /**
     * Returns categories' data tab
     *
     * @return \SP\Html\DataGrid\DataGridTab
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    protected function getCategoriesList(): DataGridTab
    {
        return $this->categoryGrid->getGrid($this->categoryService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns tags' data tab
     *
     * @return DataGridTab
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    protected function getTagsList(): DataGridTab
    {
        return $this->tagGrid->getGrid($this->tagService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns clients' data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getClientsList(): DataGridTab
    {
        return $this->clientGrid->getGrid($this->clientService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns custom fields' data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getCustomFieldsList(): DataGridTab
    {
        return $this->customFieldGrid->getGrid($this->customFieldDefService->search($this->itemSearchData))
            ->updatePager();
    }

    /**
     * Returns account files' data tab
     *
     * @return DataGridTab
     * @throws ConstraintException
     * @throws QueryException
     */
    protected function getAccountFilesList(): DataGridTab
    {
        return $this->fileGrid->getGrid($this->accountFileService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns accounts' data tab
     *
     * @return DataGridTab
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    protected function getAccountsList(): DataGridTab
    {
        return $this->accountGrid->getGrid($this->accountService->search($this->itemSearchData))->updatePager();
    }

    /**
     * Returns accounts' history data tab
     *
     * @return DataGridTab
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    protected function getAccountsHistoryList(): DataGridTab
    {
        return $this->accountHistoryGrid->getGrid($this->accountHistoryService->search($this->itemSearchData))
            ->updatePager();
    }

    /**
     * Returns API tokens data tab
     *
     * @return DataGridTab
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    protected function getItemPresetList(): DataGridTab
    {
        return $this->itemPresetGrid->getGrid($this->itemPresetService->search($this->itemSearchData))->updatePager();
    }

    /**
     * @return TabsGridHelper
     */
    public function getTabsGridHelper(): TabsGridHelper
    {
        return $this->tabsGridHelper;
    }
}
