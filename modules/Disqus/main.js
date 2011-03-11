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

    Event.observe(document, ":go", function () {
      if ($('disqus-script')) {
        $('disqus-script').remove();
      }

      if ($('disqus')) {
        $('disqus').remove();
      }

      $(document.body).insert(new Element('div', { id: 'disqus' }));
      $('disqus').insert(new Element('div', { id: 'disqus_thread' }));

      Event.fire(document, ':module.disqus.added', $('disqus'));

      $H(miniLOL.config["Disqus"]).each(function (pair) {
        window["disqus_" + pair.key] = pair.value;
      });

      window.disqus_developer  = window.disqus_developer || 0;
      window.disqus_identifier = location.hash.replace(/^#/, '');
      window.disqus_url        = location.href;
      window.disqus_title      = document.title;

      miniLOL.utils.include("http://" + window.disqus_shortname + ".disqus.com/embed.js", { id: 'disqus-script' });
    });
  }
});
