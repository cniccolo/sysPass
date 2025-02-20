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
 * @var  PluginModel $plugin
 * @var  PluginInterface $pluginInfo
 * @var ThemeIcons $icons
 * @var \SP\Domain\Config\Ports\ConfigDataInterface $configData
 * @var callable $_getvar
 * @var \SP\Mvc\View\TemplateInterface $this
 */

use SP\Core\UI\ThemeIcons;
use SP\Infrastructure\Plugin\Repositories\PluginModel;
use SP\Plugin\PluginInterface;

$plugin = $_getvar('plugin');
$pluginInfo = $_getvar('pluginInfo');
?>

<div id="box-popup">
    <h2 class="center"><?php echo $_getvar('header'); ?><i
                class="btn-popup-close material-icons">close</i></h2>

    <form method="post" name="frmCategories" id="frmPlugins">
        <table class="popup-data">
            <tbody>
            <tr>
                <td class="descField"><?php echo __('Name'); ?></td>
                <td class="valField">
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input id="name" name="name" type="text"
                               class="mdl-textfield__input mdl-color-text--indigo-400"
                               value="<?php echo $plugin->getName(); ?>" <?php echo $_getvar('readonly'); ?>>
                        <label class="mdl-textfield__label"
                               for="name"><?php echo __('Plugin name'); ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="descField"><?php echo __('Version'); ?></td>
                <td class="valField">
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input id="version" name="version" type="text"
                               class="mdl-textfield__input mdl-color-text--indigo-400"
                               value="<?php echo implode('.', $pluginInfo->getVersion()); ?>" <?php echo $_getvar('readonly'); ?>>
                        <label class="mdl-textfield__label"
                               for="version"><?php echo __('Plugin version'); ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="descField"><?php echo __('Compatible Version'); ?></td>
                <td class="valField">
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input id="compatible_version" name="compatible_version"
                               type="text"
                               class="mdl-textfield__input mdl-color-text--indigo-400"
                               value="<?php echo implode('.', $pluginInfo->getCompatibleVersion()); ?>" <?php echo $_getvar('readonly'); ?>>
                        <label class="mdl-textfield__label"
                               for="compatible_version"><?php echo __('sysPass compatible version'); ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="descField"><?php echo __('Author'); ?></td>
                <td class="valField">
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input id="author" name="author" type="text"
                               class="mdl-textfield__input mdl-color-text--indigo-400"
                               value="<?php echo $pluginInfo->getAuthor(); ?>" <?php echo $_getvar('readonly'); ?>>
                        <label class="mdl-textfield__label"
                               for="author"><?php echo __('Plugin author'); ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="descField"><?php echo __('Events'); ?></td>
                <td class="valField">
                    <div class="lowres-title"><?php echo __('Events'); ?></div>

                    <div class="list-wrap">
                        <ul class="mdl-list">
                            <?php foreach ($pluginInfo->getEvents() as $event): ?>
                                <li class="mdl-list__item">
                                <span class="mdl-list__item-primary-content">
                                <i class="material-icons mdl-list__item-icon">flash_on</i>
                                    <?php echo $event; ?>
                                </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
