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

namespace SP\DataModel\ItemPreset;

/**
 * Class AccountPrivate
 *
 * @package SP\DataModel
 */
final class AccountPrivate
{
    /**
     * @param  bool|null  $privateUser
     * @param  bool|null  $privateGroup
     */
    public function __construct(private ?bool $privateUser = false, private ?bool $privateGroup = false)
    {
        $this->privateUser = $privateUser;
        $this->privateGroup = $privateGroup;
    }

    public function isPrivateUser(): bool
    {
        return $this->privateUser;
    }

    public function isPrivateGroup(): bool
    {
        return $this->privateGroup;
    }
}
