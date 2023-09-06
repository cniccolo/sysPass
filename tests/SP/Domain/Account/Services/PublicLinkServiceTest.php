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

namespace SP\Tests\Domain\Account\Services;

use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use SP\Core\Context\ContextInterface;
use SP\Core\Crypt\CryptInterface;
use SP\DataModel\ItemSearchData;
use SP\DataModel\PublicLinkData;
use SP\Domain\Account\Ports\AccountServiceInterface;
use SP\Domain\Account\Ports\PublicLinkRepositoryInterface;
use SP\Domain\Account\Services\PublicLinkService;
use SP\Domain\Common\Models\Simple;
use SP\Domain\Common\Services\ServiceException;
use SP\Http\RequestInterface;
use SP\Infrastructure\Common\Repositories\NoSuchItemException;
use SP\Infrastructure\Database\QueryResult;
use SP\Tests\Generators\PublicLinkDataGenerator;
use SP\Tests\UnitaryTestCase;

/**
 * Class PublicLinkServiceTest
 *
 * @group unitary
 */
class PublicLinkServiceTest extends UnitaryTestCase
{

    private PublicLinkRepositoryInterface|MockObject $publicLinkRepository;
    private RequestInterface|MockObject              $request;
    private MockObject|PublicLinkService             $publicLinkService;
    private CryptInterface|MockObject                $crypt;
    private MockObject|AccountServiceInterface       $accountService;

    /**
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function testAddLinkView()
    {
        $publicLinkData = new PublicLinkData();
        $publicLinkData->setHash(self::$faker->sha1);

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('addLinkView')
            ->with(
                new Callback(function (PublicLinkData $publicLinkData) {
                    $useInfo = unserialize($publicLinkData->getUseInfo(), ['allowed_classes' => false]);

                    return is_array($useInfo) && count($useInfo) === 1;
                })
            );

        $this->publicLinkService->addLinkView($publicLinkData);
    }

    /**
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function testAddLinkViewWithoutHash()
    {
        $publicLinkData = new PublicLinkData();

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage('Public link hash not set');

        $this->publicLinkService->addLinkView($publicLinkData);
    }

    /**
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function testAddLinkViewWithUseInfo()
    {
        $publicLinkData = new PublicLinkData();
        $publicLinkData->setHash(self::$faker->sha1);
        $publicLinkData->setUseInfo([
            [
                'who'   => self::$faker->ipv4,
                'time'  => time(),
                'hash'  => self::$faker->sha1,
                'agent' => self::$faker->userAgent,
                'https' => self::$faker->boolean,
            ],
        ]);

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('addLinkView')
            ->with(
                new Callback(function (PublicLinkData $publicLinkData) {
                    $useInfo = unserialize($publicLinkData->getUseInfo(), ['allowed_classes' => false]);

                    return is_array($useInfo) && count($useInfo) === 2;
                })
            );

        $this->publicLinkService->addLinkView($publicLinkData);
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testGetByHash()
    {
        $hash = self::$faker->sha1;
        $publicLink = PublicLinkDataGenerator::factory()->buildPublicLink();
        $result = new QueryResult([new Simple($publicLink->toArray())]);

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getByHash')
            ->with($hash)
            ->willReturn($result);

        $actual = $this->publicLinkService->getByHash($hash);

        $this->assertEquals($publicLink, $actual);
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testGetByHashNotFound()
    {
        $hash = self::$faker->sha1;

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getByHash')
            ->with($hash)
            ->willReturn(new QueryResult([]));

        $this->expectException(NoSuchItemException::class);
        $this->expectExceptionMessage('Link not found');

        $this->publicLinkService->getByHash($hash);
    }

    /**
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function testDeleteByIdBatch()
    {
        $ids = array_map(fn() => self::$faker->randomNumber(), range(0, 9));

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('deleteByIdBatch')
            ->with($ids)
            ->willReturn(10);

        $actual = $this->publicLinkService->deleteByIdBatch($ids);

        $this->assertEquals(count($ids), $actual);
    }

    /**
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Domain\Common\Services\ServiceException
     */
    public function testDeleteByIdBatchWithCountMismatch()
    {
        $ids = array_map(fn() => self::$faker->randomNumber(), range(0, 9));

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('deleteByIdBatch')
            ->with($ids)
            ->willReturn(1);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage('Error while removing the links');

        $this->publicLinkService->deleteByIdBatch($ids);
    }

    public function testCreateLinkHash()
    {
        $this->assertNotEmpty(PublicLinkService::createLinkHash());
    }

    /**
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testUpdate()
    {
        $publicLinkList = PublicLinkDataGenerator::factory()->buildPublicLinkList();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('update')
            ->with($publicLinkList);

        $this->publicLinkService->update($publicLinkList);
    }

    /**
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     */
    public function testDelete()
    {
        $id = self::$faker->randomNumber();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('delete')
            ->with($id);

        $this->publicLinkService->delete($id);
    }

    public function testSearch()
    {
        $itemSearchData = new ItemSearchData(self::$faker->colorName);

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('search')
            ->with($itemSearchData);

        $this->publicLinkService->search($itemSearchData);
    }

    /**
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testGetHashForItem()
    {
        $itemId = self::$faker->randomNumber();
        $publicLinkData = PublicLinkDataGenerator::factory()->buildPublicLink();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getHashForItem')
            ->with($itemId)
            ->willReturn(new QueryResult([new Simple($publicLinkData->toArray())]));

        $actual = $this->publicLinkService->getHashForItem($itemId);

        $this->assertEquals($publicLinkData, $actual);
    }

    /**
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testGetHashForItemNotFound()
    {
        $itemId = self::$faker->randomNumber();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getHashForItem')
            ->with($itemId)
            ->willReturn(new QueryResult([]));

        $this->expectException(NoSuchItemException::class);
        $this->expectExceptionMessage('Link not found');

        $this->publicLinkService->getHashForItem($itemId);
    }

    /**
     * @throws \Defuse\Crypto\Exception\CryptoException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Domain\Common\Services\ServiceException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testRefresh()
    {
        $id = self::$faker->randomNumber();
        $publicLinkData = PublicLinkDataGenerator::factory()->buildPublicLink();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getById')
            ->with($id)
            ->willReturn(new QueryResult([new Simple($publicLinkData->toArray())]));

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('refresh')
            ->with(
                new Callback(function (PublicLinkData $actual) use ($publicLinkData) {
                    $filter = ['hash', 'dateExpire', 'maxCountViews', 'data'];

                    return $actual->toArray(null, $filter) === $publicLinkData->toArray(null, $filter)
                           && !empty($actual->getHash())
                           && !empty($actual->getDateExpire())
                           && !empty($actual->getMaxCountViews())
                           && !empty($actual->getData());
                })
            )
            ->willReturn(true);

        $passData = ['pass' => self::$faker->password, 'key' => self::$faker->sha1];

        $this->accountService
            ->expects(self::once())
            ->method('getDataForLink')
            ->with($publicLinkData->getItemId())
            ->willReturn(new Simple($passData));

        $this->crypt
            ->expects(self::once())
            ->method('decrypt')
            ->with(
                $passData['pass'],
                $passData['key'],
                $this->context->getTrasientKey(ContextInterface::MASTER_PASSWORD_KEY)
            )
            ->willReturn(self::$faker->password);

        $actual = $this->publicLinkService->refresh($id);

        $this->assertTrue($actual);
    }

    /**
     * @throws \Defuse\Crypto\Exception\CryptoException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Domain\Common\Services\ServiceException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testRefreshNotFound()
    {
        $id = self::$faker->randomNumber();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getById')
            ->with($id)
            ->willReturn(new QueryResult([]));

        $this->expectException(NoSuchItemException::class);
        $this->expectExceptionMessage('Link not found');

        $this->publicLinkService->refresh($id);
    }

    /**
     * @return void
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function testGetPublicLinkKey()
    {
        $hash = self::$faker->sha1;

        $actual = $this->publicLinkService->getPublicLinkKey($hash);

        $this->assertEquals($hash, $actual->getHash());
        $this->assertNotEmpty($actual->getKey());
    }

    /**
     * @return void
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function testGetPublicLinkKeyWithoutHash()
    {
        $actual = $this->publicLinkService->getPublicLinkKey();

        $this->assertNotEmpty($actual->getHash());
        $this->assertNotEmpty($actual->getKey());
    }

    /**
     * @return void
     * @throws \SP\Core\Exceptions\SPException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function testGetById()
    {
        $itemId = self::$faker->randomNumber();
        $builPublicLinkList = PublicLinkDataGenerator::factory()->buildPublicLinkList();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getById')
            ->with($itemId)
            ->willReturn(new QueryResult([new Simple($builPublicLinkList->toArray())]));

        $actual = $this->publicLinkService->getById($itemId);

        $this->assertEquals($builPublicLinkList->toArray(null, ['clientName']), $actual->toArray());
    }

    /**
     * @return void
     * @throws \SP\Core\Exceptions\SPException
     * @throws \SP\Infrastructure\Common\Repositories\NoSuchItemException
     */
    public function testGetByIdNotFound()
    {
        $itemId = self::$faker->randomNumber();

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('getById')
            ->with($itemId)
            ->willReturn(new QueryResult([]));

        $this->expectException(NoSuchItemException::class);
        $this->expectExceptionMessage('Link not found');

        $this->publicLinkService->getById($itemId);
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testGetAllBasic()
    {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage('Not implemented');

        $this->publicLinkService->getAllBasic();
    }

    public function testGetUseInfo()
    {
        $hash = self::$faker->sha1;
        $who = self::$faker->ipv4;
        $userAgent = self::$faker->userAgent;

        $request = $this->createMock(RequestInterface::class);

        $request->expects(self::once())
                ->method('getClientAddress')
                ->with(true)
                ->willReturn($who);

        $request->expects(self::once())
                ->method('getHeader')
                ->with('User-Agent')
                ->willReturn($userAgent);

        $request->expects(self::once())
                ->method('isHttps')
                ->willReturn(true);

        $actual = PublicLinkService::getUseInfo($hash, $request);

        $this->assertArrayHasKey('who', $actual);
        $this->assertArrayHasKey('time', $actual);
        $this->assertArrayHasKey('hash', $actual);
        $this->assertArrayHasKey('agent', $actual);
        $this->assertArrayHasKey('https', $actual);
        $this->assertEquals($who, $actual['who']);
        $this->assertEquals($hash, $actual['hash']);
        $this->assertEquals($userAgent, $actual['agent']);
        $this->assertTrue($actual['https']);
    }

    /**
     * @return void
     * @throws \Defuse\Crypto\Exception\CryptoException
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testCreate()
    {
        $publicLinkData = PublicLinkDataGenerator::factory()->buildPublicLink();
        $result = new QueryResult();
        $result->setLastId(self::$faker->randomNumber());

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('create')
            ->with(
                new Callback(function (PublicLinkData $actual) use ($publicLinkData) {
                    $filter = ['hash', 'dateExpire', 'maxCountViews', 'data'];

                    return $actual->toArray(null, $filter) === $publicLinkData->toArray(null, $filter)
                           && !empty($actual->getHash())
                           && !empty($actual->getDateExpire())
                           && !empty($actual->getMaxCountViews())
                           && !empty($actual->getData());
                })
            )
            ->willReturn($result);

        $passData = ['pass' => self::$faker->password, 'key' => self::$faker->sha1];

        $this->accountService
            ->expects(self::once())
            ->method('getDataForLink')
            ->with($publicLinkData->getItemId())
            ->willReturn(new Simple($passData));

        $this->crypt
            ->expects(self::once())
            ->method('decrypt')
            ->with(
                $passData['pass'],
                $passData['key'],
                $this->context->getTrasientKey(ContextInterface::MASTER_PASSWORD_KEY)
            )
            ->willReturn(self::$faker->password);

        $actual = $this->publicLinkService->create($publicLinkData);

        $this->assertEquals($result->getLastId(), $actual);
    }

    public function testCalcDateExpire()
    {
        $expireDate = time() + $this->config->getConfigData()->getPublinksMaxTime();

        $this->assertEqualsWithDelta($expireDate, PublicLinkService::calcDateExpire($this->config), 2);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicLinkRepository = $this->createMock(PublicLinkRepositoryInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->request->method('getClientAddress')
                      ->willReturn(self::$faker->ipv4);
        $this->request->method('getHeader')
                      ->willReturn(self::$faker->userAgent);
        $this->request->method('isHttps')
                      ->willReturn(self::$faker->boolean);

        $this->accountService = $this->createMock(AccountServiceInterface::class);
        $this->crypt = $this->createMock(CryptInterface::class);

        $this->context->setTrasientKey(ContextInterface::MASTER_PASSWORD_KEY, self::$faker->password);

        $this->publicLinkService =
            new PublicLinkService(
                $this->application,
                $this->publicLinkRepository,
                $this->request,
                $this->accountService,
                $this->crypt
            );
    }
}
