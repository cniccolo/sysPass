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

namespace SP\Modules\Web\Controllers\CustomField;


use Exception;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Core\Exceptions\ValidationException;
use SP\Http\JsonResponse;
use SP\Modules\Web\Controllers\Traits\JsonTrait;

/**
 * Class SaveCreateController
 */
final class SaveCreateController extends CustomFieldSaveBase
{
    use JsonTrait;

    /**
     * @return bool
     * @throws \JsonException
     */
    public function saveCreateAction(): bool
    {
        try {
            if (!$this->acl->checkUserAccess(ActionsInterface::CUSTOMFIELD_CREATE)) {
                return $this->returnJsonResponse(
                    JsonResponse::JSON_ERROR,
                    __u('You don\'t have permission to do this operation')
                );
            }

            $this->form->validateFor(ActionsInterface::CUSTOMFIELD_CREATE);

            $itemData = $this->form->getItemData();

            $this->customFieldDefService->create($itemData);

            $this->eventDispatcher->notifyEvent(
                'create.customField',
                new Event(
                    $this,
                    EventMessage::factory()
                        ->addDescription(__u('Field added'))
                        ->addDetail(__u('Field'), $itemData->getName())
                )
            );

            return $this->returnJsonResponse(
                JsonResponse::JSON_SUCCESS,
                __u('Field added')
            );
        } catch (ValidationException $e) {
            return $this->returnJsonResponseException($e);
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }
    }
}