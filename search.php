<?php
require "misc/header.php";
require "misc/tools.php";

$query = htmlspecialchars(trim($_REQUEST["q"]));
echo "<title>" . $query . " - LinXer</title>"
?>
<body>
<form class="sub-search-container" method="get" autocomplete="off">
    <h1 class="logomobile"><a class="no-decoration" href="./">Lin<span class="X">X</span>er</a></h1>
    <label>
        <input type="text" name="q"
            <?php
            $query_encoded = urlencode($query);

            if (empty($query) || strlen($query) > 256) {
                header("Location: ./");
                die();
            }

            echo "value=\"$query\"";
            ?>
        >
    </label>
    <br>
    <?php
    foreach ($_REQUEST as $key => $value) {
        if ($key != "q" && $key != "p" && $key != "t") {
            echo "<input type='hidden' name='$key' value='$value'/>";
        }
    }

    $type = isset($_REQUEST["t"]) ? (int)$_REQUEST["t"] : 0;
    // Set the value to 0 if it's below 1 or higher than 3.
    // Otherwise, the page is renderer improperly since the value is invalid.
    if ($type < 0 || $type > 3) $type = 0;

    echo "<button class='hide' name='t' value='$type'></button>";
    ?>
    <div class="sub-search-button-wrapper">
        <?php
        generate_search_cat_button(0, "search_icon.svg", "General");
        generate_search_cat_button(1, "search_img_icon.svg", "Images");
        generate_search_cat_button(2, "search_vid_icon.svg", "Videos");
        generate_search_cat_button(3, "search_torrents_icon.svg", "Torrents");
        ?>
    </div>
    <hr>
</form>

<?php
$config = require "config.php";

$page = isset($_REQUEST["p"]) ? (int)$_REQUEST["p"] : 0;
if ($page < 0) $page = 0;

$start_time = microtime(true);
switch ($type) {
    case 0:
        $query_parts = explode(" ", $query);
        $last_word_query = end($query_parts);
        if (str_starts_with($query, '!') || str_starts_with($last_word_query, '!'))
            check_ddg_bang($query);

        require "engines/google/text.php";
        $results = get_text_results($query, $page);
        print_elapsed_time(sizeof($results), $start_time);
        print_text_results($query, $results);
        break;

    case 1:
        require "engines/qwant/image.php";
        $results = get_image_results($query_encoded, $page);
        print_elapsed_time(sizeof($results), $start_time);
        print_image_results($results);
        break;

    case 2:
        require "engines/brave/video.php";
        $results = get_video_results($query_encoded);
        print_elapsed_time(sizeof($results), $start_time);
        print_video_results($results);
        break;

    case 3:
        if ($config->disable_bittorent_search)
            echo "<p class=\"text-result-container\">Torrent searches have been disabled.</p>";
        else {
            require "engines/bittorrent/merge.php";
            $results = get_merged_torrent_results($query_encoded);
            print_elapsed_time(sizeof($results), $start_time);
            print_merged_torrent_results($results);
            break;
        }

        break;

    default:
        require "engines/google/text.php";
        $results = get_text_results($query_encoded, $page);
        print_text_results($query, $results);
        print_elapsed_time(sizeof($results), $start_time);
        break;
}


if (2 > $type) {
    echo "<div class='next-page-button-wrapper'>";

    if ($page != 0) {
        print_next_page_button("&lt;&lt;", 0, $query, $type);
        print_next_page_button("&lt;", $page - 10, $query, $type);
    }

    for ($i = $page / 10; $page / 10 + 10 > $i; $i++)
        print_next_page_button($i + 1, $i * 10, $query, $type);

    print_next_page_button("&gt;", $page + 10, $query, $type);

    echo "</div>";
}
?>

<?php require "misc/footer.php"; ?>
