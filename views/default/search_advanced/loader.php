<?php

echo elgg_view("graphics/ajax_loader", array("hidden" => false));

$url = current_page_url();
$url = elgg_http_add_url_query_elements($url, array("loader" => 1));

?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".elgg-layout-one-column").load("<?php echo $url; ?>");
	});
</script>