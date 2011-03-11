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

require(STATIC_ADAPTERS.'/modules/Markdown/markdown.php');

class MarkdownModule extends Module
{
    public function name ()
    {
        return 'Markdown';
    }

    public function __construct ()
    {
        miniLOL::instance()->resources->get('miniLOL.config')->load('modules/Markdown/resources/config.xml');
        miniLOL::instance()->resources->get('miniLOL.functions')->load('modules/Markdown/resources/functions.xml');
    }

    public function execute ($what)
    {
        if (!is_string($what) && $what['content']) {
            $what = $what['content'];
        }

        return Markdown($what);
    }
}
