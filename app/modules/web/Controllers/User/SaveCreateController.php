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

namespace SP\Modules\Web\Controllers\User;


use Exception;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Core\Exceptions\ValidationException;
use SP\Http\JsonResponse;
use SP\Modules\Web\Controllers\Traits\JsonTrait;
use SP\Mvc\Controller\ItemTrait;

/**
 * Class SaveCreateController
 */
final class SaveCreateController extends UserSaveBase
{
    use JsonTrait, ItemTrait;

    /**
     * @return bool
     * @throws \JsonException
     */
    public function saveCreateAction()
    {
        try {
            if (!$this->acl->checkUserAccess(ActionsInterface::USER_CREATE)) {
                return $this->returnJsonResponse(
                    JsonResponse::JSON_ERROR,
                    __u('You don\'t have permission to do this operation')
                );
            }

            $this->form->validateFor(ActionsInterface::USER_CREATE);

            $itemData = $this->form->getItemData();

            $id = $this->userService->create($itemData);

            $this->eventDispatcher->notifyEvent(
                'create.user',
                new Event(
                    $this,
                    EventMessage::factory()
                        ->addDescription(__u('User added'))
                        ->addDetail(__u('User'), $itemData->getName())
                )
            );

            $this->addCustomFieldsForItem(ActionsInterface::USER, $id, $this->request, $this->customFieldService);

            $this->checkChangeUserPass($id, $itemData);

            return $this->returnJsonResponse(JsonResponse::JSON_SUCCESS, __u('User added'));
        } catch (ValidationException $e) {
            return $this->returnJsonResponseException($e);
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }
    }
}