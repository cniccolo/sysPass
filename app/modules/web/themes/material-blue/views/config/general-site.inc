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
 * @var ThemeIcons $icons
 * @var \SP\Domain\Config\Ports\ConfigDataInterface $configData
 * @var callable $_getvar
 * @var \SP\Mvc\View\TemplateInterface $this
 */

use SP\Core\UI\ThemeIcons;
use SP\Mvc\View\Components\SelectItem;

?>

<?php if (!$_getvar('curlIsAvailable')): ?>
    <div class="msg-option-unvailable">
        <?php printf(__('The \'%s\' extension is unavailable'), 'curl'); ?>
        <i id="help-curl"
           class="material-icons"><?php echo $icons->getIconHelp()->getIcon(); ?></i>
        <div class="mdl-tooltip mdl-tooltip--large" for="help-curl">
            <p>
                <?php echo __('This extension is needed to check for sysPass updates and notices'); ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<div id="title" class="titleNormal">
    <i class="material-icons">home</i>
    <?php echo __('Site'); ?>
</div>

<table id="tblSite" class="data tblConfig">
    <tr>
        <td class="descField">
            <?php echo __('Language'); ?>
            <div id="help-sitelang"
                 class="icon material-icons <?php echo $icons->getIconHelp()->getClass(); ?>"><?php echo $icons->getIconHelp()->getIcon(); ?></div>
            <div class="mdl-tooltip mdl-tooltip--large" for="help-sitelang">
                <p>
                    <?php echo __('Sets the global app language. You can set a per user language on preferences.'); ?>
                </p>

                <p>
                    <?php echo __('To improve/add translations, you can get involved at https://poeditor.com/join/project/fmlsBuugyv'); ?>
                </p>
            </div>
        </td>
        <td class="valField">
            <div class="lowres-title"><?php echo __('Language'); ?></div>

            <select name="sitelang" id="sel-sitelang" size="1"
                    class="select-box sel-chosen-ns">
                <?php /** @var SelectItem $lang */
                foreach ($_getvar('langs') as $lang): ?>
                    <option
                            value='<?php echo $lang->getId(); ?>' <?php echo $lang->isSelected() ? 'selected' : ''; ?>><?php echo $lang->getName(); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="descField">
            <?php echo __('Visual Theme'); ?>
            <div id="help-sitetheme"
                 class="icon material-icons <?php echo $icons->getIconHelp()->getClass(); ?>"><?php echo $icons->getIconHelp()->getIcon(); ?></div>
            <div class="mdl-tooltip mdl-tooltip--large" for="help-sitetheme">
                <p>
                    <?php echo __('Sets the global application visual theme. You can set a per user visual theme on preferences.'); ?>
                </p>
            </div>
        </td>
        <td class="valField">
            <div class="lowres-title"><?php echo __('Visual Theme'); ?></div>

            <select name="sitetheme" id="sel-sitetheme" size="1"
                    class="select-box sel-chosen-ns">
                <?php /** @var SelectItem $theme */
                foreach ($_getvar('themes') as $theme): ?>
                    <option
                            value='<?php echo $theme->getId(); ?>' <?php echo $theme->isSelected() ? 'selected' : ""; ?>><?php echo $theme->getName(); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="descField">
            <?php echo __('Session timeout (s)'); ?>
        </td>
        <td class="valField">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input id="session_timeout" name="session_timeout" type="number"
                       min="0" step="300"
                       pattern="[0-9]{2,4}"
                       class="mdl-textfield__input mdl-color-text--indigo-400"
                       maxlength="5"
                       value="<?php echo $configData->getSessionTimeout(); ?>"
                       required/>
                <label class="mdl-textfield__label"
                       for="session_timeout"><?php echo __('Session timeout (s)'); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="descField">
            <?php echo __('Application URL'); ?>
            <div id="help-app_url"
                 class="icon material-icons <?php echo $icons->getIconHelp()->getClass(); ?>"><?php echo $icons->getIconHelp()->getIcon(); ?></div>
            <div class="mdl-tooltip mdl-tooltip--large" for="help-app_url">
                <p>
                    <?php echo __(
                        'Sets the application URL when accessing through a reverse proxy or load balancer.'
                    ); ?>
                </p>
            </div>
        </td>
        <td class="valField">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input id="app_url" name="app_url" type="url"
                       pattern="^https?://.*"
                       class="mdl-textfield__input mdl-color-text--indigo-400"
                       value="<?php echo htmlspecialchars($configData->getApplicationUrl(), ENT_QUOTES); ?>"/>
                <label class="mdl-textfield__label"
                       for="app_url"><?php echo __('Application URL'); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <td class="descField"></td>
        <td class="valField">
            <ul class="config-site-list-action mdl-list">
                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="https_enabled">
                            <input type="checkbox" id="https_enabled"
                                   class="mdl-switch__input"
                                   name="https_enabled" <?php echo $configData->isHttpsEnabled() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Force HTTPS'); ?></span>
                        <span class="mdl-list__item-sub-title"><?php echo __('It forces the use of HTTPS on all the connections'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="debug_enabled">
                            <input type="checkbox" id="debug_enabled"
                                   class="mdl-switch__input"
                                   name="debug_enabled" <?php echo $configData->isDebug() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Enable debug'); ?></span>
                        <span class="mdl-list__item-sub-title"><?php echo __('Displays some information about application configuration and performance.'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="maintenance_enabled">
                            <input type="checkbox" id="maintenance_enabled"
                                   class="mdl-switch__input"
                                   name="maintenance_enabled" <?php echo $configData->isMaintenance() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Maintenance mode'); ?></span>
                        <span class="mdl-list__item-sub-title"><?php echo __('In this mode nobody can log into the application. To disable it, you need to either modify the configuration file or unset on the site tab.'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="check_updates_enabled">
                            <input type="checkbox" id="check_updates_enabled"
                                   class="mdl-switch__input"
                                   name="check_updates_enabled" <?php echo $configData->isCheckUpdates() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Check for updates'); ?></span>
                        <span class="mdl-list__item-sub-title"><?php echo __('Check for application updates (only for administrators).'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="check_notices_enabled">
                            <input type="checkbox" id="check_notices_enabled"
                                   class="mdl-switch__input"
                                   name="check_notices_enabled" <?php echo $configData->isCheckNotices() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Check for notifications'); ?></span>
                        <span class="mdl-list__item-sub-title"><?php echo __('Check if there are either sysPass security or warning notices (only for administrators).'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="encrypt_session_enabled">
                            <input type="checkbox" id="encrypt_session_enabled"
                                   class="mdl-switch__input"
                                   name="encrypt_session_enabled" <?php echo $configData->isEncryptSession() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Encrypt Session'); ?></span>
                        <span class="mdl-list__item-sub-title">
                            <?php echo __('Encrypt PHP session data'); ?>
                            <?php echo __('This feature increases the PHP sessions security, because the stored data won\'t be readable.'); ?>
                        </span>
                    </span>
                </li>
            </ul>
        </td>
    </tr>
</table>
