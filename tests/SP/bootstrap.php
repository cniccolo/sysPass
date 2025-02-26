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

namespace SP\Tests;

use DI\Container;
use DI\ContainerBuilder;
use Exception;
use RuntimeException;
use SP\Core\Context\ContextException;
use SP\Core\Context\ContextInterface;
use SP\Core\Exceptions\FileNotFoundException;
use SP\DataModel\ProfileData;
use SP\Domain\User\Services\UserLoginResponse;
use SP\Infrastructure\Database\DatabaseConnectionData;
use SP\Infrastructure\Database\DbStorageInterface;
use SP\Infrastructure\Database\MysqlHandler;
use SP\Util\FileUtil;
use function SP\logger;
use function SP\processException;

define('DEBUG', true);
define('IS_TESTING', true);
define('APP_ROOT', dirname(__DIR__, 2));
define('TEST_ROOT', dirname(__DIR__));

const APP_DEFINITIONS_FILE = APP_ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Definitions.php';

define('RESOURCE_PATH', TEST_ROOT.DIRECTORY_SEPARATOR.'res');
define('CONFIG_PATH', RESOURCE_PATH.DIRECTORY_SEPARATOR.'config');
define('CONFIG_FILE', CONFIG_PATH.DIRECTORY_SEPARATOR.'config.xml');
define('ACTIONS_FILE', CONFIG_PATH.DIRECTORY_SEPARATOR.'actions.xml');
define('LOCALES_PATH', APP_ROOT.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'locales');
define('MODULES_PATH', APP_ROOT.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'modules');
define('SQL_PATH', APP_ROOT.DIRECTORY_SEPARATOR.'schemas');
define('CACHE_PATH', RESOURCE_PATH.DIRECTORY_SEPARATOR.'cache');
define('TMP_PATH', TEST_ROOT.DIRECTORY_SEPARATOR.'tmp');
define('BACKUP_PATH', TMP_PATH);
define('PLUGINS_PATH', TMP_PATH);
define('XML_SCHEMA', APP_ROOT.DIRECTORY_SEPARATOR.'schemas'.DIRECTORY_SEPARATOR.'syspass.xsd');
define('LOG_FILE', TMP_PATH.DIRECTORY_SEPARATOR.'test.log');
define('FIXTURE_FILES', [
    RESOURCE_PATH.DIRECTORY_SEPARATOR.'datasets'.DIRECTORY_SEPARATOR.'truncate.sql',
    RESOURCE_PATH.DIRECTORY_SEPARATOR.'datasets'.DIRECTORY_SEPARATOR.'syspass.sql',
]);
define('SELF_IP_ADDRESS', getRealIpAddress());
define('SELF_HOSTNAME', gethostbyaddr(SELF_IP_ADDRESS));

require_once APP_ROOT.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require_once APP_ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'BaseFunctions.php';

logger('APP_ROOT='.APP_ROOT);
logger('TEST_ROOT='.TEST_ROOT);
logger('SELF_IP_ADDRESS='.SELF_IP_ADDRESS);

// Setup directories
try {
    recreateDir(TMP_PATH);
    recreateDir(CACHE_PATH);
} catch (FileNotFoundException $e) {
    processException($e);
}

if (is_dir(CONFIG_PATH)
    && decoct(fileperms(CONFIG_PATH) & 0777) !== '750'
) {
    print 'Setting permissions for '.CONFIG_PATH.PHP_EOL;

    chmod(CONFIG_PATH, 0750);
}

/**
 * Función para llamadas a gettext
 */
if (!function_exists('\gettext')) {
    function gettext(string $str): string
    {
        return $str;
    }
}

function getRealIpAddress(): string
{
    return trim(shell_exec('ip a s eth0 | awk \'$1 == "inet" {print $2}\' | cut -d"/" -f1')) ?: '127.0.0.1';
}

/**
 * Configura el contexto de la aplicación para los tests
 *
 * @return Container
 * @throws ContextException
 * @throws Exception
 */
function setupContext(): Container
{
    // Instancia del contenedor de dependencias con las definiciones de los objetos necesarios
    // para la aplicación
    $dic = (new ContainerBuilder())
        ->addDefinitions(APP_DEFINITIONS_FILE)
        ->build();

    // Inicializar el contexto
    $context = $dic->get(ContextInterface::class);
    $context->initialize();

    $context->setTrasientKey('_masterpass', '12345678900');

    $userData = new UserLoginResponse();
    $userData->setId(1);
    $userData->setLogin('Admin');
    $userData->setUserGroupName('Admins');
    $userData->setUserGroupId(1);
    $userData->setIsAdminApp(true);
    $userData->setLastUpdate(time());

    $context->setUserData($userData);

    $context->setUserProfile(new ProfileData());

    // Inicializar los datos de conexión a la BBDD
    $dic->set(DbStorageInterface::class, getDbHandler());

    return $dic;
}

function getDbHandler(?DatabaseConnectionData $connectionData = null): MysqlHandler
{
    if ($connectionData === null) {
        // Establecer configuración de conexión con la BBDD
        $connectionData = DatabaseConnectionData::getFromEnvironment();
    }

    return new MysqlHandler($connectionData);
}

function getResource(string $dir, string $file): string
{
    return file_get_contents(RESOURCE_PATH.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file) ?: '';
}

function saveResource(string $dir, string $file, string $data): bool|int
{
    return file_put_contents(RESOURCE_PATH.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file, $data);
}

/**
 * @throws \SP\Core\Exceptions\FileNotFoundException
 */
function recreateDir(string $dir): void
{
    if (is_dir($dir)) {
        logger('Deleting '.$dir);

        FileUtil::rmdirRecursive($dir);
    }

    logger('Creating '.$dir);

    if (!mkdir($dir) && !is_dir($dir)) {
        throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
    }
}
