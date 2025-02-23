<?php
    define("FORUM_TITLE", "Cool Messaging Board :3");
    define("PAGE_SIZE", 50);
    define("THEME_FOLDER", __DIR__."/themes");
    define("THREAD_FILE",  __DIR__ . "/threads.txt");
    define("UPLOADS_FOLDER_RELATIVE", "uploads");
    define("UPLOADS_FOLDER", __DIR__ . "/" . UPLOADS_FOLDER_RELATIVE);
    define("UPLOADS_ALLOWEDEXTENSIONS", array("png", "jpg", "jpeg", "webp", "jfif", "gif"));
    define("UPLOADS_MAX_WIDTH", 1280);
    define("UPLOADS_MAX_HEIGHT", 1280);
	define("UPLOADS_MAX_WIDTH_RENDER", 1000); // Render in forums message
    define("UPLOADS_MAX_HEIGHT_RENDER", 800); // Render in forums message
    define("UPLOADS_ALLOWEDFORMATS", array (IMAGETYPE_GIF, IMAGETYPE_JPEG,IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_BMP, IMAGETYPE_WBMP, IMAGETYPE_TIFF_MM, IMAGETYPE_TIFF_II,IMAGETYPE_JPC, IMAGETYPE_JP2, IMAGETYPE_JPX, IMAGETYPE_SWF,IMAGETYPE_XBM));
    define("UPLOADS_MAXFILESIZE",1500000); // 1000000
    define("SQLCONFIG_FILE", __DIR__."/SQL.txt");
    define("FORUM_DATA_FOLDER", __DIR__. "/MSGBOARD_DATA");
    define("ADMIN_FILE", FORUM_DATA_FOLDER."/admin.txt");
    define("BAN_FILE", FORUM_DATA_FOLDER."/ban.txt");
    define("THREAD_FOLDER", FORUM_DATA_FOLDER."/threads");
    if (!file_exists(SQLCONFIG_FILE))
    {
        $cfgFileCreate = fopen(SQLCONFIG_FILE, "w");
        fwrite($cfgFileCreate, "enabled: true\nserver-name: localhost\nuser-name: user\npassword: password");
        fclose($cfgFileCreate);
    }
    $sqlcfgFile = fopen(SQLCONFIG_FILE, "r");
        define("FORUM_SQL_ENABLED"   , trim(explode(":",fgets($sqlcfgFile))[1]) == "true");
        define("FORUM_SQL_SERVERNAME", trim(explode(":",fgets($sqlcfgFile))[1]));
        define("FORUM_SQL_USERNAME"  , trim(explode(":",fgets($sqlcfgFile))[1]));
        define("FORUM_SQL_PASSWORD"  , trim(explode(":",fgets($sqlcfgFile))[1]));
    fclose($sqlcfgFile);

    if (!FORUM_SQL_ENABLED)
        include("config_noSQL.php");
    else
        include("scripts/sql_setupDB.php");
    $defaultThreads = array(
        "main", 
        "cats", 
        "cheese enthusiasts", 
        "games",
        "technology"
    );

    if (!is_dir(UPLOADS_FOLDER))
        mkdir(UPLOADS_FOLDER);

    if (!is_dir(THEME_FOLDER))
        mkdir(THEME_FOLDER);


    if (!file_exists(THREAD_FILE))
    {
        $threads_file = fopen(THREAD_FILE, "w");
        foreach($defaultThreads as $a)
            fwrite($threads_file, "{$a}\n");
        fclose($threads_file);
    }

    $defaultAdmins = array(
        "morgana|catsarecool!!!"
    );
    if (!is_dir(FORUM_DATA_FOLDER))
        mkdir(FORUM_DATA_FOLDER);

    if (!is_dir(THREAD_FOLDER))
        mkdir(THREAD_FOLDER);

    touch(FORUM_DATA_FOLDER . "/.htaccess");
    $threadaccess = fopen(FORUM_DATA_FOLDER . "/.htaccess", "wb");
    fwrite($threadaccess, "order deny,allow\ndeny from all\nallow from 127.0.0.1");
    fclose($threadaccess);


    touch(THREAD_FOLDER . "/.htaccess");
    $threadaccess = fopen(THREAD_FOLDER . "/.htaccess", "wb");
    fwrite($threadaccess, "order deny,allow\ndeny from all\nallow from 127.0.0.1");
    fclose($threadaccess);

    if (!file_exists(ADMIN_FILE))
    {
        $admins_file = fopen(ADMIN_FILE, "w");
        foreach($defaultAdmins as $a)
            fwrite($admins_file, "{$a}\n");
        fclose($admins_file);
        //chmod(ADMIN_FILE, 606);
    }
?>