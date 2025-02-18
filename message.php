<?php
        session_start();
        include("util.php");
        $thread = getCurrentThread();
        $page = print_pages($thread);
        printMessages($thread, $page);
?>