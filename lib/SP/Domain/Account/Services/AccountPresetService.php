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

namespace SP\Domain\Account\Services;

use SP\Core\Application;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\NoSuchPropertyException;
use SP\Core\Exceptions\QueryException;
use SP\Core\Exceptions\ValidationException;
use SP\DataModel\ItemPreset\AccountPermission;
use SP\DataModel\ItemPreset\Password;
use SP\Domain\Account\Dtos\AccountDto;
use SP\Domain\Account\Ports\AccountPresetServiceInterface;
use SP\Domain\Account\Ports\AccountToUserGroupRepositoryInterface;
use SP\Domain\Account\Ports\AccountToUserRepositoryInterface;
use SP\Domain\Common\Services\Service;
use SP\Domain\Config\Ports\ConfigDataInterface;
use SP\Domain\ItemPreset\Ports\ItemPresetInterface;
use SP\Domain\ItemPreset\Ports\ItemPresetServiceInterface;
use SP\Mvc\Controller\Validators\ValidatorInterface;

/**
 * Class AccountPreset
 *
 * @package SP\Domain\Account\Services
 */
final class AccountPresetService extends Service implements AccountPresetServiceInterface
{
    public function __construct(
        Application $application,
        private ItemPresetServiceInterface $itemPresetService,
        private AccountToUserGroupRepositoryInterface $accountToUserGroupRepository,
        private AccountToUserRepositoryInterface $accountToUserRepository,
        private ConfigDataInterface $configData,
        private ValidatorInterface $validator
    ) {
        parent::__construct($application);
    }

    /**
     * @throws ValidationException
     * @throws ConstraintException
     * @throws NoSuchPropertyException
     * @throws QueryException
     */
    public function checkPasswordPreset(AccountDto $accountDto): AccountDto
    {
        $itemPreset = $this->itemPresetService->getForCurrentUser(ItemPresetInterface::ITEM_TYPE_ACCOUNT_PASSWORD);

        if ($itemPreset !== null && $itemPreset->getFixed() === 1) {
            $passwordPreset = $itemPreset->hydrate(Password::class);

            $this->validator->validate($passwordPreset, $accountDto->getPass());

            if ($this->configData->isAccountExpireEnabled()) {
                $expireTimePreset = $passwordPreset->getExpireTime();

                if ($expireTimePreset > 0
                    && ($accountDto->getPassDateChange() === 0
                        || $accountDto->getPassDateChange() < time() + $expireTimePreset)
                ) {
                    return $accountDto->withPassDateChange(time() + $expireTimePreset);
                }
            }
        }

        return $accountDto;
    }

    /**
     * @throws QueryException
     * @throws ConstraintException
     * @throws NoSuchPropertyException
     */
    public function addPresetPermissions(int $accountId): void
    {
        $itemPresetData =
            $this->itemPresetService->getForCurrentUser(ItemPresetInterface::ITEM_TYPE_ACCOUNT_PERMISSION);

        if ($itemPresetData !== null && $itemPresetData->getFixed()) {
            $userData = $this->context->getUserData();
            $accountPermission = $itemPresetData->hydrate(AccountPermission::class);

            $usersView = array_diff($accountPermission->getUsersView(), [$userData->getId()]);
            $usersEdit = array_diff($accountPermission->getUsersEdit(), [$userData->getId()]);
            $userGroupsView = array_diff($accountPermission->getUserGroupsView(), [$userData->getUserGroupId()]);
            $userGroupsEdit = array_diff($accountPermission->getUserGroupsEdit(), [$userData->getUserGroupId()]);

            if (count($usersView) > 0) {
                $this->accountToUserRepository->addByType($accountId, $usersView);
            }

            if (count($usersEdit) > 0) {
                $this->accountToUserRepository->addByType($accountId, $usersEdit, true);
            }

            if (count($userGroupsView) > 0) {
                $this->accountToUserGroupRepository->addByType($accountId, $userGroupsView);
            }

            if (count($userGroupsEdit) > 0) {
                $this->accountToUserGroupRepository->addByType($accountId, $userGroupsEdit, true);
            }
        }
    }
}
