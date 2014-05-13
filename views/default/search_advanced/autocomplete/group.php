<?php
$entity = $vars["entity"];

echo "<img src='" . $entity->getIconURL("tiny") . "' /> " . $entity->name;
