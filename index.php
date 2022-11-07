<?php require "misc/header.php"; ?>

<title>LinXer</title>
<body>
<form class="search-container" action="search.php" method="get" autocomplete="off">
    <h1>
        Lin<span class="X">X</span>er<small style="font-size: 13px">a fork of LibreX</small>
    </h1>
    <input type="text" name="q" autofocus/>
    <input type="hidden" name="p" value="0"/>
    <input type="hidden" name="t" value="0"/>
    <input type="submit" class="hide"/>
    <div class="search-button-wrapper">
        <button name="t" value="0" type="submit">Search</button>
        <button name="t" value="3" type="submit">Search Torrents</button>
    </div>
</form>

<?php require "misc/footer.php"; ?>
