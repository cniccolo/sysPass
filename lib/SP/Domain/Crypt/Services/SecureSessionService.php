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

namespace SP\Domain\Crypt\Services;

use Defuse\Crypto\Key;
use Exception;
use SP\Core\Application;
use SP\Core\Crypt\CryptInterface;
use SP\Core\Crypt\RequestBasedPassword;
use SP\Core\Crypt\RequestBasedPasswordInterface;
use SP\Core\Crypt\UUIDCookie;
use SP\Core\Crypt\Vault;
use SP\Domain\Common\Services\Service;
use SP\Domain\Common\Services\ServiceException;
use SP\Domain\Crypt\Ports\SecureSessionServiceInterface;
use SP\Infrastructure\File\FileCacheInterface;
use SP\Infrastructure\File\FileException;
use function SP\logger;
use function SP\processException;

/**
 * Class SecureSessionService
 *
 * @package SP\Domain\Crypt\Services
 */
final class SecureSessionService extends Service implements SecureSessionServiceInterface
{
    private const CACHE_EXPIRE_TIME = 86400;
    private const CACHE_PATH        = CACHE_PATH.DIRECTORY_SEPARATOR.'secure_session';

    public function __construct(
        Application $application,
        private readonly CryptInterface $crypt,
        private readonly FileCacheInterface $fileCache,
        private readonly RequestBasedPasswordInterface $requestBasedPassword
    ) {
        parent::__construct($application);
    }

    /**
     * Returns an unique filename from a browser cookie
     *
     * @throws ServiceException
     */
    public static function getFileNameFrom(UUIDCookie $cookie, string $seed): string
    {
        if (($uuid = $cookie->load($seed)) === false
            && ($uuid = $cookie->create($seed)) === false
        ) {
            throw new ServiceException('Unable to get UUID for filename');
        }

        return self::CACHE_PATH.DIRECTORY_SEPARATOR.$uuid;
    }

    /**
     * Returns the encryption key
     *
     *
     * @return Key|false
     */
    public function getKey(): Key|bool
    {
        try {
            if ($this->fileCache->isExpired(self::CACHE_EXPIRE_TIME)) {
                logger('Session key expired or does not exist', 'ERROR');

                return $this->saveKey();
            }

            $vault = $this->fileCache->load(null, Vault::class);

            return Key::loadFromAsciiSafeString($vault->getData($this->requestBasedPassword->build()));
        } catch (FileException) {
            return $this->saveKey();
        } catch (Exception $e) {
            processException($e);
        }

        return false;
    }

    /**
     * Saves the encryption key
     *
     * @return Key|false
     */
    private function saveKey(): Key|bool
    {
        try {
            $securedKey = Key::createNewRandomKey();

            $data = Vault::factory($this->crypt)
                         ->saveData(
                             $securedKey->saveToAsciiSafeString(),
                             $this->requestBasedPassword->build()
                         );

            $this->fileCache->save($data);

            logger('Saved session key');

            return $securedKey;
        } catch (Exception $e) {
            processException($e);
        }

        return false;
    }
}
