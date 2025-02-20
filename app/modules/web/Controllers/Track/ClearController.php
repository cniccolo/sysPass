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

namespace SP\Modules\Web\Controllers\Track;


use Exception;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Acl\UnauthorizedActionException;
use SP\Core\Events\Event;
use SP\Core\Exceptions\SPException;
use SP\Http\JsonResponse;
use SP\Modules\Web\Controllers\Traits\JsonTrait;

/**
 * Class ClearController
 */
final class ClearController extends TrackBase
{
    use JsonTrait;

    /**
     * Clears tracks
     *
     * @return bool
     * @throws \JsonException
     */
    public function clearAction(): bool
    {
        try {
            if (!$this->acl->checkUserAccess(ActionsInterface::TRACK_CLEAR)) {
                throw new UnauthorizedActionException(SPException::ERROR);
            }

            $this->trackService->clear();

            $this->eventDispatcher->notifyEvent('clear.track', new Event($this));

            return $this->returnJsonResponse(JsonResponse::JSON_SUCCESS, __u('Tracks cleared out'));
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }
    }
}