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

require(STATIC_ADAPTERS.'/modules/Blog/Blog.php');

class BlogModule extends Module
{
    private $_resource;

    public function name ()
    {
        return 'Blog';
    }

    public function __construct ()
    {
        $this->_blog = new Blog(MODULES.'/Blog/resources/data.xml');
    }

    public function get ($what, $data)
    {
        return $this->_blog->get($what, $data);
    }

    public function page ($number, $posts=null)
    {
        return $this->_blog->get($number, $posts);
    }

    public function execute ($args)
    {
        if ($args['id']) {
            return $this->_blog->output('post', $args);
        }
        else if ($args['number']) {
            return $this->_blog->output('post', $args);
        }
        else if ($args['page']) {
            return $this->_blog->output('page', $args);
        }
        else {
            return $this->_blog->output('page', array('page' => 1));
        }
    }
}

?>
