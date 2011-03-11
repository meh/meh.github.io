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

class ModulesResource extends Resource implements Iterator
{
    public function name ()
    {
        return 'miniLOL.modules';
    }

    public function _load ($path, $adapter=true)
    {
        foreach (DOMDocument::load($path)->getElementsByTagName('module') as $module) {
            array_push($this->_data, array(
                'name'    => $module->getAttribute('name'),
                'adapter' => $adapter
            ));
        }
    }

    public function exists ($name)
    {
        return in_array($name, $this->_data);
    }

    // Iterator implementation

    private $_position = 0;

    function rewind ()
    {
        $this->_position = 0;
    }

    function current ()
    {
        return $this->_data[$this->_position];
    }

    function key ()
    {
        return $this->_position;
    }

    function next ()
    {
        $this->_position++;
    }

    function valid ()
    {
        return isset($this->_data[$this->_position]);
    }
}

?>
