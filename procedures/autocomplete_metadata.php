<?php

$q = sanitise_string(get_input("q"));
$autocomplete_field = sanitise_string(get_input("autocomplete_field"));
$limit = max((int) get_input("limit", 10), 1);

$result = array();
if (!empty($q) && !empty($autocomplete_field)) {
	
	$name_id = add_metastring($autocomplete_field);
	$dbprefix = elgg_get_config('dbprefix');
	$query = "SELECT msv.string, count(*) as count
	FROM {$dbprefix}metadata md
	JOIN {$dbprefix}metastrings msv ON md.value_id = msv.id
	JOIN {$dbprefix}entities e ON md.entity_guid = e.guid
	WHERE md.name_id = {$name_id}
	AND e.type = 'user'
	AND msv.string LIKE '%{$q}%'
	GROUP BY msv.string
	ORDER BY count DESC
	LIMIT 0, {$limit}";
	
	$metadata = get_data($query);
	
	if ($metadata) {
		foreach ($metadata as $md) {
			$result[] = array(
				'value' => $md->string
			);
		}
	}
}

header("Content-Type: application/json");
echo json_encode(array_values($result));

exit();
