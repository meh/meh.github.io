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

ob_start();

define('MINILOL_VERSION', '1.2');
define('__VERSION__', '0.1');

define('ROOT',     preg_replace('#^/$#', '', realpath(dirname(__FILE__))));
define('WEB_ROOT', preg_replace('#^/$#', '', dirname($_SERVER['SCRIPT_NAME'])));
define('MODULES',  ROOT.'/modules');

define('STATIC_ROOT',      MODULES.'/Static');
define('STATIC_RESOURCES', STATIC_ROOT.'/resources');
define('STATIC_SYSTEM',    STATIC_ROOT.'/system');
define('STATIC_ADAPTERS',  STATIC_ROOT.'/adapters');
define('STATIC_MODULES',   STATIC_ROOT.'/modules');

require(STATIC_SYSTEM.'/utils.php');
require(STATIC_SYSTEM.'/miniLOL.php');

session_start();

$miniLOL = miniLOL::instance();
$miniLOL->initialize();

$config =& $miniLOL->resources->get('miniLOL.config')->get();

if (ob_get_length() > 0) {
    $miniLOL->error(ob_get_contents());
}

ob_end_clean();
ob_start('ob_gzhandler');

if ($miniLOL->error()) {
    echo $miniLOL->error();
    exit;
}

$output['content'] = $miniLOL->go($_SERVER['REQUEST_URI'], $_REQUEST);

if ($content === false) {
    exit;
}

$output['title'] = interpolate($miniLOL->get('title'), $config['core']);

$output['meta'] = '';

foreach (array_merge((array) $config['Static']['meta'], (array) ($miniLOL->get('page') ? $miniLOL->get('page')->meta : null)) as $name => $content) {
    $output['meta'] .= "<meta name='{$name}' content='{$content}' />\n";
}

$output['favicon'] = ($config['Static']['favicon'])
    ? $output['favicon'] = "<link rel='icon' type='image/png' href='{$config['Static']['favicon']}' />"
    : '';

$output['styles'] = '';
foreach ($miniLOL->theme->styles(true) as $style) {
    $output['styles'] .= "<link rel='stylesheet' type='text/css' href='{$style}' />\n";
}

$output['javascript'] = array();

if ($config['Static']['alwaysOn'] != 'true') {
    $scripts = unifiedScriptsURL(array(
        'system/prototype.min.js', 'system/scriptaculous.min.js', # Uncomment if you need scriptaculous

        'system/miniLOL.min.js'
    ));

    $output['javascript']['dependencies'] = <<<HTML

    <script type="text/javascript" src="{$scripts}"></script>

    <script type="text/javascript">// <![CDATA[

    miniLOL.CSS.create("body { display: none !important; }", "__miniLOL.Static.hide");

    Event.observe(document, ":initialization", function () {
        $("__miniLOL.Static.hide").remove();
    });

    // ]]></script>

    <script id="__miniLOL_Static_fixUrl" type="text/javascript">// <![CDATA[
        (miniLOL.utils.fixURL = function () {
            var matches = location.href.match(/\?(.*)$/);

            if (matches) {
                location.href = location.href.replace(/\?(.*)$/, "#" + matches[1]);

                return true;
            }
        })();
    // ]]></script>

HTML;

    $output['javascript']['initialization'] = 'if (miniLOL.utils.fixURL()) return false; miniLOL.initialize()';
}

$output['whole'] = <<<HTML

<!DOCTYPE html>
<html>
<head>
    <title>{$output['title']}</title>

    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

    {$output['meta']}

    {$output['favicon']}

    {$output['styles']}

    {$output['javascript']['dependencies']}
</head>

<body onload="{$output['javascript']['initialization']}">
    {$output['content']}
</body>
</html>

HTML;

$miniLOL->set('output', $output['whole']);
$miniLOL->events->fire(':output');
echo $miniLOL->get('output');

?>
