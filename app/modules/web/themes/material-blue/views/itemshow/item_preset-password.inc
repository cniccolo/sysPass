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
 * @var Password $password
 * @var ThemeIcons $icons
 * @var \SP\Domain\Config\Ports\ConfigDataInterface $configData
 * @var callable $_getvar
 * @var \SP\Mvc\View\TemplateInterface $this
 */

use SP\Core\UI\ThemeIcons;
use SP\DataModel\ItemPreset\Password;

$password = $_getvar('password');
?>

<table class="popup-data">
    <tbody>
    <tr>
        <td class="descField"><?php echo __('Length'); ?></td>
        <td class="valField">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input id="timeout" name="length" type="number"
                       class="mdl-textfield__input mdl-color-text--indigo-400"
                       min="1" max="100"
                       value="<?php echo $password->getLength() ?: 1; ?>"/>
                <label class="mdl-textfield__label"
                       for="length"><?php echo __('Length'); ?></label>
            </div>
        </td>
    </tr>

    <tr>
        <td class="descField"><?php echo __('Accounts password expiry'); ?></td>
        <td class="valField">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input id="expire_time" name="expire_time" type="number"
                       class="mdl-textfield__input mdl-color-text--indigo-400"
                       min="0"
                       value="<?php echo $password->getExpireTime() ? $password->getExpireTime() / $_getvar('expireTimeMultiplier') : 1; ?>"/>
                <label class="mdl-textfield__label"
                       for="expire_time"><?php echo __('Expire time (days)'); ?></label>
            </div>
        </td>
    </tr>

    <tr>
        <td class="descField"><?php echo __('Score'); ?></td>
        <td class="valField">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input id="score" name="score" type="number"
                       class="mdl-textfield__input mdl-color-text--indigo-400"
                       min="0" max="5"
                       value="<?php echo $password->getScore(); ?>"
                       disabled="disabled" readonly/>
                <label class="mdl-textfield__label"
                       for="score"><?php echo __('Score'); ?></label>
            </div>
        </td>
    </tr>

    <tr>
        <td class="descField"><?php echo __('Regular Expression'); ?></td>
        <td class="valField">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input id="regex" name="regex" type="text"
                       class="mdl-textfield__input mdl-color-text--indigo-400"
                       value="<?php echo htmlspecialchars($password->getRegex(), ENT_QUOTES); ?>"/>
                <label class="mdl-textfield__label"
                       for="regex"><?php echo __('Regular Expression'); ?></label>
            </div>
        </td>
    </tr>

    <tr>
        <td class="descField"></td>
        <td class="valField">
            <ul class="password-list-action mdl-list">
                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="use_numbers_enabled">
                            <input type="checkbox" id="use_numbers_enabled"
                                   class="mdl-switch__input"
                                   name="use_numbers_enabled" <?php echo $password->isUseNumbers() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Include Numbers'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="use_letters_enabled">
                            <input type="checkbox" id="use_letters_enabled"
                                   class="mdl-switch__input"
                                   name="use_letters_enabled" <?php echo $password->isUseLetters() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Include Letters'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="use_symbols_enabled">
                            <input type="checkbox" id="use_symbols_enabled"
                                   class="mdl-switch__input"
                                   name="use_symbols_enabled" <?php echo $password->isUseSymbols() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Include Symbols'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="use_upper_enabled">
                            <input type="checkbox" id="use_upper_enabled"
                                   class="mdl-switch__input"
                                   name="use_upper_enabled" <?php echo $password->isUseUpper() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Include Uppercase'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="use_lower_enabled">
                            <input type="checkbox" id="use_lower_enabled"
                                   class="mdl-switch__input"
                                   name="use_lower_enabled" <?php echo $password->isUseLower() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Include Lowercase'); ?></span>
                    </span>
                </li>

                <li class="mdl-list__item mdl-list__item--two-line">
                    <div class="mdl-switch__box">
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                               for="use_image_enabled">
                            <input type="checkbox" id="use_image_enabled"
                                   class="mdl-switch__input"
                                   name="use_image_enabled" <?php echo $password->isUseImage() ? 'checked' : ''; ?>/>
                        </label>
                    </div>

                    <span class="mdl-list__item-primary-content">
                        <span><?php echo __('Image to show password'); ?></span>
                        <span class="mdl-list__item-sub-title">
                            <?php echo __('Generate an image with a text of the account password.'); ?>
                            <br>
                            <?php echo __('Useful for environments where copying a password to clipboard is a security risk.'); ?>
                        </span>
                    </span>
                </li>
            </ul>
        </td>
    </tr>
    </tbody>
</table>
