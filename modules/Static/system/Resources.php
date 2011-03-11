<?php
/****************************************************************************
 * Copyleft meh. [http://meh.doesntexist.org | meh@paranoici.org]           *
 *                                                                          *
 * This file is part of miniLOL. A PHP implementation.                      *
 *                                                                          *
 * miniLOL is free software: you can redistribute it and/or modify          *
 * it under the terms of the GNU Affero General Public License as           *
 * published by the Free Software Foundation, either version 3 of the       *
 * License, or (at your option) any later version.                          *
 *                                                                          *
 * miniLOL is distributed in the hope that it will be useful,               *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 * GNU Affero General Public License for more details.                      *
 *                                                                          *
 * You should have received a copy of the GNU Affero General Public License *
 * along with miniLOL.  If not, see <http://www.gnu.org/licenses/>.         *
 ****************************************************************************/

require(STATIC_SYSTEM.'/Resource.php');

class Resources
{
    private $_resources;

    public function __construct ()
    {
        $this->_resources = array();

        foreach (glob(STATIC_ADAPTERS.'/resources/*.php') as $resource) {
            require $resource;

            preg_match('#([^/]*)\.php$#', $resource, $matches);
            $class = "{$matches[1]}Resource";

            $resource = new $class;

            $this->_resources[$resource->name()] = $resource;
        }

    }

    public function &get ($name)
    {
        return $this->_resources[$name];
    }

    public function reload ()
    {
        foreach ($this->_resources as $resource) {
            $resource->reload();
        }
    }
}

?>
