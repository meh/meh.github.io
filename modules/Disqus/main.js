/*********************************************************************
*           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE              *
*                   Version 2, December 2004                         *
*                                                                    *
*  Copyleft meh.                                                     *
*                                                                    *
*           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE              *
*  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION   *
*                                                                    *
*  0. You just DO WHAT THE FUCK YOU WANT TO.                         *
*********************************************************************/

miniLOL.module.create("Disqus", {
  version: "0.1",

  initialize: function () {
    miniLOL.resource.get("miniLOL.config").load(this.root + "/resources/config.xml");

    $(document.body).insert(new Element('div', { id: 'disqus' }));
    $('disqus').insert(new Element('div', { id: 'disqus_thread' }));

    Event.fire(document, ':module.disqus.added', $('disqus'));

    $H(miniLOL.config["Disqus"]).each(function (pair) {
      window["disqus_" + pair.key] = pair.value;
    });

    function normalize (str) {
      var replacement = '_';

      return str.replace(/[#_\-=?&]/g, replacement).replace(new RegExp('^\\' + replacement), '');
    }

    function identifier () {
      return normalize(location.hash) || normalize(miniLOL.config["core"].homePage);
    }

    Event.observe(document, ":go", function () {
      if (!$('disqus-script')) {
        window.disqus_identifier = identifier();

        miniLOL.utils.include("http://" + window.disqus_shortname + ".disqus.com/embed.js", { id: 'disqus-script' });
      }
      else {
        DISQUS.reset({ reload: true, config: function () {
          window.disqus_identifier = this.page.identifier = identifier();
        }});
      }
    });
  }
});
