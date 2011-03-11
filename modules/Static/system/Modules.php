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

require(STATIC_SYSTEM.'/Module.php');

class Modules
{
    private $_modules;

    public function __construct ()
    {
        $this->_modules = array();
    }

    public function load ($name, $adapter=true)
    {
        if ($adapter) {
            $path = STATIC_ADAPTERS."/modules/{$name}/main.php";
        }
        else {
            $path = STATIC_MODULES."/{$name}/main.php";
        }

        if (!file_exists($path)) {
            return;
        }

        require $path;

        $class  = str_replace(' ', '', $name) . 'Module';
        $module = $this->_modules[$name] = new $class;

        foreach ($module->aliases as $alias) {
            $this->_modules[$alias] = $module;
        }
    }

    public function &get ($name)
    {
        return $this->_modules[$name];
    }

    public function execute ($name, $args)
    {
        return $this->get($name)->execute($args);
    }
}

?>
