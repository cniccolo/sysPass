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

namespace SP\Tests\Generators;

use SP\DataModel\AccountVData;
use SP\DataModel\ItemData;
use SP\Domain\Account\Dtos\AccountEnrichedDto;
use SP\Domain\Common\Adapters\SimpleModel;

/**
 * Class AccountDataGenerator
 */
final class AccountDataGenerator extends DataGenerator
{
    public function buildAccountEnrichedData(): AccountEnrichedDto
    {
        $out = new AccountEnrichedDto(AccountVData::buildFromSimpleModel($this->buildAccountData()));
        $out->setUsers($this->buildItemData());
        $out->setTags($this->buildItemData());
        $out->setUserGroups($this->buildItemData());

        return $out;
    }

    public function buildAccountData(): SimpleModel
    {
        return new SimpleModel([
            'id'                 => $this->faker->randomNumber(),
            'name'               => $this->faker->name,
            'clientId'           => $this->faker->randomNumber(),
            'clientName'         => $this->faker->name,
            'categoryId'         => $this->faker->randomNumber(),
            'categoryName'       => $this->faker->name,
            'userId'             => $this->faker->randomNumber(),
            'userName'           => $this->faker->userName,
            'userLogin'          => $this->faker->name,
            'userGroupId'        => $this->faker->randomNumber(),
            'userGroupName'      => $this->faker->name,
            'userEditId'         => $this->faker->randomNumber(),
            'userEditName'       => $this->faker->userName,
            'userEditLogin'      => $this->faker->name,
            'login'              => $this->faker->name,
            'url'                => $this->faker->url,
            'notes'              => $this->faker->text,
            'otherUserEdit'      => $this->faker->boolean,
            'otherUserGroupEdit' => $this->faker->boolean,
            'dateAdd'            => $this->faker->unixTime,
            'dateEdit'           => $this->faker->unixTime,
            'countView'          => $this->faker->randomNumber(),
            'countDecrypt'       => $this->faker->randomNumber(),
            'isPrivate'          => $this->faker->boolean,
            'isPrivateGroup'     => $this->faker->boolean,
            'passDate'           => $this->faker->unixTime,
            'passDateChange'     => $this->faker->unixTime,
            'parentId'           => $this->faker->randomNumber(),
            'publicLinkHash'     => $this->faker->sha1,
            'pass'               => $this->faker->password,
            'key'                => $this->faker->sha1,
        ]);
    }

    /**
     * @return ItemData[]
     */
    public function buildItemData(): array
    {
        return array_map(
            fn() => new ItemData(['id' => $this->faker->randomNumber(), 'name' => $this->faker->name]),
            range(0, 9)
        );
    }

    public function buildAccountHistoryData(): SimpleModel
    {
        return new SimpleModel([
            'id'             => $this->faker->randomNumber(),
            'accountId'      => $this->faker->randomNumber(),
            'name'           => $this->faker->name,
            'login'          => $this->faker->userName,
            'url'            => $this->faker->url,
            'notes'          => $this->faker->text,
            'userEditId'     => $this->faker->randomNumber(),
            'passDateChange' => $this->faker->unixTime,
            'clientId'       => $this->faker->randomNumber(),
            'categoryId'     => $this->faker->randomNumber(),
            'isPrivate'      => $this->faker->numberBetween(0, 1),
            'isPrivateGroup' => $this->faker->numberBetween(0, 1),
            'parentId'       => $this->faker->randomNumber(),
            'userId'         => $this->faker->randomNumber(),
            'userGroupId'    => $this->faker->randomNumber(),
            'key'            => $this->faker->text,
            'pass'           => $this->faker->text,
        ]);
    }
}
