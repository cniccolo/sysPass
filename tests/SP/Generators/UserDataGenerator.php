<?php
/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2023, Rubén Domínguez nuxsmin@$syspass.org
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

use SP\DataModel\UserData;
use SP\DataModel\UserPassData;
use SP\DataModel\UserPreferencesData;

/**
 * Class UserDataGenerator
 */
final class UserDataGenerator extends DataGenerator
{
    public function buildUserData(): UserData
    {
        return new UserData(array_merge($this->getUserProperties(), $this->getUserPassProperties()));
    }

    /**
     * @return array
     */
    private function getUserProperties(): array
    {
        return [
            'id'            => $this->faker->randomNumber(),
            'name'          => $this->faker->name,
            'email'         => $this->faker->randomNumber(),
            'login'         => $this->faker->name,
            'ssoLogin'      => $this->faker->userName,
            'notes'         => $this->faker->text,
            'userGroupId'   => $this->faker->randomNumber(),
            'userGroupName' => $this->faker->name,
            'userProfileId' => $this->faker->randomNumber(),
            'isAdminApp'    => $this->faker->boolean,
            'isAdminAcc'    => $this->faker->boolean,
            'isDisabled'    => $this->faker->boolean,
            'isChangePass'  => $this->faker->boolean,
            'isChangedPass' => $this->faker->boolean,
            'isLdap'        => $this->faker->boolean,
            'isMigrate'     => $this->faker->boolean,
            'loginCount'    => $this->faker->randomNumber(),
            'lastLogin'     => $this->faker->unixTime,
            'lastUpdate'    => $this->faker->unixTime,
            'preferences'   => serialize($this->buildUserPreferencesData()),
        ];
    }

    public function buildUserPreferencesData(): UserPreferencesData
    {
        return new UserPreferencesData($this->getUserPreferencesProperties());
    }

    private function getUserPreferencesProperties(): array
    {
        return [
            'lang'                     => $this->faker->languageCode,
            'theme'                    => $this->faker->colorName,
            'resultsPerPage'           => $this->faker->randomNumber(),
            'accountLink'              => $this->faker->boolean,
            'sortViews'                => $this->faker->boolean,
            'topNavbar'                => $this->faker->boolean,
            'optionalActions'          => $this->faker->boolean,
            'resultsAsCards'           => $this->faker->boolean,
            'checkNotifications'       => $this->faker->boolean,
            'showAccountSearchFilters' => $this->faker->boolean,
            'user_id'                  => $this->faker->randomNumber(),
        ];
    }

    /**
     * @return array
     */
    private function getUserPassProperties(): array
    {
        return [
            'id'              => $this->faker->randomNumber(),
            'pass'            => $this->faker->password,
            'hashSalt'        => $this->faker->sha1,
            'mPass'           => $this->faker->sha1,
            'mKey'            => $this->faker->sha1,
            'lastUpdateMPass' => $this->faker->dateTime->getTimestamp(),
        ];
    }

    public function buildUserPassData(): UserPassData
    {
        return new UserPassData($this->getUserPassProperties());
    }
}
