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

namespace SP\Modules\Web\Controllers\Plugin;


use Exception;
use SP\Core\Acl\Acl;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Application;
use SP\Core\Events\Event;
use SP\Domain\Plugin\Ports\PluginServiceInterface;
use SP\Http\JsonResponse;
use SP\Infrastructure\Plugin\Repositories\PluginModel;
use SP\Modules\Web\Controllers\ControllerBase;
use SP\Modules\Web\Controllers\Traits\JsonTrait;
use SP\Mvc\Controller\WebControllerHelper;
use SP\Plugin\PluginManager;

/**
 * Class ViewController
 */
final class ViewController extends ControllerBase
{
    use JsonTrait;

    private PluginServiceInterface $pluginService;
    private PluginManager          $pluginManager;

    public function __construct(
        Application $application,
        WebControllerHelper $webControllerHelper,
        PluginServiceInterface $pluginService,
        PluginManager $pluginManager
    ) {
        parent::__construct($application, $webControllerHelper);

        $this->checkLoggedIn();

        $this->pluginService = $pluginService;
        $this->pluginManager = $pluginManager;
    }

    /**
     * View action
     *
     * @param  int  $id
     *
     * @return bool
     * @throws \JsonException
     */
    public function viewAction(int $id): bool
    {
        try {
            if (!$this->acl->checkUserAccess(ActionsInterface::PLUGIN_VIEW)) {
                return $this->returnJsonResponse(
                    JsonResponse::JSON_ERROR,
                    __u('You don\'t have permission to do this operation')
                );
            }

            $this->view->assign('header', __('View Plugin'));
            $this->view->assign('isView', true);

            $this->setViewData($id);

            $this->eventDispatcher->notifyEvent('show.plugin', new Event($this));

            return $this->returnJsonResponseData(['html' => $this->render()]);
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }
    }

    /**
     * Sets view data for displaying items's data
     *
     * @param  int|null  $pluginId
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    protected function setViewData(?int $pluginId = null): void
    {
        $this->view->addTemplate('plugin');

        $pluginData = $pluginId
            ? $this->pluginService->getById($pluginId)
            : new PluginModel();
        $pluginInfo = $this->pluginManager->getPlugin($pluginData->getName());

        $this->view->assign('plugin', $pluginData);
        $this->view->assign('pluginInfo', $pluginInfo);

        $this->view->assign('nextAction', Acl::getActionRoute(ActionsInterface::ITEMS_MANAGE));

        if ($this->view->isView === true) {
            $this->view->assign('disabled', 'disabled');
            $this->view->assign('readonly', 'readonly');
        } else {
            $this->view->assign('disabled', false);
            $this->view->assign('readonly', false);
        }
    }
}
