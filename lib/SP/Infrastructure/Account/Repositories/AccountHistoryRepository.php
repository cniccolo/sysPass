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

namespace SP\Infrastructure\Account\Repositories;

use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\ItemSearchData;
use SP\Domain\Account\Dtos\AccountHistoryCreateDto;
use SP\Domain\Account\Dtos\EncryptedPassword;
use SP\Domain\Account\Ports\AccountHistoryRepositoryInterface;
use SP\Infrastructure\Common\Repositories\Repository;
use SP\Infrastructure\Common\Repositories\RepositoryItemTrait;
use SP\Infrastructure\Database\QueryData;
use SP\Infrastructure\Database\QueryResult;
use function SP\__u;

/**
 * Class AccountHistoryRepository
 *
 * @package Services
 */
final class AccountHistoryRepository extends Repository implements AccountHistoryRepositoryInterface
{
    use RepositoryItemTrait;

    /**
     * Obtiene el listado del histórico de una cuenta.
     *
     * @param $id
     *
     * @return QueryResult
     */
    public function getHistoryForAccount($id): QueryResult
    {
        $query = $this->queryFactory
            ->newSelect()
            ->from('AccountHistory AS Account')
            ->cols([
                'Account.id',
                'Account.dateEdit',
                'Account.dateAdd',
                'User.login AS userAdd',
                'UserEdit.login AS userEdit',
            ])
            ->join('INNER', 'User as User', 'Account.userId = User.id')
            ->join('LEFT', 'User as UserEdit', 'Account.userEditId = UserEdit.id')
            ->where('Account.id = :id')
            ->bindValues(['id' => $id])
            ->orderBy(['Account.id DESC']);

        return $this->db->doSelect(QueryData::build($query));
    }

    /**
     * Crea una nueva cuenta en la BBDD
     *
     * @param  \SP\Domain\Account\Dtos\AccountHistoryCreateDto  $dto
     *
     * @return int
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function create(AccountHistoryCreateDto $dto): int
    {
        $accountData = $dto->getAccount();

        $query = $this->queryFactory
            ->newInsert()
            ->into('Account')
            ->cols([
                'accountId'          => $accountData->getId(),
                'clientId'           => $accountData->getClientId(),
                'categoryId'         => $accountData->getCategoryId(),
                'name'               => $accountData->getName(),
                'login'              => $accountData->getLogin(),
                'url'                => $accountData->getUrl(),
                'pass'               => $accountData->getPass(),
                'key'                => $accountData->getKey(),
                'notes'              => $accountData->getNotes(),
                'userId'             => $accountData->getUserId(),
                'userGroupId'        => $accountData->getUserGroupId(),
                'userEditId'         => $accountData->getUserEditId(),
                'isPrivate'          => $accountData->getIsPrivate(),
                'isPrivateGroup'     => $accountData->getIsPrivateGroup(),
                'passDate'           => $accountData->getPassDate(),
                'passDateChange'     => $accountData->getPassDateChange(),
                'parentId'           => $accountData->getParentId(),
                'countView'          => $accountData->getCountView(),
                'countDecrypt'       => $accountData->getCountDecrypt(),
                'dateAdd'            => $accountData->getDateAdd(),
                'dateEdit'           => $accountData->getDateEdit(),
                'otherUserEdit'      => $accountData->getOtherUserEdit(),
                'otherUserGroupEdit' => $accountData->getOtherUserGroupEdit(),
                'isModify'           => $dto->isModify(),
                'isDeleted'          => $dto->isDelete(),
                'mPassHash'          => $dto->getMasterPassHash(),
            ]);

        $queryData = QueryData::build($query)->setOnErrorMessage(__u('Error while updating history'));

        return $this->db->doQuery($queryData)->getLastId();
    }

    /**
     * Elimina los datos de una cuenta en la BBDD.
     *
     * @param  int  $id
     *
     * @return bool
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function delete(int $id): bool
    {
        $query = $this->queryFactory
            ->newDelete()
            ->from('AccountHistory')
            ->where('id = :id')
            ->bindValues(['id' => $id]);

        $queryData = QueryData::build($query)->setOnErrorMessage(__u('Error while deleting the account'));

        return $this->db->doQuery($queryData)->getAffectedNumRows() === 1;
    }

    /**
     * Returns the item for given id
     *
     * @param  int  $id
     *
     * @return QueryResult
     */
    public function getById(int $id): QueryResult
    {
        $query = $this->queryFactory
            ->newSelect()
            ->from('AccountHistory AS Account')
            ->cols([
                'Account.id',
                'Account.accountId',
                'Account.name',
                'Account.categoryId',
                'Account.userId',
                'Account.clientId',
                'Account.userGroupId',
                'Account.userEditId',
                'Account.login',
                'Account.url',
                'Account.notes',
                'Account.countView',
                'Account.countDecrypt',
                'Account.dateAdd',
                'Account.dateEdit',
                'Account.otherUserEdit',
                'Account.otherUserGroupEdit',
                'Account.isPrivate',
                'Account.isPrivateGroup',
                'Account.passDate',
                'Account.passDateChange',
                'Account.parentId',
                'Account.userLogin',
                'Account.publicLinkHash',
                'Account.isModify',
                'Account.isDeleted',
                'Account.mPassHash',
                'Category.name AS categoryName',
                'User.name AS userName',
                'Client.name AS clientName',
                'UserGroup.name AS userGroupName',
                'UserEdit.name AS userEditName',
                'UserEdit.login AS userEditLogin',
            ])
            ->where('Account.id = :id')
            ->join('INNER', 'Category', 'Account.categoryId = Category.id')
            ->join('INNER', 'Client', 'Account.clientId = Client.id')
            ->join('INNER', 'UserGroup', 'Account.userGroupId = UserGroup.id')
            ->join('INNER', 'User', 'Account.userId = User.id')
            ->join('LEFT', 'User AS UserEdit', 'Account.userEditId = UserEdit.id')
            ->bindValues(['id' => $id])
            ->limit(1);

        $queryData = QueryData::build($query)->setOnErrorMessage(__u('Error while retrieving account\'s data'));

        return $this->db->doSelect($queryData);
    }

    /**
     * Returns all the items
     *
     * @return QueryResult
     */
    public function getAll(): QueryResult
    {
        $query = $this->queryFactory
            ->newSelect()
            ->from('AccountHistory AS Account')
            ->cols([
                'Account.id',
                'Account.accountId',
                'Account.name',
                'Account.categoryId',
                'Account.userId',
                'Account.clientId',
                'Account.userGroupId',
                'Account.userEditId',
                'Account.login',
                'Account.url',
                'Account.notes',
                'Account.countView',
                'Account.countDecrypt',
                'Account.dateAdd',
                'Account.dateEdit',
                'Account.otherUserEdit',
                'Account.otherUserGroupEdit',
                'Account.isPrivate',
                'Account.isPrivateGroup',
                'Account.passDate',
                'Account.passDateChange',
                'Account.parentId',
                'Account.userLogin',
                'Account.publicLinkHash',
                'Account.isModify',
                'Account.isDeleted',
                'Account.mPassHash',
                'Category.name AS categoryName',
                'User.name AS userName',
                'Client.name AS clientName',
                'UserGroup.name AS userGroupName',
                'UserEdit.name AS userEditName',
                'UserEdit.login AS userEditLogin',
            ])
            ->join('INNER', 'Category', 'Account.categoryId = Category.id')
            ->join('INNER', 'Client', 'Account.clientId = Client.id')
            ->join('INNER', 'UserGroup', 'Account.userGroupId = UserGroup.id')
            ->join('INNER', 'User', 'Account.userId = User.id')
            ->join('LEFT', 'User AS UserEdit', 'Account.userEditId = UserEdit.id')
            ->orderBy(['Account.id DESC']);

        return $this->db->doSelect(QueryData::build($query));
    }

    /**
     * Deletes all the items for given ids
     *
     * @param  array  $ids
     *
     * @return int
     * @throws ConstraintException
     * @throws QueryException
     */
    public function deleteByIdBatch(array $ids): int
    {
        if (count($ids) === 0) {
            return 0;
        }

        $query = $this->queryFactory
            ->newDelete()
            ->from('AccountHistory')
            ->where('id IN (:ids)', ['ids' => $ids]);

        $queryData = QueryData::build($query)->setOnErrorMessage(__u('Error while deleting the accounts'));

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Deletes all the items for given accounts id
     *
     * @param  array  $ids
     *
     * @return int
     * @throws ConstraintException
     * @throws QueryException
     */
    public function deleteByAccountIdBatch(array $ids): int
    {
        if (count($ids) === 0) {
            return 0;
        }

        $query = $this->queryFactory
            ->newDelete()
            ->from('AccountHistory')
            ->where('accountId IN (:accountIds)', ['accountIds' => $ids]);

        $queryData = QueryData::build($query)->setOnErrorMessage(__u('Error while deleting the accounts'));

        return $this->db->doQuery($queryData)->getAffectedNumRows();
    }

    /**
     * Searches for items by a given filter
     *
     * @param  ItemSearchData  $itemSearchData
     *
     * @return QueryResult
     */
    public function search(ItemSearchData $itemSearchData): QueryResult
    {
        $query = $this->queryFactory
            ->newSelect()
            ->from('AccountHistory AS Account')
            ->cols([
                'Account.id',
                'Account.name',
                'Client.name AS clientName',
                'Category.name AS categoryName',
                'Account.isModify',
                'Account.isDeleted',
                'IFNULL(Account.dateEdit,Account.dateAdd) as date',
            ])
            ->join('INNER', 'Category', 'Account.categoryId = Category.id')
            ->join('INNER', 'Client', 'Account.clientId = Client.id')
            ->orderBy(['Account.date DESC', 'clientName ASC', 'Account.id DESC'])
            ->limit($itemSearchData->getLimitCount())
            ->offset($itemSearchData->getLimitStart());

        if (!empty($itemSearchData->getSeachString())) {
            $query
                ->where('Account.name LIKE :name')
                ->orWhere('Client.name LIKE :clientName');

            $search = '%'.$itemSearchData->getSeachString().'%';

            $query->bindValues([
                'name'       => $search,
                'clientName' => $search,
            ]);
        }

        return $this->db->doSelect(QueryData::build($query), true);
    }

    /**
     * Obtener los datos relativos a la clave de todas las cuentas.
     *
     * @return QueryResult
     */
    public function getAccountsPassData(): QueryResult
    {
        $query = $this->queryFactory
            ->newSelect()
            ->from('AccountHistory')
            ->cols(
                [
                    'id',
                    'name',
                    'pass',
                    'key',
                    'mPassHash',
                ]
            )
            ->where('BIT_LENGTH(pass) > 0')
            ->orderBy(['id ASC']);

        return $this->db->doSelect(QueryData::build($query));
    }

    /**
     * Actualiza la clave de una cuenta en la BBDD.
     *
     * @param  int  $accountId
     * @param  \SP\Domain\Account\Dtos\EncryptedPassword  $encryptedPassword
     *
     * @return bool
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function updatePassword(int $accountId, EncryptedPassword $encryptedPassword): bool
    {
        $query = $this->queryFactory
            ->newUpdate()
            ->table('AccountHistory')
            ->cols([
                'pass'      => $encryptedPassword->getPass(),
                'key'       => $encryptedPassword->getKey(),
                'mPassHash' => $encryptedPassword->getHash(),
            ])
            ->where('id = :id')
            ->bindValues(['id' => $accountId]);

        $queryData = QueryData::build($query)->setOnErrorMessage(__u('Error while updating the password'));

        return $this->db->doQuery($queryData)->getAffectedNumRows() === 1;
    }
}
