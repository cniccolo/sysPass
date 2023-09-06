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

namespace SP\Tests\Services\Config;

use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use SP\Core\Context\ContextException;
use SP\Domain\Common\Services\ServiceException;
use SP\Domain\Config\Adapters\ConfigData;
use SP\Domain\Config\Ports\ConfigDataInterface;
use SP\Domain\Config\Services\ConfigBackupService;
use SP\Domain\Config\Services\ConfigFileService;
use SP\Infrastructure\File\FileException;
use function SP\Tests\getResource;
use function SP\Tests\recreateDir;
use function SP\Tests\saveResource;
use function SP\Tests\setupContext;

/**
 * Class ConfigBackupServiceTest
 *
 * @package SP\Tests\Services\Config
 */
class ConfigBackupServiceTest extends TestCase
{
    protected static $currentConfig;

    public static function setUpBeforeClass(): void
    {
        self::$currentConfig = getResource('config', 'config.xml');
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        saveResource('config', 'config.xml', self::$currentConfig);
        recreateDir(CACHE_PATH);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContextException
     */
    public function testBackup()
    {
        $dic = setupContext();

        $configData = new ConfigData();
        $configData->setConfigVersion(uniqid());

        $service = $dic->get(ConfigBackupService::class);
        $service->backup($configData);

        $this->assertTrue(true);

        return $configData;
    }

    /**
     * @depends testBackup
     *
     * @param  ConfigDataInterface  $configData
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContextException
     * @throws ServiceException
     * @throws FileException
     */
    public function testRestore(ConfigDataInterface $configData)
    {
        $dic = setupContext();

        $service = $dic->get(ConfigBackupService::class);
        $data = $service->restore();

        $this->assertEquals($configData->getConfigVersion(), $data->getConfigVersion());

        $config = $dic->get(ConfigFileService::class)->loadConfigFromFile();

        $this->assertEquals($config->getConfigVersion(), $data->getConfigVersion());
        $this->assertGreaterThanOrEqual($config->getConfigDate(), $data->getConfigDate());
    }
}
