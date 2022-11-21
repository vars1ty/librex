<?php
return (object)array(
    // e.g.: fr -> https://google.fr/
    "google_domain" => "com",

    // Google results will be in this language
    "google_language" => "en",

    // Checkboxes don't currently support this config, hence they are left out.
    "disable_bittorent_search" => false,
    "bittorent_trackers" => "&tr=http%3A%2F%2Fnyaa.tracker.wf%3A7777%2Fannounce&tr=udp%3A%2F%2Fopen.stealth.si%3A80%2Fannounce&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce&tr=udp%3A%2F%2Fexodus.desync.com%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.torrent.eu.org%3A451%2Fannounce",

    // Enable Ahmia Onion searching?
    "disable_hidden_service_search" => false,

    /*
     * Below you'll see redirects to open-source re-implementations of common platform frontends.
     * They have all been hand-picked for 2 things:
     * 1. Low latency
     * 2. Which country its hosted in
     * If the country can't be deemed as secure with **your** data and the project is hosted on an unknown/insecure provider, it won't be added.
     * This eliminates several countries, especially the United States **and** most European countries that are known for having obscure/questionable
     * data-privacy laws and are rapidly becoming worse, such as Sweden.
     * There's exceptions though, like with CloudTube which is hosted by Vultr in Australia.
     */

    // YouTube
    "invidious" => "https://tube.cadence.moe", // <= Hosted in the United States through Vultr with relatively low latency
    // Twitter
    "nitter" => "https://nitter.it", // <= Hosted in Finland with low latency
    // Reddit
    "libreddit" => "https://libreddit.garudalinux.org", // <= Hosted in Finland with low latency
    // TikTok
    "proxitok" => "https://proxitok.pabloferreiro.es", // <= Hosted in German with low latency, official instance
    // Translate
    "libretranslate" => "https://libretranslate.com", // <= Location unknown (CF), official instance, low latency
    // Wikipedia
    // Wikiless hardly does anything but bypass censorship in some countries, and remove some small amounts of non-critical JavaScript.
    "wikiless" => "https://wikiless.org", // <= Hosted in Finland through Hetzner with low latency, official instance

    /*
        To send requests trough a proxy uncomment CURLOPT_PROXY and CURLOPT_PROXYTYPE:

        CURLOPT_PROXYTYPE options:

            CURLPROXY_HTTP
            CURLPROXY_SOCKS4
            CURLPROXY_SOCKS4A
            CURLPROXY_SOCKS5
            CURLPROXY_SOCKS5_HOSTNAME

        !!! ONLY CHANGE THE OTHER OPTIONS IF YOU KNOW WHAT YOU ARE DOING !!!
    */
    "curl_settings" => array(
        // CURLOPT_PROXY => "ip:port",
        // CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        // This should preferably be up-to-date with the latest Chrome version to blend in with requests.
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36",
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        // Only allow 3 redirects, unlike the standard 5.
        CURLOPT_MAXREDIRS => 3,
        // Lower timeout than the standard 18.
        CURLOPT_TIMEOUT => 8,
        CURLOPT_VERBOSE => false
    )
);