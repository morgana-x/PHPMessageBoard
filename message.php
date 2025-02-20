<?php
        session_start();
        include("config.php");
        include("scripts/util.php");



        $thread = getCurrentThread();
        $page = print_pages($thread);
        printMessages($thread, $page);
?>