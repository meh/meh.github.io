/*********************************************************************
 *           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE             *
 *                   Version 2, December 2004                        *
 *                                                                   *
 *  Copyleft meh.                                                    *
 *                                                                   *
 *           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE             *
 *  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION  *
 *                                                                   *
 *  0. You just DO WHAT THE FUCK YOU WANT TO.                        *
 *********************************************************************/

miniLOL.module.create("Paper", {
    version: "0.1",

    type: "active",

    aliases: ["paper"],

    initialize: function () {
        miniLOL.CSS.include(this.root+"/resources/style.css", true);
    },

    execute: function (what) {
        if (what["name"]) {
            miniLOL.content.set("<div id='paper'>" + miniLOL.module.execute('Markdown', miniLOL.utils.get(this.root+"/papers/#{name}".interpolate(what))) + "</div>")
            this.content(miniLOL.theme.content());
            this.menu(miniLOL.theme.content());

            var to = miniLOL.theme.content();

            if (what['chapter']) {
                to = to.xpath('//a[@name = #{name}]'.interpolate({ name: what['chapter'] })).first();
            }

            to.scrollTo.delay(0.1);
        }
    },

    content: function (element) {
        element.xpath('//h1[position()=1]').first().addClassName('title')
        element.xpath('//h2[position()=1]').first().addClassName('title')

        element.xpath('//h1[position()>2]').each(function (element) {
            element.parentNode.insertBefore(new Element('hr'), element);
        });
    },

    menu: function (element) {
        var block   = new Element('div', { id: 'paper-menu' });
        var chapter = [0, 0, 0, 0, 0, 0]
        var group;

        element.xpath('//h1[not(@class="title")] | //h2[not(@class="title")] | //h3 | //h4 | //h5 | //h6').each(function (element) {
            var anchor = new Element('a', { name: this.toChapter(chapter, element.nodeName[1].toNumber()) });
            anchor.appendChild(element);
            element.parentNode.replaceChild(anchor, element);

            if (element.nodeName == 'h1') {
                if (group) {
                    block.appendChild(group);
                }

                group = new Element('div', { 'class': 'main' })
            }

            var a = new Element('a', { 'class': element.nodeName });

            Event.observe(a, 'click', function () {
                element.xpath('//a[@name = #{name}]'.interpolate({ name: this.firstChild.firstChild.nodeValue })).first().scrollTo();
            });
        }, this);

        block.appendChild(group);

        $('paper').insert(block);
    },

    toChapter: function (array, level) {
        var result = '';

        for (var i = 0; i < level; i++) {
            result += '.' + array[i];
        }

        return result.substr(1);
    }
});
