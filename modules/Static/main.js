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

miniLOL.module.create('Static', {
    version: '0.1',

    initialize: function () {
        Event.observe(document, ':go', this.execute);
    },

    execute: function () {
        $$('a').each(function (link) {
            var matches = link.getAttribute('href').match(/(.*)[?#](.*)$/);

            if (matches) {
                if (matches[1] && !matches[1].include(location.host)) {
                    return;
                }
            }
            else {
                return;
            }
            
            var href = matches[2];

            link.setAttribute('href', '?' + href);

            link.observe('click', function (event) { 
                event.stop();

                location.hash = href;
            });
        });
    }
});
