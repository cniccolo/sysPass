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

use SP\Domain\Install\Services\InstallerService;

/**
 * Class VersionUtil
 *
 * @package SP\Util
 */
final class VersionUtil
{
    /**
     * Devolver versión normalizada en cadena
     */
    public static function getVersionStringNormalized(): string
    {
        return implode('', InstallerService::VERSION).'.'.InstallerService::BUILD;
    }

    /**
     * Compare versions
     *
     * @param  string  $currentVersion
     * @param  array|string  $upgradeableVersion
     *
     * @return bool True if $currentVersion is lower than $upgradeableVersion
     */
    public static function checkVersion(string $currentVersion, array|string $upgradeableVersion): bool
    {
        if (is_array($upgradeableVersion)) {
            $upgradeableVersion = array_pop($upgradeableVersion);
        }

        $currentVersion = self::normalizeVersionForCompare($currentVersion);
        $upgradeableVersion = self::normalizeVersionForCompare($upgradeableVersion);

        if (empty($currentVersion) || empty($upgradeableVersion)) {
            return false;
        }

        if (PHP_INT_SIZE > 4) {
            return version_compare($currentVersion, $upgradeableVersion) === -1;
        }

        [$currentVersion, $build] = explode('.', $currentVersion, 2);
        [$upgradeVersion, $upgradeBuild] = explode('.', $upgradeableVersion, 2);

        $versionRes = (int)$currentVersion < (int)$upgradeVersion;

        return (($versionRes && (int)$upgradeBuild === 0)
                || ($versionRes && (int)$build < (int)$upgradeBuild));
    }

    /**
     * Return a normalized version string to be compared
     *
     * @param  array|string  $versionIn
     *
     * @return string
     */
    public static function normalizeVersionForCompare(array|string $versionIn): string
    {
        if (!empty($versionIn)) {
            if (is_string($versionIn)) {
                [$version, $build] = explode('.', $versionIn);
            } elseif (is_array($versionIn) && count($versionIn) === 4) {
                $version = implode('', array_slice($versionIn, 0, 3));
                $build = $versionIn[3];
            } else {
                return '';
            }

            $nomalizedVersion = 0;

            foreach (str_split($version) as $key => $value) {
                $nomalizedVersion += (int)$value * (10 ** (3 - $key));
            }

            return $nomalizedVersion.'.'.$build;
        }

        return '';
    }

    /**
     * @param  string  $version
     *
     * @return float|int
     */
    public static function versionToInteger(string $version): float|int
    {
        $intVersion = 0;

        $strSplit = str_split(str_replace('.', '', $version));

        foreach ($strSplit as $key => $value) {
            $intVersion += (int)$value * (10 ** (3 - $key));
        }

        return $intVersion;
    }

    /**
     * Devuelve la versión de sysPass.
     *
     * @param  bool  $retBuild  devolver el número de compilación
     *
     * @return array con el número de versión
     */
    public static function getVersionArray(bool $retBuild = false): array
    {
        $version = array_values(InstallerService::VERSION);

        if ($retBuild === true) {
            $version[] = InstallerService::BUILD;

            return $version;
        }

        return $version;
    }
}
