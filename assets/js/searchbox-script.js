

jQuery(document).ready(function() {

	jQuery( '.search.blog-masonry #main > div, .blog.blog-masonry #main > div, .archive.blog-masonry #main > div' ).css({height: ''})

	var ajax_url = bsf_ajax_url.url + '?action=bsf_load_search_results&query=';

	jQuery('#bsf-live-search #bsf-sq').liveSearch({
		url: ajax_url
	});

});