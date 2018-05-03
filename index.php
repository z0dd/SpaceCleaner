<?php
include_once('init.php');

try {
	$cleaner = new SpaceCleaner(MIN_FREE_SPACE);
	$cleaner->loadNodes($nodes)->start();
} catch (Exception $e) {
	handleError($e);
}

