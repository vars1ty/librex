<?php

/*
 * LibreX does this through HTML mixed with CSS, but LinXer does it through echo because
 * it makes the entire header get merged into one line, reducing the size by a smaller fraction.
 */

$theme = "static/css/";
if (isset($_COOKIE["theme"]) || isset($_REQUEST["theme"]))
    $theme = "static/css/" . htmlspecialchars(($_COOKIE["theme"] ?? $_REQUEST["theme"]) . ".css");
else
    $theme = "static/css/dark.css";

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'/>";
echo "<meta charset='UTF-8'/>";
echo "<meta name='description' content='An open-source privacy respecting meta search engine.'/>";
echo "<meta name='referrer' content='no-referrer'/>";
echo "<link rel='stylesheet' type='text/css' href='static/css/styles.css'/>";
echo "<link title='LinXer' type='application/opensearchdescription+xml' href='/opensearch.xml?method=POST' rel='search'/>";
echo "<link rel='stylesheet' type='text/css' href='$theme'/>";