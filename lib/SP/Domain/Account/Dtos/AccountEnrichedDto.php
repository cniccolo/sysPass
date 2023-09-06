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

use SP\DataModel\ItemData;
use SP\Domain\Account\Models\AccountDataView;
use SP\Domain\Account\Models\AccountSearchView;
use SP\Domain\Common\Dtos\ItemDataTrait;

/**
 * Class AccountEnrichedDto
 */
class AccountEnrichedDto
{
    use ItemDataTrait;

    private int $id;
    /**
     * @var ItemData[] Los usuarios secundarios de la cuenta.
     */
    private array $users = [];
    /**
     * @var ItemData[] Los grupos secundarios de la cuenta.
     */
    private array $userGroups = [];
    /**
     * @var ItemData[] Las etiquetas de la cuenta.
     */
    private array $tags = [];

    /**
     * AccountDetailsResponse constructor.
     *
     * @param  \SP\Domain\Account\Models\AccountDataView  $accountDataView
     */
    public function __construct(private AccountDataView $accountDataView)
    {
        $this->id = $accountDataView->getId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  ItemData[]  $users
     *
     * @return \SP\Domain\Account\Dtos\AccountEnrichedDto
     */
    public function withUsers(array $users): AccountEnrichedDto
    {
        $self = clone $this;
        $self->users = self::buildFromItemData($users);

        return $self;
    }

    /**
     * @param  ItemData[]  $groups
     *
     * @return \SP\Domain\Account\Dtos\AccountEnrichedDto
     */
    public function withUserGroups(array $groups): AccountEnrichedDto
    {
        $self = clone $this;
        $self->userGroups = self::buildFromItemData($groups);

        return $self;
    }

    /**
     * @param  ItemData[]  $tags
     *
     * @return \SP\Domain\Account\Dtos\AccountEnrichedDto
     */
    public function withTags(array $tags): AccountEnrichedDto
    {
        $self = clone $this;
        $self->tags = self::buildFromItemData($tags);

        return $self;
    }

    /**
     * @return ItemData[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @return ItemData[]
     */
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    /**
     * @return ItemData[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getAccountDataView(): AccountDataView
    {
        return $this->accountDataView;
    }
}
