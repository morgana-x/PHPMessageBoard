<?php
    if (!file_exists(BAN_FILE))
    {
        touch(BAN_FILE);
       // chmod(BAN_FILE, 606);
    }
    $threads_file = fopen(THREAD_FILE, "r");
    while(!feof($threads_file)) {
        $thread = fgets($threads_file);
        $thread = trim($thread);
        if ($thread == "") continue;
        $path = THREAD_FOLDER . "/{$thread}.txt";
        if (file_exists($path))
            continue;
        touch($path);
        chmod($path, 0600);
    }
	fclose($threads_file);
?>