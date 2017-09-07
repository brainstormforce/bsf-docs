

jQuery(document).ready(function() {

	var ajax_url = bsf_ajax_url.url + '?action=bsf_load_search_results&query=';

	jQuery('#bsf-live-search #bsf-sq').liveSearch({
		url: ajax_url
	});

});