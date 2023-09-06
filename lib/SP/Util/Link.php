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

use SP\Core\Acl\Acl;
use SP\Core\Bootstrap\BootstrapBase;
use SP\Domain\Config\Ports\ConfigDataInterface;
use SP\Http\Uri;

/**
 * Class Link
 *
 * @package SP\Util
 */
final class Link
{
    public static function getDeepLink(
        int $itemId,
        int $actionId,
        ConfigDataInterface $configData,
        bool $useUI = false
    ): string {
        $route = Acl::getActionRoute($actionId).'/'.$itemId;

        if ($useUI) {
            $baseUrl = ($configData->getApplicationUrl() ?? BootstrapBase::$WEBURI).'/index.php';
        } else {
            $baseUrl = ($configData->getApplicationUrl() ?? BootstrapBase::$WEBURI).BootstrapBase::$SUBURI;
        }

        $uri = new Uri($baseUrl);
        $uri->addParam('r', $route);

        return $uri->getUriSigned($configData->getPasswordSalt());
    }
}
