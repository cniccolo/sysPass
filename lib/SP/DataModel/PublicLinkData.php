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

namespace SP\DataModel;

use SP\Domain\Common\Adapters\DataModelInterface;
use SP\Domain\Common\Models\Model;

/**
 * Class PublicLinkData
 *
 * @package SP\DataModel
 */
class PublicLinkData extends Model implements DataModelInterface
{
    protected ?int    $id              = null;
    protected ?int    $itemId          = null;
    protected ?string $hash            = null;
    protected ?int    $userId          = null;
    protected ?int    $typeId          = null;
    protected ?bool   $notify          = null;
    protected ?int    $dateAdd         = null;
    protected ?int    $dateUpdate      = null;
    protected ?int    $dateExpire      = null;
    protected ?int    $countViews      = null;
    protected ?int    $totalCountViews = null;
    protected ?int    $maxCountViews   = null;
    protected ?string $useInfo         = null;
    protected ?string $data            = null;

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): void
    {
        $this->data = $data;
    }

    public function getId(): ?int
    {
        return (int)$this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }

    public function getItemId(): int
    {
        return (int)$this->itemId;
    }

    public function setItemId(int $itemId): void
    {
        $this->itemId = $itemId;
    }

    public function getUserId(): int
    {
        return (int)$this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): void
    {
        $this->typeId = $typeId;
    }

    public function isNotify(): bool
    {
        return $this->notify;
    }

    public function setNotify(bool $notify): void
    {
        $this->notify = $notify;
    }

    public function getDateAdd(): ?int
    {
        return $this->dateAdd;
    }

    public function setDateAdd(int $dateAdd): void
    {
        $this->dateAdd = $dateAdd;
    }

    public function getDateExpire(): ?int
    {
        return $this->dateExpire;
    }

    public function setDateExpire(int $dateExpire): void
    {
        $this->dateExpire = $dateExpire;
    }

    public function getCountViews(): ?int
    {
        return $this->countViews;
    }

    public function setCountViews(int $countViews): void
    {
        $this->countViews = $countViews;
    }

    public function addCountViews(): ?int
    {
        return $this->countViews++;
    }

    public function getMaxCountViews(): ?int
    {
        return $this->maxCountViews;
    }

    public function setMaxCountViews(int $maxCountViews): void
    {
        $this->maxCountViews = $maxCountViews;
    }

    public function getUseInfo(): ?string
    {
        return $this->useInfo;
    }

    public function setUseInfo(array $useInfo): void
    {
        $this->useInfo = serialize($useInfo);
    }

    public function getTotalCountViews(): ?int
    {
        return $this->totalCountViews;
    }

    public function getDateUpdate(): ?int
    {
        return $this->dateUpdate;
    }

    public function getName(): ?string
    {
        return null;
    }
}
