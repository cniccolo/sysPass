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

namespace SP\Modules\Api\Controllers\Account;

use Exception;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Domain\Account\Dtos\AccountRequest;
use SP\Domain\Api\Services\ApiResponse;

/**
 * Class EditPassController
 */
final class EditPassController extends AccountBase
{
    /**
     * viewPassAction
     */
    public function editPassAction(): void
    {
        try {
            $this->setupApi(ActionsInterface::ACCOUNT_EDIT_PASS);

            $accountRequest = $this->buildAccountRequest();

            $this->accountPresetService->checkPasswordPreset($accountRequest);

            $this->accountService->editPassword($accountRequest);

            $accountDetails = $this->accountService->getByIdEnriched($accountRequest->id)->getAccountVData();

            $this->eventDispatcher->notifyEvent(
                'edit.account.pass',
                new Event(
                    $this,
                    EventMessage::factory()
                        ->addDescription(__u('Password updated'))
                        ->addDetail(__u('Name'), $accountDetails->getName())
                        ->addDetail(__u('Client'), $accountDetails->getClientName())
                        ->addDetail('ID', $accountDetails->getId())
                )
            );

            $this->returnResponse(
                ApiResponse::makeSuccess($accountDetails, $accountRequest->id, __('Password updated'))
            );
        } catch (Exception $e) {
            processException($e);

            $this->returnResponseException($e);
        }
    }

    /**
     * @return \SP\Domain\Account\Dtos\AccountRequest
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    private function buildAccountRequest(): AccountRequest
    {
        $accountRequest = new AccountRequest();
        $accountRequest->id = $this->apiService->getParamInt('id', true);
        $accountRequest->pass = $this->apiService->getParamString('pass', true);
        $accountRequest->passDateChange = $this->apiService->getParamInt('expireDate');
        $accountRequest->userEditId = $this->context->getUserData()->getId();

        return $accountRequest;
    }
}
