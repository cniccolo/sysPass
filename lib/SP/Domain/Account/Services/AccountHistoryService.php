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

namespace SP\Domain\Account\Services;

use SP\Core\Application;
use SP\DataModel\ItemSearchData;
use SP\Domain\Account\Dtos\AccountHistoryCreateDto;
use SP\Domain\Account\Dtos\EncryptedPassword;
use SP\Domain\Account\Models\AccountHistory;
use SP\Domain\Account\Ports\AccountHistoryRepositoryInterface;
use SP\Domain\Account\Ports\AccountHistoryServiceInterface;
use SP\Domain\Common\Services\Service;
use SP\Domain\Common\Services\ServiceException;
use SP\Infrastructure\Common\Repositories\NoSuchItemException;
use SP\Infrastructure\Database\QueryResult;
use function SP\__u;

/**
 * Class AccountHistoryService
 *
 * @package SP\Domain\Account\Services
 */
final class AccountHistoryService extends Service implements AccountHistoryServiceInterface
{
    public function __construct(
        Application $application,
        private AccountHistoryRepositoryInterface $accountHistoryRepository
    ) {
        parent::__construct($application);
    }

    /**
     * Returns the item for given id
     *
     * @throws NoSuchItemException
     */
    public function getById(int $id): AccountHistory
    {
        $results = $this->accountHistoryRepository->getById($id);

        if ($results->getNumRows() === 0) {
            throw new NoSuchItemException(__u('Error while retrieving account\'s data'));
        }

        return $results->getData();
    }

    /**
     * Obtiene el listado del histórico de una cuenta.
     *
     * @param  int  $id
     *
     * @return array Con los registros con id como clave y fecha - usuario como valor
     * @throws \SP\Core\Exceptions\SPException
     */
    public function getHistoryForAccount(int $id): array
    {
        return $this->accountHistoryRepository->getHistoryForAccount($id)->getDataAsArray();
    }

    /**
     * @param  \SP\DataModel\ItemSearchData  $itemSearchData
     *
     * @return \SP\Infrastructure\Database\QueryResult
     */
    public function search(ItemSearchData $itemSearchData): QueryResult
    {
        return $this->accountHistoryRepository->search($itemSearchData);
    }

    /**
     * Crea una nueva cuenta en la BBDD
     *
     * @param  \SP\Domain\Account\Dtos\AccountHistoryCreateDto  $dto
     *
     * @return int
     */
    public function create(AccountHistoryCreateDto $dto): int
    {
        return $this->accountHistoryRepository->create($dto);
    }

    /**
     * @return array
     * @throws \SP\Core\Exceptions\SPException
     */
    public function getAccountsPassData(): array
    {
        return $this->accountHistoryRepository->getAccountsPassData()->getDataAsArray();
    }

    /**
     * Elimina los datos de una cuenta en la BBDD.
     *
     * @param  int  $id
     *
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function delete(int $id): void
    {
        if (!$this->accountHistoryRepository->delete($id)) {
            throw new ServiceException(__u('Error while deleting the account'));
        }
    }

    /**
     * Deletes all the items for given ids
     *
     * @param  int[]  $ids
     *
     * @return int
     */
    public function deleteByIdBatch(array $ids): int
    {
        return $this->accountHistoryRepository->deleteByIdBatch($ids);
    }

    /**
     * Deletes all the items for given accounts id
     *
     * @param  int[]  $ids
     *
     * @return int
     */
    public function deleteByAccountIdBatch(array $ids): int
    {
        return $this->accountHistoryRepository->deleteByAccountIdBatch($ids);
    }

    /**
     * @param  int  $accountId
     * @param  \SP\Domain\Account\Dtos\EncryptedPassword  $encryptedPassword
     *
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function updatePasswordMasterPass(int $accountId, EncryptedPassword $encryptedPassword): void
    {
        if (!$this->accountHistoryRepository->updatePassword($accountId, $encryptedPassword)) {
            throw new ServiceException(__u('Error while updating the password'));
        }
    }
}
