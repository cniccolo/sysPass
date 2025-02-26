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

namespace SP\Modules\Web\Controllers\Traits;

use Exception;
use SP\Core\Context\SessionContext;
use SP\Core\Exceptions\SPException;
use SP\Http\Json;
use SP\Http\JsonResponse;

/**
 * Trait JsonTrait
 *
 * @property SessionContext $session
 * @property \Klein\Klein $router
 */
trait JsonTrait
{
    /**
     * Returns JSON response
     *
     * @param  int  $status  Status code
     * @param  string  $description  Untranslated description string
     * @param  array|null  $messages  Untranslated massages array of strings
     *
     * @return bool
     * @throws \SP\Core\Exceptions\SPException
     */
    protected function returnJsonResponse(int $status, string $description, ?array $messages = null): bool
    {
        $jsonResponse = new JsonResponse();
        $jsonResponse->setStatus($status);
        $jsonResponse->setDescription($description);

        if (null !== $messages) {
            $jsonResponse->setMessages($messages);
        }

        return Json::factory($this->router->response())->returnJson($jsonResponse);
    }

    /**
     * Returns JSON response
     *
     * @param  mixed  $data
     * @param  int  $status  Status code
     * @param  string|null  $description  Untranslated description string
     * @param  array|null  $messages
     *
     * @return bool
     * @throws \SP\Core\Exceptions\SPException
     */
    protected function returnJsonResponseData(
        $data,
        int $status = JsonResponse::JSON_SUCCESS,
        ?string $description = null,
        ?array $messages = null
    ): bool
    {
        $jsonResponse = new JsonResponse();
        $jsonResponse->setStatus($status);
        $jsonResponse->setData($data);

        if (null !== $description) {
            $jsonResponse->setDescription($description);
        }

        if (null !== $messages) {
            $jsonResponse->setMessages($messages);
        }

        return Json::factory($this->router->response())->returnJson($jsonResponse);
    }

    /**
     * Returns JSON response
     *
     * @param  \Exception  $exception
     * @param  int  $status
     *
     * @return bool
     * @throws \SP\Core\Exceptions\SPException
     */
    protected function returnJsonResponseException(Exception $exception, int $status = JsonResponse::JSON_ERROR): bool
    {
        $jsonResponse = new JsonResponse();
        $jsonResponse->setStatus($status);
        $jsonResponse->setDescription($exception->getMessage());

        if ($exception instanceof SPException && $exception->getHint() !== null) {
            $jsonResponse->setMessages([$exception->getHint()]);
        }

        return Json::factory($this->router->response())->returnJson($jsonResponse);
    }
}