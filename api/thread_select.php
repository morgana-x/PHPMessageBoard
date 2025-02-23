<?php
    session_start();
    include("../config.php");
    include("../scripts/util.php");
    if (isBanned(getIp())) return;
    //$CURRENT_THREAD = getCurrentThread();
    echo(getCurrentThread());
    //echo("<h1 align=center class=\"thread_title\" id=\"thread_menu_title\">{$CURRENT_THREAD} thread</h1>");
?>