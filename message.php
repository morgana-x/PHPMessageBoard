<?php
        include("util.php");
        $thread = getCurrentThread();
        $page = print_pages($thread);
        printMessages($thread, $page);
?>