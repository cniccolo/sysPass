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

/**
 * @var DataGridAction|DataGridActionSearch $action
 * @var SP\Html\DataGrid\DataGridTab $data
 * @var ThemeIcons $icons
 * @var \SP\Domain\Config\Ports\ConfigDataInterface $configData
 * @var callable $_getvar
 * @var \SP\Mvc\View\TemplateInterface $this
 */

use SP\Core\UI\ThemeIcons;
use SP\Html\DataGrid\Action\DataGridAction;
use SP\Html\DataGrid\Action\DataGridActionSearch;
use SP\Html\DataGrid\Action\DataGridActionType;

if (!isset($data)) {
    $data = $_getvar('data');
}

$index = $index ?? 0;
?>

<div class="tab-actions">
    <ul>
        <?php foreach ($data->getDataActions() as $action):
            if ($action->getType() === DataGridActionType::MENUBAR_ITEM):
                ?>
                <li>
                    <button
                            id="btn-add-<?php echo $action->getId(); ?>"
                            type="button"
                            class="btn-action mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-color--indigo-A200 mdl-js-ripple-effect"
                            data-nextaction="<?php echo $data->getOnCloseAction(); ?>"
                            data-onclick="<?php echo $action->getOnClick(); ?>"
                        <?php foreach ($action->getData() as $dataName => $dataValue): echo 'data-', $dataName, '=', '"', $dataValue, '"'; endforeach; ?>>
                        <i class="material-icons"><?php echo $action->getIcon()->getIcon(); ?></i>
                    </button>
                    <span for="btn-add-<?php echo $action->getId(); ?>"
                          class="mdl-tooltip mdl-tooltip--bottom"
                          title="<?php echo $action->getTitle(); ?>"><?php echo $action->getName(); ?></span>
                </li>
            <?php elseif ($action->getType() === DataGridActionType::SEARCH_ITEM): ?>
                <li class="datagrid-action-search">
                    <form method="post" id="<?php echo $action->getName(); ?>"
                          name="<?php echo $action->getName(); ?>"
                          class="form-action"
                          data-target="#data-table-<?php echo $data->getId(); ?>"
                          data-onsubmit="<?php echo $action->getOnSubmit(); ?>"
                        <?php foreach ($action->getData() as $dataName => $dataValue): echo 'data-', $dataName, '=', '"', $dataValue, '"'; endforeach; ?>>

                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                            <label class="mdl-button mdl-js-button mdl-button--icon"
                                   id="btn-search-<?php echo $index; ?>"
                                   for="search-<?php echo $index; ?>">
                                <i class="material-icons">search</i>
                            </label>
                            <span for="btn-search-<?php echo $index; ?>"
                                  class="mdl-tooltip mdl-tooltip--bottom"><?php echo $action->getTitle(); ?></span>

                            <div class="mdl-textfield__expandable-holder">
                                <input class="mdl-textfield__input" type="text"
                                       id="search-<?php echo $index; ?>"
                                       name="search"/>
                                <label class="mdl-textfield__label"
                                       for="search-<?php echo $index; ?>"><?php echo $action->getTitle(); ?></label>
                            </div>
                        </div>

                        <button type="reset"
                                id="btn-reset-<?php echo $index; ?>"
                                class="btn-clear mdl-button mdl-js-button mdl-button--icon">
                            <i class="material-icons"><?php echo $icons->getIconClear()->getIcon(); ?></i>
                        </button>

                        <span for="btn-reset-<?php echo $index; ?>"
                              class="mdl-tooltip mdl-tooltip--bottom"><?php echo $icons->getIconClear()->getTitle(); ?></span>

                        <input type="hidden" name="activetab"
                               value="<?php echo $index; ?>">
                        <input type="hidden" name="start" value="0">
                        <input type="hidden" name="count"
                               value="<?php echo $data->getPager()->getLimitCount(); ?>">
                        <input type="hidden" name="isAjax" value="1">
                    </form>
                </li>
            <?php elseif ($action->getType() === DataGridActionType::HELP_ITEM): ?>
                <li>
                    <?php include $this->includePartial('help_account_search'); ?>
                </li>
            <?php
            endif;
        endforeach; ?>

        <li>
            <button type="button" id="btn-back-<?php echo $index; ?>"
                    class="btn-back mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored <?php echo $icons->getIconBack()->getClassButton(); ?>"
                    title="<?php echo $icons->getIconBack()->getTitle(); ?>">
                <i class="material-icons"><?php echo $icons->getIconBack()->getIcon(); ?></i>
            </button>
            <span for="btn-back-<?php echo $index; ?>"
                  class="mdl-tooltip mdl-tooltip--bottom"><?php echo $icons->getIconBack()->getTitle(); ?></span>
        </li>

        <?php if ($data->getDataActionsMenuCount() > 0): ?>
            <li>
                <button id="menu-<?php echo $data->getId(); ?>-menu-lower-right"
                        class="mdl-button mdl-js-button mdl-button--icon">
                    <i class="material-icons">more_vert</i>
                </button>

                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
                    for="menu-<?php echo $data->getId(); ?>-menu-lower-right">
                    <?php foreach ($data->getDataActionsMenu() as $action): ?>
                        <li class="btn-action mdl-menu__item"
                            title="<?php echo $action->getTitle(); ?>"
                            <?php if ($action->isSelection()): ?>
                                data-selection="#data-table-<?php echo $data->getId(); ?>"
                            <?php endif; ?>
                            data-onclick="<?php echo $action->getOnClick(); ?>"
                            <?php foreach ($action->getData() as $dataName => $dataValue): echo 'data-', $dataName, '=', '"', $dataValue, '"'; endforeach; ?>>
                            <i class="material-icons <?php echo $action->getIcon()->getClass(); ?>"><?php echo $action->getIcon()->getIcon(); ?></i>
                            <?php echo $action->getName(); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</div>

<div id="data-table-<?php echo $data->getId(); ?>">
    <?php if ($data->getDataTableTemplate()): ?>
        <?php include $data->getDataTableTemplate(); ?>
    <?php else: ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'datagrid-table.inc'; ?>
    <?php endif; ?>
</div>
