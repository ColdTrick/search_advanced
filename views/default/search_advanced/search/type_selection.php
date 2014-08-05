<?php
$types = get_registered_entity_types();

$current_selected = elgg_echo("all");

$entity_type = get_input("entity_type");
$entity_subtype = get_input("entity_subtype");

if (!array_key_exists($entity_type, $types)) {
	$entity_type = "";
}

if (array_key_exists($entity_type, $types) && !in_array($entity_subtype, $types[$entity_type])) {
	$entity_subtype = "";
}

if (!empty($entity_type)) {
	echo elgg_view("input/hidden", array("name" => "search_type", "value" => "entities"));
	echo elgg_view("input/hidden", array("name" => "entity_type", "value" => $entity_type));
	
	$current_selected = elgg_echo("item:" . $entity_type);
	
	if (!empty($entity_subtype)) {
		$current_selected = elgg_echo("item:" . $entity_type . ":" . $entity_subtype);
		
		echo elgg_view("input/hidden", array("name" => "entity_subtype", "value" => $entity_subtype));
	} else {
		echo elgg_view("input/hidden", array("name" => "entity_subtype", "disabled" => "disabled"));
	}
} else {
	echo elgg_view("input/hidden", array("name" => "search_type", "value" => "entities","disabled" => "disabled"));
	echo elgg_view("input/hidden", array("name" => "entity_type", "disabled" => "disabled"));
	echo elgg_view("input/hidden", array("name" => "entity_subtype", "disabled" => "disabled"));
}

?>
<ul class="search-advanced-type-selection">
	<li>
		<a><?php echo $current_selected;?></a>
		<ul class="search-advanced-type-selection-dropdown">
			<li>
				<a><?php echo elgg_echo("all");?></a>
			</li>
			<li>
				<a rel='user'><?php echo elgg_echo("item:user");?></a>
			</li>
			<li>
				<a rel='group'><?php echo elgg_echo("item:group");?></a>
			</li>
			
			<?php
				foreach ($types["object"] as $subtype) {
					if ($subtype === "page_top") {
						// skip this one as it is merged with page objects
						continue;
					}
					echo "<li><a rel='object " . $subtype . "'>" . elgg_echo("item:object:" . $subtype) . "</a></li>";
				}
			?>
		</ul>
	</li>
</ul>
