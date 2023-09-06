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

namespace SP\Domain\Account\Adapters;

use SP\Domain\Common\Adapters\DataModelInterface;
use SP\Domain\Common\Models\Model;

/**
 * Class AccountData
 */
class AccountData extends Model implements DataModelInterface
{
    protected ?int    $id                 = null;
    protected ?int    $userId             = null;
    protected ?int    $userGroupId        = null;
    protected ?int    $userEditId         = null;
    protected ?string $name               = null;
    protected ?int    $clientId           = null;
    protected ?int    $categoryId         = null;
    protected ?string $login              = null;
    protected ?string $url                = null;
    protected ?string $pass               = null;
    protected ?string $key                = null;
    protected ?string $notes              = null;
    protected ?int    $dateAdd            = 0;
    protected ?int    $dateEdit           = 0;
    protected ?int    $countView          = 0;
    protected ?int    $countDecrypt       = 0;
    protected ?int    $isPrivate          = 0;
    protected ?int    $isPrivateGroup     = 0;
    protected ?int    $passDate           = 0;
    protected ?int    $passDateChange     = 0;
    protected ?int    $parentId           = 0;
    protected ?bool   $otherUserGroupEdit = false;
    protected ?bool   $otherUserEdit      = false;

    public function getDateAdd(): ?int
    {
        return $this->dateAdd;
    }

    public function getDateEdit(): ?int
    {
        return $this->dateEdit;
    }

    public function getUserEditId(): ?int
    {
        return $this->userEditId;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(string $pass): void
    {
        $this->pass = $pass;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUserGroupId(): ?int
    {
        return $this->userGroupId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCountView(): ?int
    {
        return $this->countView;
    }

    public function getCountDecrypt(): ?int
    {
        return $this->countDecrypt;
    }

    public function getIsPrivate(): ?int
    {
        return $this->isPrivate;
    }

    public function getPassDate(): ?int
    {
        return $this->passDate;
    }

    public function getPassDateChange(): ?int
    {
        return $this->passDateChange;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getIsPrivateGroup(): ?int
    {
        return $this->isPrivateGroup;
    }

    public function getOtherUserGroupEdit(): ?bool
    {
        return $this->otherUserGroupEdit;
    }

    public function getOtherUserEdit(): ?bool
    {
        return $this->otherUserEdit;
    }
}
