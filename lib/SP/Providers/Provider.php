<?php
/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2021, Rubén Domínguez nuxsmin@$syspass.org
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

namespace SP\Providers;

use SP\Core\Application;
use SP\Core\Context\ContextInterface;
use SP\Core\Events\EventDispatcher;
use SP\Domain\Config\Services\ConfigFileService;

/**
 * Class Service
 *
 * @package SP\Providers
 */
abstract class Provider implements ProviderInterface
{
    protected ConfigFileService $config;
    protected ContextInterface  $context;
    protected EventDispatcher  $eventDispatcher;
    protected bool             $initialized = false;

    /**
     * Provider constructor.
     *
     * @param  \SP\Core\Application  $application
     */
    public function __construct(Application $application)
    {
        $this->config = $application->getConfig();
        $this->context = $application->getContext();
        $this->eventDispatcher = $application->getEventDispatcher();
    }

    /**
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }
}