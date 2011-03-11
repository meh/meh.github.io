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

require(STATIC_SYSTEM.'/Event.php');

class Events
{
    private $_events;

    public function __construct ()
    {
        $this->_events = array();
    }

    public function observe ($name, $callback)
    {
        if (!is_array($this->_events[$name])) {
            $this->_events[$name] = array();
        }

        array_push($this->_events[$name], $callback);
    }

    public function stopObserving ($name, $callback=null)
    {
        if ($callback) {
            foreach ($this->_events[$name] as $key => $value) {
                if ($value == $callback) {
                    unset($this->_events[$name][$key]);
                    break;
                }
            }
        }
        else {
            unset($this->_events[$name]);
        }
    }

    public function fire ($name, $memo=null)
    {
        if (is_array($this->_events[$name])) {
            $event = new Event($name, $memo);

            foreach ($this->_events[$name] as $callback) {
                call_user_func($callback, $event);

                if ($event->stopped()) {
                    break;
                }
            }
        }
    }
}

?>
