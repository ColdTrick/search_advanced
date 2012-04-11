<?php
$types = get_registered_entity_types();

echo elgg_view("input/hidden", array("name" => "type"));

?>
<ul class="search-advanced-type-selection">
	<li>
		<a>All</a>
		<ul class="search-advanced-type-selection-dropdown">
			<li>
				<a>All</a>
			</li>
			<li>
				<a>Users</a>
			</li>
			<li>
				<a>Groups</a>
			</li>
			<li>
				<a>Content</a>
				<ul>
					<?php 
						foreach($types["object"] as $subtype){
							echo "<li><a>" . elgg_echo("item:object:" . $subtype) . "</a></li>";
						}
					?>
				</ul>
			</li>
			
		</ul>
	</li>
	
</ul>

<style type="text/css">

.search-advanced-type-selection {
	display: inline-block;
	position: relative;
}

.search-advanced-type-selection-dropdown {
	display: none;
	position: absolute;
	right: -4px;
	background: white;
	border: 1px solid #4690D6;
	padding: 10px 20px 10px 10px;
	z-index: 10;
	
	text-align: right;
	
	-webkit-box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.5);
	-moz-box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.5);
	box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.5);
	
}

.search-advanced-type-selection > li > a {
	padding: 0 5px;
	margin-right: -4px;
	display: inline-block;
	height: 19px;
	background: #666;
}

.search-advanced-type-selection > li > a:hover {
	text-decoration: none;
}

.search-advanced-type-selection a {
	color: #333;
	cursor: pointer;
	
}
.search-advanced-type-selection-dropdown a {
	padding: 5px;
	white-space: nowrap;
	display: inline-block;
	width: 100%;
}

.search-advanced-type-selection-dropdown a:hover {
	background: #4690D6;
	color: white;
	text-decoration: none;
}

form.elgg-search {
	border: 1px solid #666666;
	height: 19px;
}

.elgg-search input[type="text"].search-input {
	background-position: 210px -934px;
	padding: 2px 26px 2px 4px;
	border: none;
	border-left: 1px solid #666666;
	-webkit-border-radius: 0px;
	-moz-border-radius: 0px;
	border-radius: 0px;
}

</style>

<script type="text/javascript">

	$(document).ready(function(){
		$(".search-advanced-type-selection > li > a").click(function(e){
			$(this).next().show();
			e.preventDefault();
			e.stopPropagation();
		});

		$(".search-advanced-type-selection-dropdown").click(function(e){
			e.stopPropagation();
		});

		$(".search-advanced-type-selection-dropdown a").click(function(e){
			$(".search-advanced-type-selection > li > a").html($(this).html());
			$(".search-advanced-type-selection-dropdown").hide();
		});
	});

	$(document).click(function(){
		$(".search-advanced-type-selection-dropdown").hide();
	});

</script>