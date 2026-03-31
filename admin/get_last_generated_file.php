<?php

// get_last_generated_file.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$excelDirectory = __DIR__ . '/excel';

// Get the list of all files in the /admin/excel directory
$files = scandir($excelDirectory, SCANDIR_SORT_DESCENDING);

// Exclude . and .. from the list
$files = array_diff($files, ['.', '..']);

// Get the first (latest) file from the list
$latestFile = reset($files);

// Send the filename as the response
echo $latestFile;
