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

namespace SP\Domain\Account\Dtos;

use SP\DataModel\AccountSearchVData;
use SP\DataModel\ItemData;
use SP\Domain\Account\Models\AccountSearchView;
use SP\Domain\Common\Dtos\ItemDataTrait;

/**
 * Class AccountAclDto
 *
 * @package SP\DataModel\Dto
 */
final class AccountAclDto
{
    use ItemDataTrait;

    /**
     * @param  int  $accountId
     * @param  int  $userId
     * @param  ItemData[]  $usersId
     * @param  int  $userGroupId
     * @param  ItemData[]  $userGroupsId
     * @param  int  $dateEdit
     */
    public function __construct(
        private int $accountId,
        private int $userId,
        private array $usersId,
        private int $userGroupId,
        private array $userGroupsId,
        private int $dateEdit
    ) {
        $this->usersId = self::buildFromItemData($usersId);
        $this->userGroupsId = self::buildFromItemData($userGroupsId);
    }

    /**
     * @param  AccountEnrichedDto  $accountDetailsResponse
     *
     * @return AccountAclDto
     */
    public static function makeFromAccount(AccountEnrichedDto $accountDetailsResponse): AccountAclDto
    {
        return new self(
            $accountDetailsResponse->getId(),
            $accountDetailsResponse->getAccountDataView()->getUserId(),
            $accountDetailsResponse->getUsers(),
            $accountDetailsResponse->getAccountDataView()->getUserGroupId(),
            $accountDetailsResponse->getUserGroups(),
            strtotime($accountDetailsResponse->getAccountDataView()->getDateEdit())
        );
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserGroupId(): int
    {
        return $this->userGroupId;
    }

    public function getDateEdit(): int
    {
        return $this->dateEdit;
    }

    /**
     * @param  AccountSearchView  $accountSearchView
     *
     * @param  array  $users
     * @param  array  $userGroups
     *
     * @return AccountAclDto
     */
    public static function makeFromAccountSearch(
        AccountSearchView $accountSearchView,
        array $users,
        array $userGroups
    ): AccountAclDto {
        return new self(
            $accountSearchView->getId(),
            $accountSearchView->getUserId(),
            $users,
            $accountSearchView->getUserGroupId(),
            $userGroups,
            strtotime($accountSearchView->getDateEdit())
        );
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @return ItemData[]
     */
    public function getUsersId(): array
    {
        return $this->usersId;
    }

    /**
     * @return ItemData[]
     */
    public function getUserGroupsId(): array
    {
        return $this->userGroupsId;
    }
}
