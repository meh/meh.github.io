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

ob_start('ob_gzhandler');

define('ROOT', realpath('../..'));

$lastFor = 60 * 60 * 24 * 356; # a year, lol

header('Content-Type: text/javascript');
header('Pragma: public');
header("Cache-Control: max-age={$lastFor}");
header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lastFor) . ' GMT');

$scripts = unserialize(base64_decode(urldecode($_REQUEST['d'])));
$output  = '';

foreach ($scripts as $script) {
    if ($script[0] != '/') {
        $script = "../../{$script}";
    }

    $script = realpath($script);

    if (strpos($script, ROOT) === 0 && preg_match('/\.js$/i', $script)) {
        $output .= file_get_contents($script) . "\n";
    }
}

echo $output;

?>
