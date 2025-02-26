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

namespace SP\Modules\Api\Controllers\UserGroup;


use Exception;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\DataModel\UserGroupData;
use SP\Domain\Api\Services\ApiResponse;

/**
 * Class EditController
 */
final class EditController extends UserGroupBase
{
    /**
     * editAction
     */
    public function editAction(): void
    {
        try {
            $this->setupApi(ActionsInterface::GROUP_EDIT);

            $userGroupData = $this->buildUserGroupData();

            $this->userGroupService->update($userGroupData);

            $this->eventDispatcher->notifyEvent(
                'edit.userGroup',
                new Event(
                    $this,
                    EventMessage::factory()
                        ->addDescription(__u('Group updated'))
                        ->addDetail(__u('Name'), $userGroupData->getName())
                        ->addDetail('ID', $userGroupData->getId())
                        ->addExtra('userGroupId', $userGroupData->getId())
                )
            );

            $this->returnResponse(
                ApiResponse::makeSuccess($userGroupData, $userGroupData->getId(), __('Group updated'))
            );
        } catch (Exception $e) {
            processException($e);

            $this->returnResponseException($e);
        }
    }

    /**
     * @return \SP\DataModel\UserGroupData
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    private function buildUserGroupData(): UserGroupData
    {
        $userGroupData = new UserGroupData();
        $userGroupData->setId($this->apiService->getParamInt('id', true));
        $userGroupData->setName($this->apiService->getParamString('name', true));
        $userGroupData->setDescription($this->apiService->getParamString('description'));
        $userGroupData->setUsers($this->apiService->getParamArray('usersId'));

        return $userGroupData;
    }
}