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

namespace SP\Domain\Common\Models;

use SP\Core\Exceptions\NoSuchPropertyException;
use SP\Util\Util;

/**
 * Trait Datamodel
 *
 * @package SP\DataModel
 */
trait SerializedModel
{
    /**
     * @param  string|null  $class
     * @param  string  $property
     *
     * @return mixed|null
     * @throws NoSuchPropertyException
     */
    public function hydrate(?string $class = null, string $property = 'data'): mixed
    {
        if (property_exists($this, $property)) {
            if ($this->{$property} === null) {
                return null;
            }

            if ($class !== null) {
                return Util::unserialize($class, $this->{$property});
            }

            /** @noinspection UnserializeExploitsInspection */
            return unserialize($this->{$property});
        }

        throw new NoSuchPropertyException($property);
    }
}
