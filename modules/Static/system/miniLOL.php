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

require(STATIC_SYSTEM.'/Events.php');
require(STATIC_SYSTEM.'/Resources.php');
require(STATIC_SYSTEM.'/Modules.php');
require(STATIC_SYSTEM.'/Theme.php');

class miniLOL
{
    public static $Version = '0.1';

    private static $_instance;

    public static function instance ()
    {
        if (self::$_instance) {
            return self::$_instance;
        }

        self::$_instance = new miniLOL;
        self::$_instance->__initialize();

        return self::$_instance;
    }

    public $events;
    public $resources;
    public $modules;

    public $theme;

    private $_data;

    private function __initialize () {
        $this->events = new Events;

        $this->resources = new Resources;
        $this->modules   = new Modules;

        $this->theme = new Theme;

        $this->resources->get('miniLOL.config')->load(STATIC_RESOURCES.'/config.xml');
        $this->resources->get('miniLOL.modules')->load(STATIC_RESOURCES.'/modules.xml', false);

        $this->events->observe(':output', array($this, '__fixLinks'));

        $this->set('url.normalize', array($this, '__urlNormalize'));
        $this->set('url.outputize', array($this, '__urlOutputize'));
    }

    public function __urlNormalize ($url)
    {
        $url = str_replace(WEB_ROOT.'/', '', $url);

        if ($url[0] == '#') {
            $url[0] = '?';
        }

        return $url;
    }

    public function __urlOutputize ($url)
    {
        if (isURL($url)) {
            return $url;
        }

        return call_user_func($this->get('url.normalize'), $url);
    }

    public function __fixLinks ($event)
    {
        $html = str_get_html($this->get('output'));

        foreach ($html->find('a') as $a) {
            $a->href = call_user_func($this->get('url.outputize'), $a->href);
        }

        $this->set('output', $html->save());
    }

    public function initialize () {
        $this->resources->get('miniLOL.config')->load('resources/config.xml');

        $config =& $this->resources->get('miniLOL.config')->get();

        $this->set('title', $config['core']['siteTitle']);

        $this->theme->load($config['core']['theme']);

        $this->resources->get('miniLOL.menus')->load('resources/menus.xml')->normalize(array($this->theme, 'menus'));
        $this->resources->get('miniLOL.pages')->load('resources/pages.xml')->normalize(array($this->theme, 'pages'));
        
        $this->resources->get('miniLOL.functions')->load('resources/functions.xml');
        
        foreach ($this->resources->get('miniLOL.modules')->load('resources/modules.xml') as $module) {
            $this->modules->load($module['name'], $module['adapter']);
        }
    }

    public function error ($what=null)
    {
        if ($what == null) {
            return $this->_error;
        }

        if (is_bool($what)) {
            if ($what) {
                $this->_error = 'Something went wrong.';
            }
            else {
                $this->_error = false;
            }
        }
        else {
            $this->_error = (string) $what;
        }
    }

    public function &get ($name)
    {
        return $this->_data[$name];
    }

    public function &set ($name, $value)
    {
        $this->_data[$name] = $value;

        return $this->_data[$name];
    }

    public function load ($page, $arguments)
    {
        $result = @file_get_contents("http://{$_SERVER['HTTP_HOST']}".WEB_ROOT."/data/{$page}?{$arguments}");

        if (!$result) {
            $result = @file_get_contents(ROOT."/data/{$page}");
        }

        if (!$result) {
            $result = null;
        }

        return $result;
    }

    public function go ($url, $arguments, $query=null, $again=false)
    {
        if (!$url) {
            throw new Exception('No url was passed.');
        }

        $url = call_user_func($this->get('url.normalize'), $url);

        $this->set('go.params.url', $url);
        $this->set('go.params.arguments', $arguments);
        $this->set('go.params.query', $query);
        $this->set('go.params.again', $again);

        $this->events->fire(':go.before');

        $url       = $this->get('go.params.url');
        $arguments = $this->get('go.params.arguments');
        $query     = $this->get('go.params.query');
        $again     = $this->get('go.params.again');

        if (isURL($url)) {
            header("Location: {$url}");
            return false;
        }

        if (!$query) {
            $query = $_SERVER['QUERY_STRING'];
        }

        if (preg_match('/\?(([^=&]+)&|([^=&]+)$)/', $url, $matches)) {
            $page = (!empty($matches[2])) ? $matches[2] : $matches[3];

            $alias = $this->resources->get('miniLOL.pages')->get($page)->attributes['alias'];
            $type  = $this->resources->get('miniLOL.pages')->get($page)->attributes['type'];
            $menu  = $this->resources->get('miniLOL.pages')->get($page)->attributes['menu'];

            if (($title = $this->resources->get('miniLOL.pages')->get($page)->attributes['title'])) {
                $this->set('title', $title);
            }

            if ($alias) {
                return $this->go($alias, $arguments, $query, $again);
            }
            else {
                $config =& $this->resources->get('miniLOL.config')->get('Static');
                $content = $this->resources->get('miniLOL.pages')->get($page)->content;

                $this->set('page', $this->resources->get('miniLOL.pages')->get($page));
            }
        }
        else if ($arguments['module']) {
            $content = $this->modules->get($arguments['module'])->execute($arguments);
        }
        else if ($arguments['page']) {
            $page    = $arguments['page']; unset($arguments['page']);
            $content = $this->load($page, $query);
        }
        else {
            if (!$again) {
                $config = $this->resources->get('miniLOL.config')->get('core');

                return $this->go($config['homePage'], $arguments, null, true);
            }
        }

        if ($content === null && !isset($arguments['module'])) {
            $content = '404 - Not Found';
        }
        else {
            if (isset($arguments['type'])) {
                $type = $arguments['type'];
            }

            if ($type) {
                $content = $this->resources->get('miniLOL.functions')->render($type, $content, $arguments);
            }
        }

        if ($arguments['menu']) {
            $menu = $arguments['menu'];
        }

        if (!$menu) {
            $menu = 'default';
        }

        $this->set('menu', $this->resources->get('miniLOL.menus')->get($menu));
        $this->set('content', $content);

        $this->events->fire(':initialized');
        $this->events->fire(':go', $url);

        return $this->theme->output($this->get('content'), $this->get('menu'));
    }
}

?>
