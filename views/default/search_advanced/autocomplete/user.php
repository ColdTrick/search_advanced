<?php
$entity = $vars["entity"];

echo elgg_view('output/img', [
	'src' => $entity->getIconURL("tiny")
]);

echo $entity->name;
