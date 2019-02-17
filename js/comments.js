function comments(issue) {
	var url = "https://api.github.com/repos/meh/meh.github.io/issues/" + issue + "/comments";
	var comments = $(tmpl("template-comments", { issue: issue }).trim());
	var container = comments.find('.container');

	return axios.get(url, {
		headers: {
			"Accept": "application/vnd.github.3.full+json"
		}
	}).then(function(response) {
		response.data.forEach(function(comment) {
			$(tmpl("template-comment", comment).trim()).appendTo(container);
		});

		return comments;
	}).catch(function() {
		console.warn("Who needs comments anyway?");
	});
}
