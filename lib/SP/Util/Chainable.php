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

namespace SP\Util;

use Closure;

/**
 * Class Chainable
 */
final class Chainable
{
    private array $args;

    public function __construct(private Closure $next, private object $bindTo, ...$args)
    {
        $this->args = $args;
    }

    public function next(Closure $next): Chainable
    {
        $resolved = $this->next->call($this->bindTo, ...$this->args);

        return new self($next, $this->bindTo, $resolved);
    }

    public function resolve(): mixed
    {
        return $this->next->call($this->bindTo, ...$this->args);
    }
}
