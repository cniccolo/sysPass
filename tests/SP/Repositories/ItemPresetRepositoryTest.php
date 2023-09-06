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

namespace SP\Tests\Repositories;

use DI\DependencyException;
use DI\NotFoundException;
use SP\Core\Context\ContextException;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\QueryException;
use SP\DataModel\ItemSearchData;
use SP\Domain\Account\Models\ItemPreset;
use SP\Infrastructure\ItemPreset\Repositories\ItemPresetRepository;
use SP\Tests\DatabaseTestCase;
use stdClass;
use function SP\Tests\setupContext;

/**
 * Class ItemPresetRepositoryTest
 *
 * @package SP\Tests\Repositories
 */
class ItemPresetRepositoryTest extends DatabaseTestCase
{
    /**
     * @var \SP\Domain\ItemPreset\Ports\ItemPresetRepositoryInterface
     */
    private static $repository;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContextException
     */
    public static function setUpBeforeClass(): void
    {
        $dic = setupContext();

        self::$loadFixtures = true;

        // Inicializar el repositorio
        self::$repository = $dic->get(ItemPresetRepository::class);
    }

    public static function userDataProvider(): array
    {
        return [
            [1, 1, 1, 3],
            [1, 2, 2, 1],
            [1, 1, 3, 2],
            [2, 2, 2, 2],
            [2, 2, 3, 2],
            [2, 1, 3, 2],
            [3, 1, 1, 3],
            [3, 1, 2, 2],
        ];
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testDeleteByIdBatch()
    {
        $this->assertEquals(3, self::$repository->deleteByIdBatch([1, 2, 3, 10]));

        $this->assertEquals(2, self::getRowCount('ItemPreset'));

        $this->assertEquals(0, self::$repository->deleteByIdBatch([]));
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testDelete()
    {
        $this->assertEquals(1, self::$repository->delete(3));

        $this->assertEquals(1, self::$repository->delete(4));

        $this->assertEquals(0, self::$repository->delete(10));

        $this->assertEquals(3, self::getRowCount('ItemPreset'));
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testGetByIdBatch()
    {
        $this->assertCount(3, self::$repository->getByIdBatch([1, 2, 3])->getDataAsArray());
        $this->assertCount(3, self::$repository->getByIdBatch([1, 2, 5, 10])->getDataAsArray());
        $this->assertCount(0, self::$repository->getByIdBatch([])->getDataAsArray());
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testUpdate()
    {
        $data = new ItemPreset();
        $data->id = 1;
        $data->userGroupId = 1;
        $data->fixed = 1;
        $data->priority = 1;
        $data->data = 'data';
        $data->type = 'permission';

        self::$repository->update($data);

        $this->assertEquals($data, self::$repository->getById(1)->getData());
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testUpdateDuplicatedHash()
    {
        $this->expectException(ConstraintException::class);

        $data = new ItemPreset();
        $data->id = 1;
        $data->userGroupId = 1;
        $data->fixed = 1;
        $data->priority = 10;
        $data->data = 'data';
        $data->type = 'permission';

        self::$repository->update($data);
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testUpdateUnknownUserId()
    {
        $this->expectException(ConstraintException::class);

        $data = new ItemPreset();
        $data->id = 2;
        $data->userId = 10;
        $data->fixed = 1;
        $data->priority = 1;
        $data->data = 'data';
        $data->type = 'permission';

        self::$repository->update($data);
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testUpdateUnknownUserGroupId()
    {
        $this->expectException(ConstraintException::class);

        $data = new ItemPreset();
        $data->id = 2;
        $data->userGroupId = 10;
        $data->fixed = 1;
        $data->priority = 1;
        $data->data = 'data';
        $data->type = 'permission';

        self::$repository->update($data);
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testUpdateUnknownUserProfileId()
    {
        $this->expectException(ConstraintException::class);

        $data = new ItemPreset();
        $data->id = 2;
        $data->userProfileId = 10;
        $data->fixed = 1;
        $data->priority = 1;
        $data->data = 'data';
        $data->type = 'permission';

        self::$repository->update($data);
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testUpdateUnknownId()
    {
        $data = new ItemPreset();
        $data->id = 10;
        $data->userGroupId = 1;
        $data->fixed = 1;
        $data->priority = 1;
        $data->data = 'data';
        $data->type = 'permission';

        self::$repository->update($data);

        $this->assertEquals(0, self::$repository->update($data));
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testGetById()
    {
        $data = new ItemPreset();
        $data->id = 1;
        $data->userId = 1;
        $data->fixed = 0;
        $data->priority = 0;
        $data->type = 'permission';

        $result = self::$repository->getById(1);

        $this->assertEquals(1, $result->getNumRows());
        $this->assertEquals($data, $result->getData());
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testGetAll()
    {
        $count = self::getRowCount('ItemPreset');

        $result = self::$repository->getAll();
        $this->assertEquals($count, $result->getNumRows());

        /** @var ItemPreset[] $data */
        $data = $result->getDataAsArray();
        $this->assertCount($count, $data);

        $this->assertInstanceOf(ItemPreset::class, $data[0]);
        $this->assertEquals(1, $data[0]->getId());
        $this->assertEquals('permission', $data[0]->getType());
        $this->assertEquals(1, $data[0]->getUserId());
        $this->assertNull($data[0]->getUserGroupId());
        $this->assertNull($data[0]->getUserProfileId());
        $this->assertNull($data[0]->getData());
        $this->assertEquals(0, $data[0]->getFixed());
        $this->assertEquals(0, $data[0]->getPriority());

        $this->assertInstanceOf(ItemPreset::class, $data[1]);
        $this->assertEquals(2, $data[1]->getId());
        $this->assertEquals('permission', $data[1]->getType());

        $this->assertInstanceOf(ItemPreset::class, $data[2]);
        $this->assertEquals(3, $data[2]->getId());
        $this->assertEquals('permission', $data[2]->getType());
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testSearch()
    {
        // Search for user's name
        $itemSearchData = new ItemSearchData();
        $itemSearchData->setLimitCount(10);
        $itemSearchData->setSeachString('demo');

        $result = self::$repository->search($itemSearchData);
        $data = $result->getDataAsArray();

        $this->assertEquals(1, $result->getNumRows());
        $this->assertCount(1, $data);
        $this->assertInstanceOf(stdClass::class, $data[0]);
        $this->assertEquals(4, $data[0]->id);
        $this->assertEquals('permission', $data[0]->type);
        $this->assertEquals(2, $data[0]->userId);
        $this->assertNull($data[0]->userGroupId);
        $this->assertNull($data[0]->userProfileId);
        $this->assertNull($data[0]->data);
        $this->assertEquals(0, $data[0]->fixed);
        $this->assertEquals(0, $data[0]->priority);
        $this->assertEquals('sysPass demo', $data[0]->userName);

        // Search for group's name
        $itemSearchData = new ItemSearchData();
        $itemSearchData->setLimitCount(10);
        $itemSearchData->setSeachString('admins');

        $result = self::$repository->search($itemSearchData);
        $data = $result->getDataAsArray();

        $this->assertEquals(1, $result->getNumRows());
        $this->assertCount(1, $data);
        $this->assertInstanceOf(stdClass::class, $data[0]);
        $this->assertEquals(2, $data[0]->id);
        $this->assertEquals('permission', $data[0]->type);
        $this->assertNull($data[0]->userId);
        $this->assertEquals(1, $data[0]->userGroupId);
        $this->assertNull($data[0]->userProfileId);
        $this->assertNull($data[0]->data);
        $this->assertEquals(0, $data[0]->fixed);
        $this->assertEquals(10, $data[0]->priority);
        $this->assertEquals('Admins', $data[0]->userGroupName);

        // Search for profile's name
        $itemSearchData = new ItemSearchData();
        $itemSearchData->setLimitCount(10);
        $itemSearchData->setSeachString('Usuarios');

        $result = self::$repository->search($itemSearchData);
        $data = $result->getDataAsArray();

        $this->assertEquals(1, $result->getNumRows());
        $this->assertCount(1, $data);
        $this->assertInstanceOf(stdClass::class, $data[0]);
        $this->assertEquals(5, $data[0]->id);
        $this->assertEquals('permission', $data[0]->type);
        $this->assertNull($data[0]->userId);
        $this->assertNull($data[0]->userGroupId);
        $this->assertEquals(3, $data[0]->userProfileId);
        $this->assertNull($data[0]->data);
        $this->assertEquals(0, $data[0]->fixed);
        $this->assertEquals(10, $data[0]->priority);
        $this->assertEquals('Usuarios', $data[0]->userProfileName);

        // Search for no results
        $itemSearchData = new ItemSearchData();
        $itemSearchData->setLimitCount(10);
        $itemSearchData->setSeachString('test_permission');

        $result = self::$repository->search($itemSearchData);
        $data = $result->getDataAsArray();

        $this->assertEquals(0, $result->getNumRows());
        $this->assertCount(0, $data);
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testCreate()
    {
        $data = new ItemPreset();
        $data->id = 6;
        $data->userGroupId = 1;
        $data->fixed = 1;
        $data->priority = 20;
        $data->data = 'data';
        $data->type = 'permission';

        $id = self::$repository->create($data);

        $this->assertEquals($data->id, $id);
        $this->assertEquals($data, self::$repository->getById($id)->getData());
    }

    /**
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testCreateDuplicatedHash()
    {
        $this->expectException(ConstraintException::class);

        $data = new ItemPreset();
        $data->userGroupId = 1;
        $data->fixed = 1;
        $data->priority = 10;
        $data->data = 'data';
        $data->type = 'permission';

        self::$repository->create($data);
    }

    /**
     * @dataProvider userDataProvider
     *
     * @param  int  $userId
     * @param  int  $userGroupId
     * @param  int  $userProfileId
     * @param  int  $expected
     *
     * @throws ConstraintException
     * @throws QueryException
     */
    public function testGetByFilter($userId, $userGroupId, $userProfileId, $expected)
    {
        $result = self::$repository->getByFilter('permission', $userId, $userGroupId, $userProfileId);

        $this->assertEquals(1, $result->getNumRows());

        /** @var ItemPreset $data */
        $data = $result->getData();

        $this->assertInstanceOf(ItemPreset::class, $data);
        $this->assertEquals($expected, $data->getId());
    }
}
