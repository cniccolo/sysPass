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

namespace SP\Providers\Auth;

/**
 * Class AuthDataBase
 */
abstract class AuthDataBase
{
    protected ?string $name          = null;
    protected ?string $email         = null;
    protected ?bool   $authenticated = null;
    protected int     $statusCode    = 0;
    protected ?string $server        = null;
    protected bool    $failed        = false;

    /**
     * @param bool $authoritative Whether this authentication is required to access to the application
     */
    public function __construct(private readonly bool $authoritative = false)
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getAuthenticated(): ?int
    {
        return $this->authenticated;
    }

    public function setAuthenticated(?bool $authenticated = null): static
    {
        $this->authenticated = $authenticated;

        return $this;
    }

    public function getServer(): ?string
    {
        return $this->server;
    }

    public function setServer(string $server): void
    {
        $this->server = $server;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Whether this authentication is required to access to the application
     *
     * @return bool
     */
    public function isAuthoritative(): bool
    {
        return $this->authoritative;
    }

    public function isFailed(): bool
    {
        return $this->failed;
    }

    public function setFailed(bool $failed): void
    {
        $this->failed = $failed;
    }
}
