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

class Theme
{
    private $_name;
    private $_path;
    private $_info;
    private $_styles;
    private $_other_styles;
    private $_template;
    private $_html;

    public function load ($name)
    {
        if ($this->_name) {
            throw new Exception('Already loaded a theme.');
        }

        $path = ROOT."/themes/{$name}";

        if (!file_exists("{$path}/theme.xml")) {
            throw new Exception('Theme not found.');
        }

        $this->_name         = $name;
        $this->_path         = realpath($path);
        $this->_info         = array();
        $this->_styles       = array();
        $this->_other_styles = array();

        $dom   = DOMDocument::load($this->path().'/theme.xml');
        $xpath = new DOMXpath($dom);

        foreach ($dom->documentElement->attributes as $attribute) {
            $this->_info[$attribute->name] = $attribute->value;
        }

        foreach ($xpath->query('/theme/styles/style') as $style) {
            array_push($this->_styles, $style->getAttribute('name'));
        }

        $this->_html = preg_replace('/(href=[\'"])#/', '$1?', file_get_contents("{$this->_path}/template.html"));

        $this->_templates = array(
            'list' => array(
                'default' => array(
                    'global' => '<div #{attributes}>#{data}</div>',

                    'before' => '#{data}',
                    'after'  => '#{data}',

                    'link' => '<div class="#{class}" id="#{id}">#{before}<a href="#{href}" target="#{target}" #{attributes}>#{text}</a>#{after}</div>',
                    'item' => '<div class="#{class}" id="#{id}">#{before}<span #{attributes}>#{text}</span>#{after}</div>',
                    'nest' => '<div class="#{class}" style="#{style}">#{data}</div>',
                    'data' => '<div class="data">#{before}#{data}#{after}</div>'
                ),

                'table' => array(
                    'global' => '<table #{attributes}>#{data}</table>',

                    'before' => '#{data}',
                    'after'  => '#{data}',

                    'link' => '<tr><td>#{before}</td><td><a href="#{href}" target="#{target}" #{attributes}>#{text}</a></td><td>#{after}</td></tr>',
                    'item' => '<tr><td>#{before}</td><td>#{text}</td><td>#{after}</td></tr>',
                    'nest' => '<div class="#{class}" style="#{style}">#{data}</div>',
                    'data' => '<div class="data">#{before}#{data}#{after}</div>'
                )
            )
        );

        // Reading and parsing additional list templates
        foreach ($xpath->query('/theme/templates/*') as $template) {
            $this->_templates[$template->nodeName] =& XMLToArray($template);
        }

        if (file_exists(STATIC_ADAPTERS."/themes/{$name}.php")) {
            require(STATIC_ADAPTERS."/themes/{$name}.php");

            if (function_exists('Theme_callback')) {
                Theme_callback();
            }
        }
    }

    public function name ()
    {
        return $this->_name;
    }

    public function path ($relative=false)
    {
        return ($relative) ? "themes/{$this->name()}" : $this->_path;
    }

    public function info ()
    {
        return $this->_info;
    }

    public function addStyle ($path)
    {
        array_push($this->_other_styles, $path);
    }

    public function hasStyle ($name)
    {
        !!$this->_styles[$name];
    }

    public function styles ($output)
    {
        if ($output) {
            $result = array();

            foreach (array_merge($this->_styles, $this->_other_styles) as $style) {
                if (in_array($style, $this->_styles)) {
                    $path = "{$this->path(true)}/{$style}";
                }
                else {
                    $path = $style;
                }

                if (file_exists("{$path}.min.css")) {
                    $path .= '.min.css';
                }
                else {
                    $path .= '.css';
                }

                array_push($result, $path);
            }

            return $result;
        }
        else {
            return array_merge($this->_styles, $this->_other_styles);
        }
    }

    public function html ($value=null)
    {
        if (is_null($value)) {
            return $this->_html;
        }
        else {
            $this->_html = $value;
        }
    }

    public function template ($what, $name)
    {
        return $this->_templates[$what][$name];
    }

    public function menus ($menu, $layer=0)
    {
        $template = $this->_templates['menu'];

        if (!$template || !$menu) {
            return false;
        }

        $first  = true;
        $output = '';

        foreach ($menu->childNodes as $node) {
            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:
                if ($node->nodeName == 'menu') {
                    $tmp = $this->_menus_layer($template, $layer);

                    $output .= interpolate($tmp['menu'], array(
                        'data' => $this->menus($node, $layer)
                    ));
                }
                else if ($node->nodeName == 'item') {
                    $output .= $this->_menus_item($node, $template, $layer);
                }
                else {
                    $output .= $this->_menus_other($node, $template);
                }
                break;

                case XML_CDATA_SECTION_NODE:
                case XML_TEXT_NODE:
                if (!$first) {
                    $output .= $node->nodeValue;
                }

                $first = false;
                break;
            }
        }

        if (!preg_match('/^[\s\n]*$/D', $output)) {
            if ($layer == 0) {
                $tmp = $this->_menus_layer($template, $layer);

                return interpolate($tmp['menu'], array(
                    'data' => $output
                ));
            }
            else {
                return $output;
            }
        }
        else {
            return '';
        }
    }

    public function _menus_layer ($template, $layer)
    {
        $result = array(
            'menu' => '',
            'item' => ''
        );

        if ($template) {
            // Object.extend(result, template.layers["_" + layer] || template.layers["default"] || {});
            $result = array_merge($result, array_shift(array_filter(array(
                $template['layers']["_{$layer}"],
                $template['layers']['default'],
                array()
            ), 'is_array')));

            if (!$result['menu']) {
                $result['menu'] = '#{data}';
            }
            
            if (!$result['item']) {
                $result['item'] = '<a href="#{href}" #{attributes}>#{text}</a> ';
            }
        }

        return $result;
    }

    public function _menus_item ($element, $template, $layer)
    {
        $item = $element->cloneNode(true);

        if (!($itemClass = $item->getAttribute('class'))) {
            $itemClass = '';
        } $item->removeAttribute('class');

        if (!($itemId = $item->getAttribute('id'))) {
            $itemId = '';
        } $item->removeAttribute('id');

        if (!($itemHref = $item->getAttribute('href'))) {
            $itemHref = $item->getAttribute('href');
        } $item->removeAttribute('href');

        $tmp = $this->_menus_layer($template, $layer);

        return interpolate($tmp['item'], array_merge(ObjectFromAttributes($item->attributes), array(
            'class'      => $itemClass,
            'id'         => $itemId,
            'href'       => $itemHref,
            'attributes' => StringFromAttributes($item->attributes),
            'text'       => GetFirstText($element->childNodes),
            'data'       => $this->menus($element, $layer + 1)
        )));
    }

    public function _menus_other ($data, $template)
    {
        if (!$data || !$template) {
            return '';
        }

        $text = $template[$data->nodeName];

        if (!$text) {
            return '';
        }

        $output  = '';
        $outputs = array();

        foreach ($data->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $outputs[$node->nodeName] = $this->_menus_other($node, $template);
            }
        }

        return interpolate($text, array_merge($outputs, ObjectFromAttributes($data->attributes)));
    }

    public function pages ($page, $data=null)
    {
        $output = '';
        
        foreach ($page->childNodes as $node) {
            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:
                try { $output .= @call_user_func(array($this, "_pages_{$node->nodeName}"), $node, $data); } catch (Exception $e) { }
                break;

                case XML_CDATA_SECTION_NODE:
                case XML_TEXT_NODE:
                $output .= $node->nodeValue;
                break;
            }
        }

        return $output;
    }

    private function _pages_list ($element, $data)
    {
        $list = $element->cloneNode(false);
        $data = (is_array($data)) ? $data : array($element);

        if (!($listBefore = $list->getAttribute('before'))) {
            if (!($listBefore = $data[0]->getAttribute('before'))) {
                $listBefore = '';
            }
        } $list->removeAttribute('before');

        if (!($listAfter = $list->getAttribute('after'))) {
            if (!($listAfter = $data[0]->getAttribute('after'))) {
                $listAfter = '';
            }
        } $list->removeAttribute('after');

        if (!($listArgs = $list->getAttribute('arguments'))) {
            if (!($listArgs = $data[0]->getAttribute('arguments'))) {
                $listArgs = '';
            }
        } $list->removeAttribute('arguments');

        if (!($listType = $list->getAttribute('type'))) {
            if (!($listType = $data[0]->getAttribute('type'))) {
                $listType = '';
            }
        } $list->removeAttribute('type');

        if (!($listMenu = $list->getAttribute('menu'))) {
            if (!($listMenu = $data[0]->getAttribute('menu'))) {
                $listMenu = '';
            }
        } $list->removeAttribute('menu');

        if (!($listTemplate = $list->getAttribute('template'))) {
            if (!($listTemplate = $data[0]->getAttribute('template'))) {
                $listTemplate = '';
            }
        } $list->removeAttribute('template');

        if (!$this->template('list', $listTemplate)) {
            $listTemplate = 'default';
        }

        $output = '';

        foreach ($element->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                if ($node->nodeName == 'link') {
                    $link = $node->cloneNode(true);

                    if (!($href = $link->getAttribute('href'))) {
                        $href = '';
                    } $link->removeAttribute('href');

                    if (!($target = $link->getAttribute('target'))) {
                        $target = '';
                    } $link->removeAttribute('target');

                    if (!($text = $link->firstChild->nodeValue)) {
                        $text = $href;
                    }

                    if (!($before = $link->getAttribute('before'))) {
                        if (!($before = $listBefore)) {
                            $before = '';
                        }
                    } $link->removeAttribute('before');

                    if (!($after = $link->getAttribute('after'))) {
                        if (!($after = $listAfter)) {
                            $after = '';
                        }
                    } $link->removeAttribute('after');

                    if (!($domain = $link->getAttribute('domain'))) {
                        $domain = '';
                    } $link->removeAttribute('domain');

                    if (!($args = $link->getAttribute('arguments'))) {
                        $args = '';
                    } $link->removeAttribute('arguments');

                    if (!($menu = $link->getAttribute('menu'))) {
                        $menu = '';
                    } $link->removeAttribute('menu');

                    if (!($title = $link->getAttribute('title'))) {
                        $title = '';
                    } $link->removeAttribute('title');

                    $out = isURL($href);

                    if (!($linkClass = $link->getAttribute('class'))) {
                        $linkClass = '';
                    } $link->removeAttribute('class');

                    if (!($linkId = $link->getAttribute('id'))) {
                        $linkId = '';
                    } $link->removeAttribute('id');

                    if ($target || $out) {
                        $href = (!$out) ? "data/{$href}" : $href;

                        if (!$target) {
                            $target = '_blank';
                        }
                    }
                    else {
                        if (!($ltype = $link->getAttribute('type'))) {
                            if (!($ltype = $listType)) {
                                $ltype = '';
                            }
                        } $link->removeAttribute('type');

                        if ($domain == 'in' || $href[0] == '#') {
                            if ($href[0] != '#') {
                                $href = "#{$href}";
                            }
                        }
                        else {
                            $href = "#page={$href}";
                        }

                        $href[0] = '?';

                        if (!empty($args)) {
                            $args = preg_replace('/[ ,]+/', '&amp;', $args);
                        }

                        if ($ltype) {
                            $ltype = "&type={$ltype}";
                        }

                        if (miniLOL::instance()->resources->get('miniLOL.menus')->enabled() && $menu) {
                            $menu = "&amp;menu={$menu}";
                        }

                        $target = '';

                        if ($title) {
                            $title = interpolate($title, array(
                                'text' => $text,
                                'href' => $href
                            ));

                            $title = "&title={urlencode($title)}";
                        }

                        $href = "{$href}{$args}{$ltype}{$menu}{$title}";
                    }

                    $output .= interpolate($this->_templates['list'][$listTemplate]['link'], array_merge(ObjectFromAttributes($link->attributes), array(
                        'class'      => $linkClass,
                        'id'         => $linkId,
                        'attributes' => StringFromAttributes($link->attributes),
                        'before'     => interpolate($this->_templates['list'][$listTemplate]['before'], array('data' => $before)),
                        'after'      => interpolate($this->_templates['list'][$listTemplate]['after'], array('data' => $after)),
                        'href'       => $href,
                        'target'     => $target,
                        'text'       => $text,
                        'title'      => $title
                    )));
                }
                else if ($node->nodeName == 'item') {
                    $item = $node->cloneNode(true);

                    if (!($text = $item->firstChild->nodeValue)) {
                        $text = '';
                    }

                    if (!($before = $item->getAttribute('before'))) {
                        if (!($before = $listBefore)) {
                            $before = '';
                        }
                    } $item->removeAttribute('before');

                    if (!($after = $item->getAttribute('after'))) {
                        if (!($after = $listAfter)) {
                            $after = '';
                        }
                    } $item->removeAttribute('after');

                    if (!($itemClass = $item->getAttribute('class'))) {
                        $itemClass = '';
                    } $item->removeAttribute('class');

                    if (!($itemId = $item->getAttribute('id'))) {
                        $itemId = '';
                    } $item->removeAttribute('id');

                    $output .= interpolate($this->_templates['list'][$listTemplate]['item'], array_merge(ObjectFromAttributes($item->attributes), array(
                        'class'      => $itemClass,
                        'id'         => $itemId,
                        'attributes' => StringFromAttributes($item->attributes),
                        'before'     => interpolate($this->_templates['list'][$listTemplate]['before'], array('data' => $before)),
                        'after'      => interpolate($this->_templates['list'][$listTemplate]['after'], array('data' => $after)),
                        'text'       => $text
                    )));
                }
                else if ($node->nodeName == 'list') {
                    $output .= $this->_pages_list($node, array($element));
                }
                else if ($node->nodeName == 'nest') {
                    $toParse = $node->cloneNode(true);

                    if (!($before = $node->getAttribute('before'))) {
                        if (!($before = $listBefore)) {
                            $before = '';
                        }
                    }

                    if (!($after = $node->getAttribute('after'))) {
                        if (!($after = $listAfter)) {
                            $after = '';
                        }
                    }

                    $output .= interpolate($this->_templates['list'][$listTemplate]['nest'], array(
                        'class'  => $node->getAttribute('class'),
                        'style'  => $node->getAttribute('style'),
                        'before' => interpolate($this->_templates['list'][$listTemplate]['before'], array('data' => $before)),
                        'after'  => interpolate($this->_templates['list'][$listTemplate]['after'], array('data' => $after)),
                        'data'   => $this->pages($toParse, array($element))
                    ));
                }
            }
            else if ($node->nodeType == XML_CDATA_SECTION_NODE || $node->nodeType == XML_TEXT_NODE) {
                if (!preg_replace('/[\s\n]+/', '', $node->nodeValue)) {
                    continue;
                }

                $output .= interpolate($this->_templates['list'][$listTemplate]['data'], array(
                    'data' => $node->nodeValue
                ));
            }
        }

        return interpolate($this->_templates['list'][$listTemplate]['global'], array_merge(ObjectFromAttributes($list->attributes), array(
            'attributes' => StringFromAttributes($list->attributes),
            'data'       => $output
        )));
    }

    private function _pages_include ($element, $data)
    {
        $output = '';

        return $output;
    }

    public function output ($content, $menu)
    {
        $html = str_get_html($this->html());
        $html->find("#{$this->_info['content']}", 0)->innertext = $content;
        $html->find("#{$this->_info['menu']}", 0)->innertext    = $menu;

        return $html->save();
    }
}

?>
