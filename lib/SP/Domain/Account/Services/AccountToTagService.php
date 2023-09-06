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

namespace SP\Domain\Account\Services;

use SP\Core\Application;
use SP\DataModel\ItemData;
use SP\Domain\Account\Ports\AccountToTagRepositoryInterface;
use SP\Domain\Account\Ports\AccountToTagServiceInterface;
use SP\Domain\Common\Models\Simple;
use SP\Domain\Common\Services\Service;

/**
 * Class AccountToTagService
 *
 * @package SP\Domain\Account\Services
 */
final class AccountToTagService extends Service implements AccountToTagServiceInterface
{
    private AccountToTagRepositoryInterface $accountToTagRepository;

    public function __construct(
        Application $application,
        AccountToTagRepositoryInterface $accountToTagRepository
    ) {
        $this->accountToTagRepository = $accountToTagRepository;

        parent::__construct($application);
    }

    /**
     * @param  int  $id
     *
     * @return ItemData[]
     *
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function getTagsByAccountId(int $id): array
    {
        $tags = $this->accountToTagRepository
            ->getTagsByAccountId($id)
            ->getDataAsArray(Simple::class);

        return array_map(fn($tag) => ItemData::buildFromSimpleModel($tag), $tags);
    }
}
