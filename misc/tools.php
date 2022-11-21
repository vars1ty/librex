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

        if (empty(trim($frontend)))
            return $url;

        return $frontend . explode($original, $url)[1];
    }

    return $url;
}

function check_for_privacy_frontend($url)
{
    if (isset($_COOKIE["disable_frontends"])) return $url;

    $frontends = array(
        "youtube.com" => "invidious",
        "twitter.com" => "nitter",
        "reddit.com" => "libreddit",
        "tiktok.com" => "proxitok",
        "translate.google.com" => "libretranslate",
        "wikipedia.org" => "wikiless"
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
    $ch = new_curl($url);
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
    $string = preg_replace("/[\r\n]+/", "\n", $string);
    return trim(preg_replace("/\s+/", ' ', $string));
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
    echo "<label>$header";
    echo "<input title='$tooltip' type='checkbox' name='$name' " . (isset($_COOKIE[$name]) ? "checked" : "") . "/>";
    echo "</label><br>";
}

/** Creates a new CURL instance. */
function new_curl($url): CurlHandle
{
    return curl_init($url);
}

/** Generates an input field with $_COOKIE support.<br>
 * The cookie is identified via <b>$name</b> and the input value is automatically synced according to the cookies value.
 */
function generate_input_field($name, $side_header, $side_header_url, $placeholder): void
{
    global $config;
    echo "<a " . (!empty($side_header_url) ? "href='$side_header_url' target='_blank'" : "") . ">$side_header</a>";
    echo "<label>";
    echo "<input type='text' name='$name' placeholder='$placeholder' value='" . (isset($_COOKIE[$name]) ? htmlspecialchars($_COOKIE[$name]) : $config->$name) . "'>";
    echo "</label>";
}

/** Generates a search category button, like <b>Torrents</b>. */
function generate_search_cat_button($id, $img, $text): void
{
    echo "<button name='t' value='$id'>";
    echo "<img src='static/images/$img' class='img-search-gray' alt='' width='20' height='20'/>";
    echo $text;
    echo "</button>";
}

/** Returns the visitors IP-Address. */
function get_ip(): string
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"]))
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else $ip = $_SERVER["REMOTE_ADDR"];

    return $ip;
}

/** Returns the visitors User Agent. */
function get_user_agent(): string
{
    return $_SERVER['HTTP_USER_AGENT'];
}

/** Removes all comments from the read content of given file, making it possible to decode through json_decode. */
function jsonc_remove_comments($file): string
{
    $lines = file($file);
    $tmp = "";
    $comment = "  //";
    foreach ($lines as $line) {
        // Too short to be a comment, add since it's safe.
        if (strlen($line) < 5) {
            $tmp .= "$line\n";
            continue;
        }

        $substr = substr($line, 0, 4);
        // If the substring isn't containing "  //", then it's not the start of a new-line comment and is safe to add.
        if ($substr !== $comment)
            $tmp .= "$line\n";
    }

    return $tmp;
}

/** Determines what message to be displayed on the right side of the content. */
// TODO: Bring back Wikipedia support.
function determine_side_message($query): void
{
    $lower = strtolower($query);

    // JSON register
    // Remove lines which only contain a comment, skipping the need of a JSONC library.
    $decoded = json_decode(jsonc_remove_comments("static/misc/risks.jsonc"));

    // Cache all words into $matches.
    preg_match_all("/\w+/", $lower, $matches);

    // Check if the JSON contains any item from $matches.
    if (!empty($matches[0])) {
        foreach ($matches[0] as $match) {
            if (!empty($decoded->$match)) {
                set_side_message($decoded->$match, null, null, "orangered");
                return;
            }
        }
    }

    // Dynamic
    if (str_contains($lower, "my ip")) set_side_message("Your IP-Address is: " . get_ip(), "", "", "");
    elseif (str_contains($lower, "my user agent")) set_side_message("Your User Agent is: " . get_user_agent(), "", "", "");
}