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

foreach (glob(STATIC_ADAPTERS.'/functions/*.php') as $function) {
    require($function);
}

class FunctionsResource extends Resource
{
    public function name ()
    {
        return 'miniLOL.functions';
    }

    public function __construct ()
    {
        parent::__construct();
    }

    public function _load ($path)
    {
        $dom   = DOMDocument::load($path);
        $xpath = new DOMXpath($dom);
    
        foreach ($xpath->query('/functions/function') as $function) {
            $name = $function->getAttribute('name');

            if (function_exists("Function_$name")) {
                $this->_data[$name] = "Function_$name";
            }
        }
    }

    public function get ($name)
    {
        return $this->_data[$name];
    }

    public function render ($types, $content, $arguments)
    {
        foreach (preg_split('/\s*,\s*/', $types) as $type) {
            if (($callback = $this->get($type))) {
                $content = $callback($content, $arguments);
            }
        }

        return $content;
    }
}

?>
