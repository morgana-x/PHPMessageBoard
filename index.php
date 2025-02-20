<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">
    <link rel="icon" type="image/x-icon" href="/imgs/favicon/favicon.ico">
<?php

    session_start();
    include("config.php");
    include("scripts/util.php");
    if (isBanned(getIp()) )
    {
        echo("</head>");
        echo("You are temp banned from this message board1!1!");
        echo("Please wait a few days or so before returning!");
        return;
    }
    include("scripts/header.php");
    include("scripts/thread.php");
    updateBans();
?>

<div class="pattern"></div>
</body>
</html>