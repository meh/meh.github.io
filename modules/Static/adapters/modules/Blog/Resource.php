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

class BlogResource extends Resource
{
    public function name ()
    {
        return 'Blog';
    }

    public function _load ($path)
    {
        $dom = DOMDocument::load($path);

        foreach ($dom->getElementsByTagName('post') as $post) {
            $post = new Post($post);

            array_push($this->_data['byNumber'], $post);
            $this->_data['byId'][$post->id()] = $post;
        }
    }
    
    public function get ($what, $data)
    {
        switch ($what) {
            case 'number': return $this->_data['byNumber'][$data]; break;
            case 'id':     return $this->_data['byId'][$data]; break;
        }
    }

    public function clear ()
    {
        $this->_data = array(
            'byNumber' => array(),
            'byId'     => array()
        );
    }
}

?>
