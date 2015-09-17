<?php ?>
.search-advanced-type-selection {
	display: inline-block;
	position: relative;
}

.search-advanced-type-selection-dropdown {
	display: none;
	position: absolute;
	top: 18px;
	right: -4px;
	background: white;
	border: 1px solid #71B9F7;
	padding: 10px 20px 10px 10px;
	z-index: 10;
	
	text-align: right;
	
	-webkit-box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.5);
	-moz-box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.5);
	box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.5);
	
}

.search-advanced-type-selection > li {
	display: inline;
	position: relative;
	height: 20px;
}

.search-advanced-type-selection > li > a {
	padding: 0 5px;
	margin-right: -4px;
	display: inline-block;
	height: 20px;
	line-height: 20px;
	background: #71B9F7;
	white-space: nowrap;
	color: white;
	font-weight: bold;
}

.search-advanced-type-selection > li > a:after {
	content: "\25BC";
	padding: 0 0 0 2px;
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
	border: 1px solid #71B9F7;
	height: 19px;
	z-index: 1;
}

.elgg-search input[type="text"].search-input {
	background-position: 210px -934px;
	padding: 0px 26px 0px 4px;
	height: 19px;
	border: none;
	border-left: 1px solid #71B9F7;
	-webkit-border-radius: 0px;
	-moz-border-radius: 0px;
	border-radius: 0px;
	position: relative;
	vertical-align: top;
}

.elgg-form-search-advanced-search fieldset > div,
.search-advanced-user-search fieldset > div {
	margin-bottom: 5px;
}

.elgg-form-search-advanced-search > fieldset > div label {
	min-width: 130px;
	display: inline-block;
}

.elgg-form-search-advanced-search > fieldset > div .elgg-input-text {
	width: 100px;
	margin: 0 10px;
}

.elgg-menu-search-list .elgg-child-menu {
	display: none;
	position: absolute;
	right: 0;
	background: white;
	border: 1px solid #CCC;
	
	z-index: 1;
	
	border-radius: 0 0 3px 3px;
	box-shadow: 1px 3px 5px rgba(0, 0, 0, 0.25);
}

.elgg-menu-search-list .elgg-child-menu a {
	padding: 5px 10px;
	color: #444;
	text-decoration: none;
	white-space: nowrap;
}

.elgg-menu-search-list .elgg-child-menu li.elgg-state-selected a {
	font-weight: bold;
}
.elgg-menu-search-list .elgg-child-menu a:hover {
	background-color: #F0F0F0;
	color: #444;
}
	
select.search-advanced-search-types {
	max-width: 100%;
}
select.search-advanced-search-types .search-advanced-search-types-parent {
	font-weight: bold;
	font-style: italic;
}
select.search-advanced-search-types .search-advanced-search-types-child {
	padding-left: 20px;
}