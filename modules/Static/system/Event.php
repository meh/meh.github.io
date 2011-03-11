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

class Event
{
    private $_name;
    private $_memo;

    public function __construct ($name, $memo)
    {
        $this->_name =  $name;
        $this->_memo = $memo;
    }

    public function name ()
    {
        return $this->_name;
    }

    public function &memo ()
    {
        return $this->_memo;
    }

    public function stop ()
    {
        $this->_stopped = true;
    }

    public function stopped ()
    {
        return $this->_stopped;
    }
}

?>
