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

namespace SP\Infrastructure\File;

/**
 * Interface StorageInterface
 *
 * @package SP\Storage
 */
interface XmlFileStorageInterface
{
    /**
     * @throws FileException
     */
    public function load(string $node = ''): XmlFileStorageInterface;

    /**
     * @param  mixed  $data  Data to be saved
     * @param  string  $node
     *
     * @throws FileException
     */
    public function save($data, string $node = ''): XmlFileStorageInterface;

    public function getItems();

    /**
     * Returns the given path node value
     *
     * @throws \SP\Infrastructure\File\FileException
     */
    public function getPathValue(string $path): string;

    public function getFileHandler(): FileHandlerInterface;
}