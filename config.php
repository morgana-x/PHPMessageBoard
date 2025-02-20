<?php
    define("FORUM_TITLE", "Cool Messaging Board :3");
    define("PAGE_SIZE", 50);
    define("FORUM_DATA_FOLDER", __DIR__. "/MSGBOARD_DATA");
    define("ADMIN_FILE", FORUM_DATA_FOLDER."/admin.txt");
    define("BAN_FILE", FORUM_DATA_FOLDER."/ban.txt");
    define("THREAD_FOLDER", FORUM_DATA_FOLDER."/threads");
    define("THREAD_FILE",  __DIR__ . "/threads.txt");
    define("UPLOADS_FOLDER_RELATIVE", "uploads");
    define("UPLOADS_FOLDER", __DIR__ . "/" . UPLOADS_FOLDER_RELATIVE);
    define("UPLOADS_ALLOWEDEXTENSIONS", array("png", "jpg", "jpeg", "webp", "jfif"));
    define("UPLOADS_MAX_WIDTH", 1024);
    define("UPLOADS_MAX_HEIGHT", 848);
    define("UPLOADS_ALLOWEDFORMATS", array (
        // IMAGETYPE_GIF,
         IMAGETYPE_JPEG,
         IMAGETYPE_PNG,
       //  IMAGETYPE_SWF,
      //   IMAGETYPE_PSD,
         IMAGETYPE_BMP,
     //    IMAGETYPE_TIFF_II,
     /*    IMAGETYPE_TIFF_MM,
         IMAGETYPE_JPC,
         IMAGETYPE_JP2,
         IMAGETYPE_JPX,
         IMAGETYPE_JB2,*/
      //   IMAGETYPE_SWC,
      //   IMAGETYPE_IFF,*/
         IMAGETYPE_WBMP,
        // IMAGETYPE_XBM,
        // IMAGETYPE_ICO 
     ));
    define("UPLOADS_MAXFILESIZE",1000000); // 1000000
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

    if (!is_dir(UPLOADS_FOLDER))
        mkdir(UPLOADS_FOLDER);


    touch(FORUM_DATA_FOLDER . "/.htaccess");
    $threadaccess = fopen(FORUM_DATA_FOLDER . "/.htaccess", "wb");
    fwrite($threadaccess, "order deny,allow\ndeny from all\nallow from 127.0.0.1");
    fclose($threadaccess);


    touch(THREAD_FOLDER . "/.htaccess");
    $threadaccess = fopen(THREAD_FOLDER . "/.htaccess", "wb");
    fwrite($threadaccess, "order deny,allow\ndeny from all\nallow from 127.0.0.1");
    fclose($threadaccess);

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
        //chmod(ADMIN_FILE, 606);
    }
    if (!file_exists(BAN_FILE))
    {
        touch(BAN_FILE);
       // chmod(BAN_FILE, 606);
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