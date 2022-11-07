<?php
function get_base_url($url): string
{
    $split_url = explode("/", $url);
    return $split_url[0] . "//" . $split_url[2] . "/";
}

function try_replace_with_frontend($url, $frontend, $original)
{
    $config = require "config.php";

    if (isset($_COOKIE[$frontend]) || isset($_REQUEST[$frontend]) || !empty($config->$frontend)) {
        if (isset($_COOKIE[$frontend]))
            $frontend = $_COOKIE[$frontend];
        else if (isset($_REQUEST[$frontend]))
            $frontend = $_REQUEST[$frontend];
        else if (!empty($config->$frontend))
            $frontend = $config->$frontend;

        if ($original == "instagram.com") {
            if (!strpos($url, "/p/"))
                $frontend .= "/u";
        }

        if (empty(trim($frontend)))
            return $url;

        return $frontend . explode($original, $url)[1];
    }

    return $url;
}

function check_for_privacy_frontend($url)
{
    if (isset($_COOKIE["disable_frontends"]))
        return $url;

    $frontends = array(
        "youtube.com" => "invidious",
        "instagram.com" => "bibliogram",
        "twitter.com" => "nitter",
        "reddit.com" => "libreddit",
        "tiktok.com" => "proxitok"
    );

    foreach ($frontends as $original => $frontend) {
        if (strpos($url, $original)) {
            $url = try_replace_with_frontend($url, $frontend, $original);
            break;
        }
    }

    return $url;
}

function check_ddg_bang($query): void
{

    $bangs_json = file_get_contents("static/misc/ddg_bang.json");
    $bangs = json_decode($bangs_json, true);

    $array = explode(" ", $query);
    if (str_starts_with($query, "!"))
        $search_word = substr($array[0], 1);
    else
        $search_word = substr(end($array), 1);

    $bang_url = null;

    foreach ($bangs as $bang) {
        if ($bang["t"] == $search_word) {
            $bang_url = $bang["u"];
            break;
        }
    }

    if ($bang_url) {
        $bang_query_array = explode("!" . $search_word, $query);
        $bang_query = trim(implode("", $bang_query_array));

        $request_url = str_replace("{{{s}}}", $bang_query, $bang_url);
        $request_url = check_for_privacy_frontend($request_url);

        header("Location: " . $request_url);
        die();
    }
}

function get_xpath($response): DOMXPath
{
    $htmlDom = new DOMDocument;
    @$htmlDom->loadHTML($response);
    return new DOMXPath($htmlDom);
}

function request($url): bool|string
{
    global $config;

    $ch = curl_init($url);
    if (isset($_COOKIE["use_quad9"])) {
        curl_setopt($ch, CURLOPT_DOH_URL, $config->quad9_doh);
    }

    curl_setopt_array($ch, $config->curl_settings);
    return curl_exec($ch);
}

function human_filesize($bytes, $dec = 2): string
{
    $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f ", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function remove_special($string): array|string|null
{
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function print_elapsed_time($results, $start_time): void
{
    $end_time = number_format(microtime(true) - $start_time, 2, '.', '');
    echo "<p id=\"time\">Fetched $results results in $end_time seconds</p>";
}

function print_next_page_button($text, $page, $query, $type): void
{
    echo "<form class=\"page\" action=\"search.php\" target=\"_top\" method=\"get\" autocomplete=\"off\">";
    foreach ($_REQUEST as $key => $value) {
        if ($key != "q" && $key != "p" && $key != "t") {
            echo "<input type=\"hidden\" name=\"$key\" value=\"$value\"/>";
        }
    }
    echo "<input type=\"hidden\" name=\"p\" value=\"" . $page . "\" />";
    echo "<input type=\"hidden\" name=\"q\" value=\"$query\" />";
    echo "<input type=\"hidden\" name=\"t\" value=\"$type\" />";
    echo "<button type=\"submit\">$text</button>";
    echo "</form>";
}

/** Destroys (unset) ALL user-defined setting cookies */
function destroy_settings_cookies(): void
{
    foreach ($_COOKIE as $k => $v) {
        setcookie($k, null, 0, '/');
    }
}

/** Generates a checkbox with $_COOKIE support.<br>
 * The cookie is identified via <b>$name</b> and the value is automatically synced according to the cookies value.
 */
function generate_checkbox($name, $header, $tooltip): void
{
    echo
        "<label>" . $header . "
           <input title='" . $tooltip . "' type='checkbox' name='" . $name . "' " . (isset($_COOKIE[$name]) ? "checked" : "") . ">
        </label>";
}