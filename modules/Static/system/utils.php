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

require(STATIC_SYSTEM.'/simple_html_dom.php');

function interpolate ($string, $object)
{
    foreach ($object as $name => $value) {
        $string = preg_replace("|#{{$name}}|", (string) $value, $string);
    }

    return $string;
}

function array_insert(&$input, $offset, $replacement)
{
    array_splice($input, $offset, 0, 0);
    $input[$offset] = $replacement;
}

function &XMLToArray ($xml)
{
    $result = array();

    if (!is_object($xml)) {
        return $result;
    }

    $class = get_class($xml);

    if (preg_match('/^SimpleXML/', $class)) {
        if (count($xml->children()) == 0) {
            return (string) $xml;
        }

        foreach ($xml as $name => $value) {
            $result[$name] =& XMLToArray($value);
        }
    }
    else if (preg_match('/^DOM/', $class)) {
        if ($class == 'DOMDocument') {
            $xml = $xml->documentElement;
        }

        foreach ($xml->childNodes as $node) {
            if ($node->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            if ($node->getElementsByTagName('*')->length == 0) {
                $content = '';

                foreach ($node->childNodes as $text) {
                    if ($text->nodeType != XML_CDATA_SECTION_NODE && $text->nodeType != XML_TEXT_NODE) {
                        continue;
                    }

                    if (preg_match('/^[\s\n]*$/D', $text->nodeValue)) {
                        continue;
                    }

                    $content .= $text->nodeValue;
                }

                $result[$node->nodeName] = $content;
            }
            else {
                $result[$node->nodeName] =& XMLToArray($node);
            }
        }
    }

    return $result;
}

function isURL ($text)
{
    if (preg_match('/^mailto:([\w.%+-]+@[\w.]+\.[A-Za-z]{2,4})$/', $text, $match)) {
        return array(
            'protocol' => 'mailto',
            'uri'      => $match[1]
        );
    }

    if (preg_match('/^(\w+):(\/\/.+?(:\d)?)(\/)?/', $text, $match)) {
        return array(
            'protocol' => $match[1],
            'uri'      => $match[2]
        );
    }
    
    return false;
}

function ObjectFromAttributes ($data)
{
    $attributes = array();

    foreach ($data as $attribute) {
        $attributes[$attribute->name] = $attribute->value;
    }

    return $attributes;
}

function StringFromAttributes ($data)
{
    $attributes = '';

    foreach ($data as $attribute) {
        $attributes .= urlencode($attribute->name) . '="' . $attribute->value . '" ';
    }

    return $attributes;
}

function GetFirstText ($elements)
{
    $result = '';

    foreach ($elements as $element) {
        if ($element->nodeType != XML_CDATA_SECTION_NODE && $element->nodeType != XML_TEXT_NODE) {
            break;
        }

        if (!preg_match('/^[\s\n]*$/', $element->nodeValue)) {
            $result = trim($element->nodeValue);
            break;
        }
    }

    return $result;
}

function unifiedScriptsURL ($scripts)
{
    $mtime = 0;

    foreach ($scripts as $script) {
        if (($tmp = @filemtime($script)) > $mtime) {
            $mtime = $tmp;
        }
    }

    $scripts = urlencode(base64_encode(serialize($scripts)));

    return WEB_ROOT."/modules/Static/scripts.php?d={$scripts}&{$mtime}";
}

?>
