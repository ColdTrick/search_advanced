$(document).ready(function() {
	$(".search-advanced-settings-tabs li").click(function() {
		if (!$(this).hasClass("elgg-state-selected")) {
			$(".search-advanced-settings-tabs li").toggleClass("elgg-state-selected");
			$(".search-advanced-settings-profile-fields").toggleClass("hidden");
		}
	});
});