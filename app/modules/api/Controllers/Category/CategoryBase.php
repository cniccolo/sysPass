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

namespace SP\Modules\Api\Controllers\Category;


use Klein\Klein;
use SP\Core\Acl\Acl;
use SP\Core\Application;
use SP\Domain\Api\Ports\ApiServiceInterface;
use SP\Domain\Category\Ports\CategoryAdapterInterface;
use SP\Domain\Category\Ports\CategoryServiceInterface;
use SP\Modules\Api\Controllers\ControllerBase;
use SP\Modules\Api\Controllers\Help\CategoryHelp;

/**
 * Class CategoryBase
 */
abstract class CategoryBase extends ControllerBase
{
    protected CategoryServiceInterface $categoryService;
    protected CategoryAdapterInterface $categoryAdapter;

    /**
     * @throws \SP\Core\Exceptions\InvalidClassException
     */
    public function __construct(
        Application $application,
        Klein $router,
        ApiServiceInterface $apiService,
        Acl $acl,
        CategoryServiceInterface $categoryService,
        CategoryAdapterInterface $categoryAdapter
    ) {
        parent::__construct($application, $router, $apiService, $acl);

        $this->categoryService = $categoryService;
        $this->categoryAdapter = $categoryAdapter;

        $this->apiService->setHelpClass(CategoryHelp::class);
    }

}
