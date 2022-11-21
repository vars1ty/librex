<?php
$config = require "config.php";
require "./misc/tools.php";

if (isset($_REQUEST["reset"])) {
    if (isset($_SERVER["HTTP_COOKIE"])) {
        destroy_settings_cookies();
    }
}

if (isset($_REQUEST["save"])) {
    destroy_settings_cookies();
    foreach ($_POST as $key => $value) {
        if (!empty($value)) {
            setcookie($key, $value, time() + (86400 * 90), '/'); // '/' is needed for cookies to work properly.
        }
    }
}

if (isset($_REQUEST["save"]) || isset($_REQUEST["reset"])) {
    header("Location: ./settings.php");
    die();
}

require "misc/header.php";
?>

<title>LinXer - Settings</title>
<body>
<div class="misc-container">
    <h1>Settings</h1>
    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <div>
            <label for="theme">Theme:</label>
            <label>
                <select name="theme">
                    <?php
                    $themes = "<option value=\"dark\">Dark</option>
                        <option value=\"darker\">Darker</option>
                        <option value=\"amoled\">AMOLED</option>
                        <option value=\"light\">Light</option>
                        <option value=\"auto\">Auto</option>
                        <option value=\"nord\">Nord</option>
                        <option value=\"night_owl\">Night Owl</option>
                        <option value=\"discord\">Discord</option>
                        <option value=\"google\">Google Dark</option>
                        <option value=\"startpage\">Startpage Dark</option>
                        <option value=\"gruvbox\">Gruvbox</option>
                        <option value=\"github_night\">GitHub Night</option>";

                    if (isset($_COOKIE["theme"])) {
                        $cookie_theme = $_COOKIE["theme"];
                        $themes = str_replace($cookie_theme . "\"", $cookie_theme . "\" selected", $themes);
                    }

                    echo $themes;
                    ?>
                </select>
            </label>
        </div>
        <h2>Privacy friendly frontends</h2>
        <p>For an example if you want to view YouTube without getting spied on, click on "Invidious", find the instance
            that is most suitable for you then paste it in (correct format: https://example.com)</p>
        <div class="instances-container">
            <div>
                <?php
                generate_input_field("invidious", "Invidious", "https://docs.invidious.io/instances/", "Replace YouTube");
                ?>
            </div>
            <div>
                <?php
                generate_input_field("nitter", "Nitter", "https://github.com/zedeus/nitter/wiki/Instances", "Replace Twitter");
                ?>
            </div>
            <div>
                <?php
                generate_input_field("libreddit", "Libreddit", "https://github.com/spikecodes/libreddit", "Replace Reddit");
                ?>
            </div>
            <div>
                <?php
                generate_input_field("proxitok", "ProxiTok", "https://github.com/pablouser1/ProxiTok/wiki/Public-instances", "Replace TikTok");
                ?>
            </div>
            <div>
                <?php
                generate_input_field("libretranslate", "LibreTranslate", "", "Replace Translate");
                ?>
            </div>
            <div>
                <?php
                generate_input_field("wikiless", "Wikiless", "", "Replace Wikipedia");
                ?>
            </div>
        </div>
        <div>
            <?php generate_checkbox("disable_frontends", "Disable privacy frontends", "Disables privacy frontends"); ?>
        </div>
        <div>
            <h2>Privacy Enhancements</h2>
            <?php
            generate_checkbox("disable_side_info", "Disable side information", "Disables side information like results when you write \"what is my ip\", warnings when you search up insecure sites and so on.");
            ?>
        </div>
        <div>
            <button type="submit" name="save" value="1">Save</button>
            <button type="submit" name="reset" value="1">Reset</button>
        </div>
    </form>
</div>

<?php require "misc/footer.php"; ?>
