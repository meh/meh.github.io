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

abstract class Resource
{
    protected $_calls;
    protected $_data;

    public function __construct ()
    {
        $this->clear();
        $this->flush();
    }

    public function load ()
    {
        $args = func_get_args();

        miniLOL::instance()->events->fire(':resource.load', array(
            'resource'  => $this,
            'arguments' => $args
        ));

        try {
            call_user_func_array(array($this, '_load'), $args);

            array_push($this->_calls, $args);
        }
        catch (Exception $e) {
            throw $e;
        }

        miniLOL::instance()->events->fire(':resource.loaded', array(
            'resource'  => $this,
            'arguments' => $args
        ));

        return $this;
    }

    public function clear ()
    {
        miniLOL::instance()->events->fire(':resource.clear', array(
            'resource' => $this
        ));

        $this->_data = array();
    }

    public function flush ()
    {
        miniLOL::instance()->events->fire(':resource.flush', array(
            'resource' => $this
        ));

        $result       = $this->_calls;
        $this->_calls = array();

        return $result;
    }

    public function reload ()
    {
        miniLOL::instance()->events->fire(':resource.reload', array(
            'resource' => $this
        ));

        $this->clear();

        foreach ($this->flush() as $call) {
            call_user_func($this->load, $call);
        }

        miniLOL::instance()->events->fire(':resource.reloaded', array(
            'resource' => $this
        ));
    }

    abstract public function name ();
}

?>
