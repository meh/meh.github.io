function trigger(what) {
	var el = $(
		`<div class="modal">
			<div class="header">
				TRIGGER WARNING
			</div>

			<div class="message">
				This post contains words that may trigger you, hurt your feelings,
				make you cry and kill all your cats (since that's probably all you have).
			</div>

			<div class="button trigger">TRIGGER ME</div>
			<div class="button safe">SPARE MY FEELINGS</div>
		</div>`
	).first();

	el.appendTo('body');

	el.find('.safe').on('click', function() {
		$('#content').find('*').contents()
			.filter(function() {
				return this.nodeType == Node.TEXT_NODE;
			})
			.each(function(i, t) {
				console.log(t);

				for (from in what) {
					t.textContent = t.textContent.replace(new RegExp("\\b" + from + "\\b", "g"), what[from]);
				}
			});

		el.remove();
	});

	el.find('.trigger').on('click', function() {
		el.remove();
	});
}
