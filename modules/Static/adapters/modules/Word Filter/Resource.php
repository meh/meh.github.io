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

require(STATIC_ADAPTERS.'/modules/Word Filter/Filter.php');

class WordFilterResource extends Resource
{
    public function name ()
    {
        return 'Word Filter';
    }

    public function _load ($path)
    {
        $dom = DOMDocument::load($path);

        $this->_data['censor'] = $dom->documentElement->getAttribute('censor');

        foreach ($dom->getElementsByTagName('filter') as $filter) {
            array_push($this->_data['filters'], new Filter($filter, $this->_data['censor']));
        }
    }

    public function clear ()
    {
        $this->_data = array(
            'filters' => array(),
            'censor'  => '@#!%$'
        );
    }

    public function filters ()
    {
        return $this->_data['filters'];
    }

    public function censor ()
    {
        return $this->_data['censor'];
    }
}

?>
