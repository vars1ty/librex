<?php require "misc/header.php"; ?>

<title>LinXer</title>
<body>
<form class="search-container" action="search.php" method="get" autocomplete="off">
    <h1>Libre<span class="X">X</span></h1>
    <input type="text" name="q" autofocus/>
    <input type="hidden" name="p" value="0"/>
    <input type="hidden" name="t" value="0"/>
    <input type="submit" class="hide"/>
    <div class="search-button-wrapper">
        <button class="btn">default button</button>
        <button name="t" value="0" type="submit">Search with LibreX</button>
        <button name="t" value="3" type="submit">Search torrents with LibreX</button>
    </div>
</form>

<?php require "misc/footer.php"; ?>
