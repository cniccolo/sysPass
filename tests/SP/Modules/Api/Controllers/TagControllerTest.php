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

namespace SP\Tests\Modules\Api\Controllers;

use SP\Core\Acl\ActionsInterface;
use SP\Tests\Modules\Api\ApiTestCase;
use stdClass;

/**
 * Class TagControllerTest
 *
 * @package SP\Tests\Modules\Api\Controllers
 */
class TagControllerTest extends ApiTestCase
{
    private const PARAMS = [
        'name' => 'API Tag'
    ];

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testCreateAction(): void
    {
        $response = $this->createTag(self::PARAMS);

        $this->assertEquals(0, $response->result->resultCode);
        $this->assertNull($response->result->count);
        $this->assertInstanceOf(stdClass::class, $response->result);
        $this->assertEquals(4, $response->result->itemId);
        $this->assertEquals('Tag added', $response->result->resultMessage);

        $resultItem = $response->result->result;

        $this->assertEquals($response->result->itemId, $resultItem->id);
        $this->assertEquals(self::PARAMS['name'], $resultItem->name);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    private function createTag(?array $params = null): stdClass
    {
        $api = $this->callApi(
            ActionsInterface::TAG_CREATE,
            $params ?? self::PARAMS
        );

        return self::processJsonResponse($api);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testCreateActionDuplicated(): void
    {
        $response = $this->createTag(['name' => 'linux']);

        $this->assertInstanceOf(stdClass::class, $response->error);
        $this->assertEquals('Duplicated tag', $response->error->message);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testCreateActionRequiredParameters(): void
    {
        $response = $this->createTag([]);

        $this->assertInstanceOf(stdClass::class, $response->error);
        $this->assertEquals('Wrong parameters', $response->error->message);
        $this->assertInstanceOf(stdClass::class, $response->error->data);
        $this->assertIsArray($response->error->data->help);

    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testViewAction(): void
    {
        $response = $this->createTag(self::PARAMS);

        $id = $response->result->itemId;

        $api = $this->callApi(
            ActionsInterface::TAG_VIEW,
            ['id' => $id]
        );

        $response = self::processJsonResponse($api);

        $this->assertEquals(0, $response->result->resultCode);
        $this->assertInstanceOf(stdClass::class, $response->result);
        $this->assertEquals($id, $response->result->itemId);

        $resultItem = $response->result->result;

        $this->assertEquals(self::PARAMS['name'], $resultItem->name);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testViewActionNonExistant(): void
    {
        $api = $this->callApi(
            ActionsInterface::TAG_VIEW,
            ['id' => 10]
        );

        $response = self::processJsonResponse($api);

        $this->assertInstanceOf(stdClass::class, $response->error);
        $this->assertEquals('Tag not found', $response->error->message);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testEditAction(): void
    {
        $response = $this->createTag(self::PARAMS);

        $id = $response->result->itemId;

        $params = [
            'id' => $id,
            'name' => 'API test edit'
        ];

        $api = $this->callApi(
            ActionsInterface::TAG_EDIT,
            $params
        );

        $response = self::processJsonResponse($api);

        $this->assertEquals(0, $response->result->resultCode);
        $this->assertInstanceOf(stdClass::class, $response->result);
        $this->assertEquals('Tag updated', $response->result->resultMessage);
        $this->assertEquals($id, $response->result->itemId);

        $api = $this->callApi(
            ActionsInterface::TAG_VIEW,
            ['id' => $id]
        );

        $response = self::processJsonResponse($api);

        $this->assertEquals(0, $response->result->resultCode);
        $this->assertInstanceOf(stdClass::class, $response->result);
        $this->assertEquals($id, $response->result->itemId);

        $resultItem = $response->result->result;

        $this->assertEquals($params['name'], $resultItem->name);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testEditActionDuplicated(): void
    {
        $response = $this->createTag(self::PARAMS);

        $id = $response->result->itemId;

        $params = [
            'id' => $id,
            'name' => 'linux'
        ];

        $api = $this->callApi(
            ActionsInterface::TAG_EDIT,
            $params
        );

        $response = self::processJsonResponse($api);

        $this->assertInstanceOf(stdClass::class, $response->error);
        $this->assertEquals('Duplicated tag', $response->error->message);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testEditActionWrongParameters(): void
    {
        $response = $this->createTag(self::PARAMS);

        $id = $response->result->itemId;

        $params = [
            'id' => $id
        ];

        $api = $this->callApi(
            ActionsInterface::TAG_EDIT,
            $params
        );

        $response = self::processJsonResponse($api);

        $this->assertInstanceOf(stdClass::class, $response->error);
        $this->assertEquals('Wrong parameters', $response->error->message);
        $this->assertInstanceOf(stdClass::class, $response->error->data);
        $this->assertIsArray($response->error->data->help);

    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testEditActionNonExistant(): void
    {
        $params = [
            'id' => 10,
            'name' => 'API test edit'
        ];

        $api = $this->callApi(
            ActionsInterface::TAG_EDIT,
            $params
        );

        $response = self::processJsonResponse($api);

        $this->assertEquals(0, $response->result->resultCode);
        $this->assertEquals(0, $response->result->count);
    }

    /**
     * @dataProvider searchProvider
     *
     * @throws \DI\DependencyException
     * @throws \JsonException
     * @throws \DI\NotFoundException
     */
    public function testSearchActionByFilter(array $filter, int $resultsCount): void
    {
        $api = $this->callApi(
            ActionsInterface::TAG_SEARCH,
            $filter
        );

        $response = self::processJsonResponse($api);

        $this->assertEquals(0, $response->result->resultCode);
        $this->assertEquals($resultsCount, $response->result->count);
        $this->assertCount($resultsCount, $response->result->result);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testDeleteAction(): void
    {
        $response = $this->createTag();

        $id = $response->result->itemId;

        $api = $this->callApi(
            ActionsInterface::TAG_DELETE,
            ['id' => $id]
        );

        $response = self::processJsonResponse($api);

        $this->assertEquals(0, $response->result->resultCode);
        $this->assertInstanceOf(stdClass::class, $response->result);
        $this->assertEquals('Tag removed', $response->result->resultMessage);
        $this->assertEquals($id, $response->result->itemId);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testDeleteActionNonExistant(): void
    {
        $api = $this->callApi(
            ActionsInterface::TAG_DELETE,
            ['id' => 10]
        );

        $response = self::processJsonResponse($api);

        $this->assertInstanceOf(stdClass::class, $response->error);
        $this->assertEquals('Tag not found', $response->error->message);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \JsonException
     */
    public function testDeleteActionRequiredParameters(): void
    {
        $api = $this->callApi(
            ActionsInterface::TAG_DELETE,
            []
        );

        $response = self::processJsonResponse($api);

        $this->assertInstanceOf(stdClass::class, $response->error);
        $this->assertEquals('Wrong parameters', $response->error->message);
        $this->assertInstanceOf(stdClass::class, $response->error->data);
        $this->assertIsArray($response->error->data->help);

    }

    public static function searchProvider(): array
    {
        return [
            [
                [],
                3
            ],
            [
                ['count' => 1],
                1
            ],
            [
                ['text' => 'Linux'],
                1
            ],
            [
                ['text' => 'Google'],
                0
            ]
        ];
    }
}
