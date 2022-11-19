<?php
require "misc/header.php";

/** Prints a formatted Question and Answer. */
function print_qa($question, $answer): void
{
    echo "<li>$question</li>";
    echo "<small>$answer</small>";
    echo "<br><br>";
}

?>

    <title>LinXer - F.A.Q</title>
    <body>
<div class="misc-container">
    <h1>F.A.Q</h1>
    <h3>Privacy</h3>
    <hr>
    <?php
    print_qa("Is my IP saved?", "Your IP-Address is logged for a few hours due to automatic traffic monitoring software present on the hosts side.
    <br>The IP is not tied to anything like your search queries, it's just a line like this: <code>1.1.1.1</code>.");
    print_qa("Where is it hosted?", "LinXer has no set location for more than a few days.
    <br>It changes between:<br>
    <li>Iceland</li>
    <li>Norway</li>
    <li>Switzerland</li>
    <li>Romania</li>
    <li>Amsterdam</li>
    <li>Sweden</li>
    To ensure that your data isn't always flowing through one country, but rather several.<br>
    There's one downside to this though: The uptime may fluctuate, so you have been warned.");
    print_qa("What's your host?", "LinXer is self-hosted on a virtual machine with tons of spare resources, located in a country with decent privacy laws.");
    print_qa("Is CloudFlare being used?", "Yes, CloudFlare is being used to reverse-proxy traffic from the server due to it being behind an unusual port.");
    print_qa("Where are you getting your search results from?", "Google, Qwant and Brave. Then there's other sources for torrents, special query lookups, etc.
    <br>You may find more information on the projects GitHub-page.");
    print_qa("How can you hide my IP from the search engines you are scraping?", "By using CURL, the engines <b>only</b> get the IP-Address of the website which originates from one of the countries above, then it's displayed for you.
<br>It works like this:<br>Search -> LinXer fetches several engines using CURL with its own IP -> Parse -> Format -> Done, you may now see the results");
    print_qa("Are cookies being used?", "Yes, cookies are being used. This is done to save your theme, custom frontends, and so forth.
    <br>You may manage/inspect all cookies by opening the Developer Tools -> Storage -> Cookies -> LinXer
    <br><br><b>Note</b>: Although cookies are being used, disabling cookies through your browser will still let the website function as normal 🤝");
    print_qa("Is JavaScript being used?", "No and it never will be.");
    ?>
    <h3>Misc</h3>
    <hr>
    <?php
    print_qa("Why fork LibreX?", "That's like asking why some people made a fork of Firefox called LibreWolf.
    <br><b>tl;dr</b>: Privacy enhancements, some code improvements, refactoring and new features.");
    print_qa("Does LinXer support bangs?", "Yes because LibreX implemented this feature. It's the same syntax as on DuckDuckGo.");
    print_qa("Why are images loading so slow?", "Because they are also loaded behind a different IP and aren't done in complete parallel currently.");
    print_qa("How can I set LinXer as my search engine?", "
    <a href='https://addons.mozilla.org/en-US/firefox/addon/add-custom-search-engine/' target='_blank'>Add custom search engine</a> for Firefox<br>
    <a href='https://zapier.com/blog/add-search-engine-to-chrome/' target='_blank'>Guide</a> for Chromium-based browsers");
    ?>
</div>

<?php require "misc/footer.php"; ?>