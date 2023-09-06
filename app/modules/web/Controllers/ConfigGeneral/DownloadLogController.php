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

namespace SP\Modules\Web\Controllers\ConfigGeneral;


use Exception;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Acl\UnauthorizedPageException;
use SP\Core\Context\SessionContext;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Infrastructure\File\FileHandler;
use SP\Modules\Web\Controllers\SimpleControllerBase;
use SP\Modules\Web\Controllers\Traits\JsonTrait;

/**
 * Class DownloadLogController
 */
final class DownloadLogController extends SimpleControllerBase
{
    use JsonTrait;

    public function downloadLogAction(): string
    {
        if ($this->configData->isDemoEnabled()) {
            return __('Ey, this is a DEMO!!');
        }

        try {
            SessionContext::close();

            $file = new FileHandler(LOG_FILE);
            $file->checkFileExists();

            $this->eventDispatcher->notifyEvent(
                'download.logFile',
                new Event(
                    $this,
                    EventMessage::factory()
                        ->addDescription(__u('File downloaded'))
                        ->addDetail(__u('File'), str_replace(APP_ROOT, '', $file->getFile()))
                )
            );

            $response = $this->router->response();
            $response->header('Cache-Control', 'max-age=60, must-revalidate');
            $response->header('Content-length', $file->getFileSize());
            $response->header('Content-type', $file->getFileType());
            $response->header('Content-Description', ' sysPass file');
            $response->header('Content-transfer-encoding', 'chunked');
            $response->header('Content-Disposition', 'attachment; filename="'.basename($file->getFile()).'"');
            $response->header('Set-Cookie', 'fileDownload=true; path=/');
            $response->send();

            $file->readChunked();
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));
        }

        return '';
    }

    /**
     * @throws \JsonException
     * @throws \SP\Core\Exceptions\SessionTimeout
     */
    protected function initialize(): void
    {
        try {
            $this->checks();
            $this->checkAccess(ActionsInterface::CONFIG_GENERAL);
        } catch (UnauthorizedPageException $e) {
            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            $this->returnJsonResponseException($e);
        }
    }
}