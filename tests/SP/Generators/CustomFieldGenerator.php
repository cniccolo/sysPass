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

use SP\Domain\Common\Models\Simple;

/**
 * Class CustomFieldGenerator
 */
final class CustomFieldGenerator extends DataGenerator
{
    public function buildSimpleModel(bool $useEncryption = false): Simple
    {
        $data = null;
        $key = null;

        if ($useEncryption) {
            $data = $this->faker->text;
            $key = $this->faker->sha1;
        }

        return new Simple([
            'required'       => $this->faker->boolean,
            'showInList'     => $this->faker->boolean,
            'help'           => $this->faker->text,
            'definitionId'   => $this->faker->randomNumber(),
            'definitionName' => $this->faker->name,
            'typeId'         => $this->faker->randomNumber(),
            'typeName'       => $this->faker->name,
            'typeText'       => $this->faker->text,
            'moduleId'       => $this->faker->randomNumber(),
            'formId'         => $this->faker->randomNumber(),
            'isEncrypted'    => $this->faker->boolean,
            'data'           => $data,
            'key'            => $key,
        ]);
    }
}
