<?php
function get_text_results($query, $page = 0): array
{
    global $config;
    $mh = curl_multi_init();

    $query_encoded = urlencode($query);
    $results = array();

    $url = "https://www.google.$config->google_domain/search?&q=$query_encoded&start=$page&hl=$config->google_language";
    $google_ch = new_curl($url);

    curl_setopt_array($google_ch, $config->curl_settings);
    curl_multi_add_handle($mh, $google_ch);

    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running);

    $xpath = get_xpath(curl_multi_getcontent($google_ch));

    foreach ($xpath->query("//div[@id='search']//div[contains(@class, 'g')]") as $result) {
        $url = $xpath->evaluate(".//div[@class='yuRUbf']//a/@href", $result)[0];

        if (empty($url)) continue;

        if (!empty($results)) // filter duplicate results, ignore special result
            if (end($results)["url"] == $url->textContent)
                continue;

        $url = $url->textContent;
        $url = check_for_privacy_frontend($url);
        $title = $xpath->evaluate(".//h3", $result)[0];
        $description = $xpath->evaluate(".//div[contains(@class, 'VwiC3b')]", $result)[0];

        $results[] = array(
            "title" => htmlspecialchars($title->textContent),
            "url" => htmlspecialchars($url),
            "base_url" => htmlspecialchars(get_base_url($url)),
            "description" => $description == null ?
                "No description was provided for this site." :
                str_replace("...", '…', htmlspecialchars($description->textContent))
        );
    }

    return $results;
}

/** Sets the side message. */
function set_side_message($message, $img, $source, $border_color): void
{
    echo "<p class='special-result-container'" . (!empty($border_color) ? " style='border-color: $border_color'" : "") . ">";
    if (!empty($img))
        echo htmlspecialchars("<img src='$img' alt=''/>");

    echo htmlspecialchars($message);
    if (!empty($source))
        echo htmlspecialchars("<a href='$source' target='_blank'>$source</a>");
}

/** Prints the text results alongside a side message (if available). */
function print_text_results($query, $results): void
{
    if (!empty($query) && !isset($_COOKIE["disable_side_info"])) determine_side_message($query);
    echo "<div class='text-result-container'>";
    foreach ($results as $result) {
        $title = $result["title"];
        $url = $result["url"];
        $description = $result["description"];
        echo "<div class='text-result-wrapper'>";
        echo "<a href='$url'>";
        $small_url = substr($url, 0, 58);
        // If the URL contains 58 or more characters, put "…" at the end.
        // For example: https://something-here-abc-abc-abc-abc-abc.com/hello-artic…
        if (strlen($url) >= 58) $small_url .= "…";
        echo $small_url;
        echo "<h2>$title</h2>";
        echo "</a>";
        echo "<span>$description</span>";
        echo "</div>";
    }

    echo "</div>";
}