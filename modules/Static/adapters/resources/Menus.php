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

class MenusResource extends Resource
{
    public function name ()
    {
        return 'miniLOL.menus';
    }

    private $_enabled;

    public function _load ($path)
    {
        $this->_enabled = true;

        try {
            foreach (DOMDocument::load($path)->documentElement->childNodes as $node) {
                if ($node->nodeName == 'menu') {
                    $this->_data[$node->getAttribute('id')] = $node;
                }
            }
        }
        catch (Exception $e) {
            $this->_enabled = false;
        }
    }

    public function enabled ()
    {
        return $this->_enabled;
    }

    public function get ($name)
    {
        return $this->_data[$name];
    }

    public function normalize ($callback)
    {
        foreach ($this->_data as $name => $menu) {
            $this->_data[$name] = call_user_func($callback, $menu);
        }
    }
}

?>
