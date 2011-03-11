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

class URLRewriteModule extends Module
{
    public function name ()
    {
        return 'URL Rewrite';
    }

    public static function htaccess ()
    {
        $_WEB_ROOT = WEB_ROOT;

        return <<<APACHE

RewriteEngine On
RewriteBase /

RewriteRule ^([^&/]+)$ {$_WEB_ROOT}/?$1 [L,QSA,NS]

RewriteRule ^pages/((themes|resources|system|modules)/.+)$ {$_WEB_ROOT}/$1 [L,QSA,NS]
RewriteRule ^pages/(.*)$ {$_WEB_ROOT}/?page=$1 [L,QSA,NS]

RewriteRule ^module/((themes|resources|system|modules)/.+)$ {$_WEB_ROOT}/$1 [L,QSA,NS]
RewriteRule ^module/(.*)$ {$_WEB_ROOT}/?module=$1 [L,QSA,NS]

APACHE;
    }

    public function __construct ()
    {
        miniLOL::instance()->events->observe(':output', array($this, '__fixScript'));

        if (!file_exists(ROOT.'/.htaccess')) {
            file_put_contents(ROOT.'/.htaccess', URLRewriteModule::htaccess());
        }

        $this->__urlNormalize__ = miniLOL::instance()->get('url.normalize');

        miniLOL::instance()->set('url.normalize', array($this, '__urlNormalize'));
        miniLOL::instance()->set('url.outputize', array($this, '__urlOutputize'));
    }

    public function __urlNormalize ($url)
    {
        $url = call_user_func($this->__urlNormalize__, $url);

        if (preg_match('#pages/(.+)$#', $url, $matches)) {
            $url = '?page=' . str_replace('?', '&', $matches[1]);
        }
        else {
            if ($url[0] != '?') {
                $url = "?{$url}";
            }
        }

        return $url;
    }

    public function __urlOutputize ($url)
    {
        $url = call_user_func(miniLOL::instance()->get('url.normalize'), $url);

        $url = preg_replace('#^\?page=(.*?)(&(.*))?$#', 'pages/$1?$3', $url);
        $url = preg_replace('#^\?module=(.*?)(&(.*))?$#', 'modules/$1?$3', $url);
        $url = preg_replace('#^\?([^&/]+)(&(.*))?$#', '$1?$3', $url);
        $url = preg_replace('#\?$#', '', $url);

        return $url;
    }


    public function __fixScript ($event)
    {
        $html = str_get_html(miniLOL::instance()->get('output'));

        if (!($script = $html->find('#__miniLOL_Static_fixUrl', 0))) {
            return;
        }

        $_WEB_ROOT    = WEB_ROOT;
        $_WEB_ROOT_RE = preg_replace('#[\./]#', '\$1', $_WEB_ROOT);
        
        $script->innertext = <<<JAVASCRIPT
// <![CDATA[

        (miniLOL.utils.fixURL = function () {
            var matches = location.href.match(/(.*?{$_WEB_ROOT_RE}\/)(.+?)(\?(.+))$/);

            if (!matches) {
                return false;
            }

            var first = matches[1];
            var path  = matches[2];
            var query = (matches[4]) ? '&' + matches[3] : '';

            if (matches = path.match(/^pages\/(.*)$/)) {
//                location.href = location.href.
            }

            if (matches) {
                location.href = location.href.replace(/\?(.*)$/, '#' + matches[1]);

                return true;
            }
        })();

        Event.observe(document, ':module.create', function (event) {
            if (event.memo.name != 'Static') {
                return;
            }

            event.memo.execute = (function () {

            }).bind(event.memo);
        });

// ]]>
JAVASCRIPT;

        miniLOL::instance()->set('output', $html->save());
    }
}

?>
