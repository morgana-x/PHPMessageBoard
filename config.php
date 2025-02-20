<?php
    define("FORUM_TITLE", "Cool Messaging Board :3");
    define("PAGE_SIZE", 50);
    define("FORUM_DATA_FOLDER", __DIR__. "/MSGBOARD_DATA");
    define("ADMIN_FILE", FORUM_DATA_FOLDER."/admin.txt");
    define("THREAD_FOLDER", FORUM_DATA_FOLDER."/threads");
    define("THREAD_FILE",  __DIR__ . "/threads.txt");

    $defaultThreads = array(
        "main", 
        "cats", 
        "cheese enthusiasts", 
        "games",
        "technology"
    );
    $defaultAdmins = array(
        "morgana|catsarecool!!!"
    );
    if (!is_dir(FORUM_DATA_FOLDER))
        mkdir(FORUM_DATA_FOLDER);

    if (!is_dir(THREAD_FOLDER))
        mkdir(THREAD_FOLDER);

    if (!file_exists(FORUM_DATA_FOLDER . "/.htaccess"))
    {
        touch(FORUM_DATA_FOLDER . "/.htaccess");
        $threadaccess = fopen(FORUM_DATA_FOLDER . "/.htaccess", "wb");
        fwrite($threadaccess, "Deny from all");
        fclose($threadaccess);
    }
    if (!file_exists(THREAD_FOLDER . "/.htaccess"))
    {
        touch(THREAD_FOLDER . "/.htaccess");
        $threadaccess = fopen(THREAD_FOLDER . "/.htaccess", "wb");
        fwrite($threadaccess, "Deny from all");
        fclose($threadaccess);
    }
    if (!file_exists(THREAD_FILE))
    {
        $threads_file = fopen(THREAD_FILE, "w");
        foreach($defaultThreads as $a)
            fwrite($threads_file, "{$a}\n");
        fclose($threads_file);
    }
    if (!file_exists(ADMIN_FILE))
    {
        $admins_file = fopen(ADMIN_FILE, "w");
        foreach($defaultAdmins as $a)
            fwrite($admins_file, "{$a}\n");
        fclose($admins_file);
        chmod(ADMIN_FILE, 606);
    }
    $threads_file = fopen(THREAD_FILE, "r");
    if (!is_dir(THREAD_FOLDER))
        mkdir(THREAD_FOLDER, 606, true);

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
?>