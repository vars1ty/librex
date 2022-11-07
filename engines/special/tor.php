<?php
function tor_result($response): array
{
    $formatted_response = "It seems like you are not using Tor";
    if (str_contains($response, $_SERVER["REMOTE_ADDR"])) {
        $formatted_response = "It seems like you are using Tor";
    }

    $source = "https://check.torproject.org";
    return array(
        "special_response" => array(
            "response" => $formatted_response,
            "source" => $source
        )
    );
}