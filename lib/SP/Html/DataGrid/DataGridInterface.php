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

namespace SP\Html\DataGrid;

defined('APP_ROOT') || die();

use SP\Html\DataGrid\Action\DataGridActionInterface;
use SP\Html\DataGrid\Layout\DataGridHeaderInterface;
use SP\Html\DataGrid\Layout\DataGridPagerInterface;

/**
 * Interface DataGridInterface
 *
 * @package SP\Html\DataGrid
 */
interface DataGridInterface
{
    /**
     * @param $id string
     */
    public function setId(string $id);

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param DataGridHeaderInterface $header
     */
    public function setHeader(DataGridHeaderInterface $header);

    /**
     * @return DataGridHeaderInterface
     */
    public function getHeader(): DataGridHeaderInterface;

    /**
     * @param DataGridDataInterface $data
     */
    public function setData(DataGridDataInterface $data);

    /**
     * @return DataGridDataInterface
     */
    public function getData();

    /**
     * @param DataGridActionInterface $action
     * @param bool                    $isMenu Añadir al menu de acciones
     *
     * @return $this
     */
    public function addDataAction(DataGridActionInterface $action, $isMenu = false): DataGridInterface;

    /**
     * @return DataGridActionInterface[]
     */
    public function getDataActions(): array;

    /**
     * @return mixed
     */
    public function getGrid();

    /**
     * Establecer el paginador
     *
     * @param DataGridPagerInterface $pager
     */
    public function setPager(DataGridPagerInterface $pager);

    /**
     * Devolver el paginador
     *
     * @return DataGridPagerInterface|null
     */
    public function getPager(): ?DataGridPagerInterface;

    /**
     * @param int $action
     */
    public function setOnCloseAction(int $action);

    /**
     * Establecer la plantilla utilizada para la cabecera
     *
     * @param string $template El nombre de la plantilla a utilizar
     */
    public function setDataHeaderTemplate(string $template);

    /**
     * Devolver la plantilla utilizada para la cabecera
     *
     * @return string
     */
    public function getDataHeaderTemplate(): string;

    /**
     * Establecer la plantilla utilizada para las acciones
     *
     * @param string $template El nombre de la plantilla a utilizar
     */
    public function setDataActionsTemplate(string $template);

    /**
     * Devolver la plantilla utilizada para las acciones
     *
     * @return string|null
     */
    public function getDataActionsTemplate(): ?string;

    /**
     * Establecer la plantilla utilizada para el paginador
     *
     * @param string $template El nombre de la plantilla a utilizar
     */
    public function setDataPagerTemplate(string $template);

    /**
     * Devolver la plantilla utilizada para el paginador
     *
     * @return string|null
     */
    public function getDataPagerTemplate(): ?string;

    /**
     * Establcer la plantilla utilizada para los datos de la consulta
     *
     * @param string $template El nombre de la plantilla a utilizar
     */
    public function setDataRowTemplate(string $template);

    /**
     * Devolver la plantilla utilizada para los datos de la consulta
     *
     * @return string|null
     */
    public function getDataRowTemplate(): ?string;

    /**
     * Devuelve el tiempo total de carga del DataGrid
     *
     * @return int
     */
    public function getTime(): int;

    /**
     * Establece el tiempo total de carga del DataGrid
     *
     * @param int|float $time
     */
    public function setTime($time);

    /**
     * Devolver las acciones que se muestran en un menu
     *
     * @return DataGridActionInterface[]
     */
    public function getDataActionsMenu(): array;

    /**
     * Devolver las acciones filtradas
     *
     * @param $filter
     *
     * @return DataGridActionInterface[]
     */
    public function getDataActionsFiltered($filter): array;

    /**
     * Devolver las acciones de menu filtradas
     *
     * @param $filter
     *
     * @return DataGridActionInterface[]
     */
    public function getDataActionsMenuFiltered($filter): array;

    /**
     * Actualizar los datos del paginador
     *
     * @return static
     */
    public function updatePager(): DataGridInterface;
}