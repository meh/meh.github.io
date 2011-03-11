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

class Post
{
    private $_id;
    private $_date;
    private $_title;
    private $_author;
    private $_content;

    public function __construct ($dom)
    {
        $this->id($dom->getAttribute('id'));
        $this->date($dom->getAttribute('date'));
        $this->title($dom->getAttribute('title'));
        $this->author($dom->getAttribute('author'));
        $this->content($dom->firstChild->nodeValue);
    }

    public function id ($value=null)
    {
        if ($value) {
            $this->_id = $value;
        }
        else {
            return $this->_id;
        }
    }

    public function date ($value=null)
    {
        if ($value) {
            $this->_date = $value;
        }
        else {
            return $this->_date;
        }
    }

    public function title ($value=null)
    {
        if ($value) {
            $this->_title = $value;
        }
        else {
            return $this->_title;
        }
    }

    public function author ($value=null)
    {
        if ($value) {
            $this->_author = $value;
        }
        else {
            return $this->_author;
        }
    }

    public function content ($value=null)
    {
        if ($value) {
            $this->_content = $value;
        }
        else {
            return $this->_content;
        }
    }
}

?>
