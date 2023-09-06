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

use PHPUnit\Framework\MockObject\MockObject;
use SP\DataModel\PublickLinkOldData;
use SP\Domain\Account\Ports\PublicLinkRepositoryInterface;
use SP\Domain\Account\Services\UpgradePublicLinkService;
use SP\Domain\Common\Models\Simple;
use SP\Infrastructure\Database\QueryResult;
use SP\Tests\Generators\PublicLinkDataGenerator;
use SP\Tests\Stubs\PublicLinkRepositoryStub;
use SP\Tests\UnitaryTestCase;

/**
 * Class UpgradePublicLinkServiceTest
 *
 * @group unitary
 */
class UpgradePublicLinkServiceTest extends UnitaryTestCase
{

    private PublicLinkRepositoryInterface|MockObject $publicLinkRepository;
    private UpgradePublicLinkService                 $upgradePublicLinkService;

    public function testUpgrade_300_18010101()
    {
        $publicLink = PublicLinkDataGenerator::factory()->buildPublicLink();

        $publicLinkOld = new PublickLinkOldData();
        $publicLinkOld->setItemId($publicLink->getItemId());
        $publicLinkOld->setLinkHash($publicLink->getHash());
        $publicLinkOld->setUserId($publicLink->getUserId());
        $publicLinkOld->setTypeId($publicLink->getTypeId());
        $publicLinkOld->setNotify($publicLink->isNotify());
        $publicLinkOld->setDateAdd($publicLink->getDateAdd());
        $publicLinkOld->setDateExpire($publicLink->getDateExpire());
        $publicLinkOld->setCountViews($publicLink->getCountViews());
        $publicLinkOld->setMaxCountViews($publicLink->getMaxCountViews());
        $publicLinkOld->setUseInfo(unserialize($publicLink->getUseInfo(), ['allowed_classes' => false]));
        $publicLinkOld->setData($publicLink->getData());

        $result =
            new QueryResult([new Simple(['id' => $publicLink->getId(), 'data' => serialize($publicLinkOld)])]);

        $this->publicLinkRepository->expects(self::once())
                                   ->method('getAny')
                                   ->with(['id', 'data'], 'PublicLink')
                                   ->willReturn($result);

        $this->publicLinkRepository
            ->expects(self::once())
            ->method('update')
            ->with($publicLink->mutate(['dateUpdate' => null, 'totalCountViews' => null]));

        $this->upgradePublicLinkService->upgrade_300_18010101();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicLinkRepository =
            $this->getMockForAbstractClass(PublicLinkRepositoryStub::class);

        $this->upgradePublicLinkService = new UpgradePublicLinkService($this->application, $this->publicLinkRepository);
    }
}
