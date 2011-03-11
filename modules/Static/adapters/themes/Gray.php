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

function __fix_menu ($event)
{
    $memo = $event->memo();
    $menu = str_get_html(miniLOL::instance()->get('menu'));
    $set  = false;

    $normalize = miniLOL::instance()->get('url.normalize');

    foreach ($menu->find('a') as $link) {
        if (strstr($event->memo(), call_user_func($normalize, $link->href)) !== false) {
            $link->parent()->class = 'current';
            $set = true;
            break;
        }
    }

    if (!$set) {
        if (preg_match("#^http(s)?://.*?\Q{$_SERVER['HTTP_HOST']}\E(/.+)$#", $_SERVER['HTTP_REFERER'], $matches)) {
            $old = call_user_func($normalize, $matches[2]);

            foreach ($menu->find('a') as $link) {
                if (strstr($old, call_user_func($normalize, $link->href)) !== false) {
                    $link->parent()->class = 'current';
                    $set = true;
                    break;
                }
            }
        }

        if (!$set) {
            if ($link = $menu->find('a', 0)) {
                $link->parent()->class = 'current';
            }
        }
    }

    miniLOL::instance()->set('menu', $menu->save());
}

function __fix_title ($event)
{
    $config  = miniLOL::instance()->resources->get('miniLOL.config')->get('core');
    $content = str_get_html(miniLOL::instance()->theme->html());

    $content->find('div#siteTitle', 0)->innertext = $config['siteTitle'];

    miniLOL::instance()->theme->html($content->save());
}

function Theme_callback ()
{
    miniLOL::instance()->events->observe(':initialized', '__fix_title');
    miniLOL::instance()->events->observe(':go', '__fix_menu');
}

?>
