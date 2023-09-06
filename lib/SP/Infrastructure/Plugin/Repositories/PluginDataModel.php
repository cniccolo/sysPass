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

namespace SP\Infrastructure\Plugin\Repositories;

use SP\DataModel\EncryptedModel;
use SP\Domain\Common\Adapters\HydratableInterface;
use SP\Domain\Common\Models\SerializedModel;

/**
 * Class PluginData
 *
 * @package SP\Infrastructure\Plugin\Repositories
 */
final class PluginDataModel implements HydratableInterface
{
    use SerializedModel;
    use EncryptedModel;

    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $itemId;
    /**
     * @var string
     */
    private $data;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return (int)$this->itemId;
    }

    /**
     * @param  int  $itemId
     */
    public function setItemId(int $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param  string  $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }
}
