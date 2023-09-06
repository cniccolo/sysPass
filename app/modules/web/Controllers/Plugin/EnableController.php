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
use SP\Core\Application;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Domain\Plugin\Ports\PluginServiceInterface;
use SP\Http\JsonResponse;
use SP\Modules\Web\Controllers\ControllerBase;
use SP\Modules\Web\Controllers\Traits\JsonTrait;
use SP\Mvc\Controller\WebControllerHelper;

/**
 * Class EnableController
 */
final class EnableController extends ControllerBase
{
    use JsonTrait;

    private \SP\Domain\Plugin\Ports\PluginServiceInterface $pluginService;

    public function __construct(
        Application $application,
        WebControllerHelper $webControllerHelper,
        PluginServiceInterface $pluginService
    ) {
        parent::__construct($application, $webControllerHelper);

        $this->checkLoggedIn();

        $this->pluginService = $pluginService;
    }

    /**
     * enableAction
     *
     * @param  int  $id
     *
     * @return bool
     * @throws \JsonException
     */
    public function enableAction(int $id): bool
    {
        try {
            $this->pluginService->toggleEnabled($id, true);

            $this->eventDispatcher->notifyEvent(
                'edit.plugin.enable',
                new Event($this, EventMessage::factory()->addDescription(__u('Plugin enabled')))
            );

            return $this->returnJsonResponse(JsonResponse::JSON_SUCCESS, __u('Plugin enabled'));
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }
    }
}
