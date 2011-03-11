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

class Filter
{
    private $_type;
    private $_regexp;
    private $_to;

    public function __construct ($dom, $censor)
    {
        $this->_type = $dom->getAttribute('type');

        if (($tmp = $dom->getAttribute('regexp'))) {
            $this->_regexp = $tmp;
        }
        else if (($tmp = $dom->getAttribute('raw'))) {
            $this->_regexp = "/{$tmp}/i";
        }

        $this->_regexp = preg_replace('#/([^/g]*)g([^/g]*)$#', '/$1$2', $this->_regexp);

        if ($this->_type == 'censor') {
            $this->_to = ($censor) ? $censor : '@#!%$';
        }
        else if ($this->_type == 'replace') {
            $this->_to = ($tmp = $dom->getAttribute('to')) ? $tmp : '$1';
        }
        else {
            $this->_to = '$1';
        }
    }

    public function apply ($text)
    {
        return preg_replace($this->_regexp, $this->_to, $text);
    }
}

?>
