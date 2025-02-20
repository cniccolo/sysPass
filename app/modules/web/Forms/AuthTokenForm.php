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

namespace SP\Modules\Web\Forms;

use SP\Core\Acl\ActionsInterface;
use SP\Core\Exceptions\ValidationException;
use SP\DataModel\AuthTokenData;
use SP\Domain\Auth\Services\AuthTokenService;

/**
 * Class ApiTokenForm
 *
 * @package SP\Modules\Web\Forms
 */
final class AuthTokenForm extends FormBase implements FormInterface
{
    protected ?AuthTokenData $authTokenData = null;
    protected bool           $refresh       = false;

    /**
     * Validar el formulario
     *
     * @param  int  $action
     * @param  int|null  $id
     *
     * @return AuthTokenForm|FormInterface
     * @throws ValidationException
     */
    public function validateFor(int $action, ?int $id = null): FormInterface
    {
        if ($id !== null) {
            $this->itemId = $id;
        }

        switch ($action) {
            case ActionsInterface::AUTHTOKEN_CREATE:
            case ActionsInterface::AUTHTOKEN_EDIT:
                $this->analyzeRequestData();
                $this->checkCommon();
                break;
        }

        return $this;
    }

    /**
     * Analizar los datos de la petición HTTP
     *
     * @return void
     */
    protected function analyzeRequestData(): void
    {
        $this->refresh = $this->request->analyzeBool('refreshtoken', false);

        $this->authTokenData = new AuthTokenData();
        $this->authTokenData->setId($this->itemId);
        $this->authTokenData->setUserId($this->request->analyzeInt('users'));
        $this->authTokenData->setActionId($this->request->analyzeInt('actions'));
        $this->authTokenData->setHash($this->request->analyzeEncrypted('pass'));
    }

    /**
     * @throws ValidationException
     */
    protected function checkCommon(): void
    {
        if (0 === $this->authTokenData->getUserId()) {
            throw new ValidationException(__u('User not set'));
        }

        if (0 === $this->authTokenData->getActionId()) {
            throw new ValidationException(__u('Action not set'));
        }

        if (empty($this->authTokenData->getHash())
            && (AuthTokenService::isSecuredAction($this->authTokenData->getActionId())
                || $this->isRefresh())) {
            throw new ValidationException(__u('Password cannot be blank'));
        }
    }

    public function isRefresh(): bool
    {
        return $this->refresh;
    }

    public function getItemData(): ?AuthTokenData
    {
        return $this->authTokenData;
    }
}