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

class Page {
    public $name;
    public $attributes;
    public $meta;
    public $content;

    function __construct ($dom) {
        $this->name       = $dom->getAttribute('id');
        $this->attributes = ObjectFromAttributes($dom->attributes);
        $this->meta       = XMLToArray($dom->getElementsByTagName('meta')->item(0));
        $this->content    = $dom;

        foreach ($this->meta as $name => $content) {
            $this->meta[$name] = trim($content);
        }
    }

    function normalize ($callback) {
        $this->content = call_user_func($callback, $this->content);
    }
}

class PagesResource extends Resource
{
    public function name ()
    {
        return 'miniLOL.pages';
    }

    public function _load ($path)
    {
        foreach (DOMDocument::load($path)->getElementsByTagName('page') as $page) {
            $page = new Page($page);
            $this->_data[$page->name] = $page;
        }
    }

    public function get ($page)
    {
        return $this->_data[$page];
    }

    public function normalize ($callback)
    {
        foreach ($this->_data as $page) {
            $page->normalize($callback);
        }
    }
}

?>
