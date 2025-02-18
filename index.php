<?php
    define("FORUM_TITLE", "Cool Messaging Board :3");
    session_start();
    //include("initsql.php");
    include("util.php");

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
    if (!file_exists("threads.txt"))
    {
        $threads_file = fopen("threads.txt", "w");
        foreach($defaultThreads as $a)
            fwrite($threads_file, "{$a}\n");
        fclose($threads_file);
    }
    if (!file_exists("admin.txt"))
    {
        $admins_file = fopen("admin.txt", "w");
        foreach($defaultAdmins as $a)
            fwrite($admins_file, "{$a}\n");
        fclose($admins_file);
    }
    $threads_file = fopen("threads.txt", "r");
    if (!is_dir("threads"))
        mkdir("threads", 0700, true);
    while(!feof($threads_file)) {
        $thread = fgets($threads_file);
        $thread = trim($thread);
        if ($thread == "") continue;
        $path = "threads/{$thread}.txt";
        if (file_exists($path))
            continue;
        touch($path);
    }

    

    function print_header()
    {
        $FORUM_TITLE = FORUM_TITLE;
        $CURRENT_THREAD = getCurrentThread();
        $CURRENT_THEME = "default";

        echo('<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">
            <title>Message Board</title>');
       /* echo('
            <style TYPE="text/css">
                body,tr,td,th,input,textarea { font-family: "Comic Sans MS", cursive, sans-serif; }
                textarea {background-color:rgb(200, 200, 200)}
            </style>');*/
        echo( str_replace(array("{", "}"), "", "<link rel=\"stylesheet\" href=\"themes\{$CURRENT_THEME}\styles.css\">"));
        echo('</head>');
        echo("<body style=\"background-color:pink;\">");
        if (isAdmin())
        {
            echo("<div align=right>");
            echo("<h4 align=right>{$_SESSION["admin_username"]}</h4>");
            echo('<form align=right action="index.php" method="post">');
            echo('<button type="submit" name="logout" value="logout">Logout</button>');
            echo('</form>');
            echo("</div>");
        }
        else
        {
            echo('<div id="loginpanel" style="background-color:pick;display:none;">
                <form align=right action="index.php" method="post">
                    <label>username:</label><br>
                    <input type="text" name="login_user" value=""><br>
                    <label>password:</label><br>
                    <input type="text" name="login_pw" value=""><br>
                    <input type="submit" value="Login">
                </form>');
            echo("</div>");
            echo("<div align=right><button id=\"loginbutton\" align=right onclick=\"toggleAdminPanel()\">Login</button></div>");
            echo('
                <script>
                function toggleAdminPanel(element, color) {
                 if (document.getElementById("loginpanel").style.display == "none")
                 {
                        document.getElementById("loginpanel").style.display = "block";
                        document.querySelector("#loginbutton").innerText = "Close";
                 }
                 else
                 {
                        document.getElementById("loginpanel").style.display = "none";
                        document.querySelector("#loginbutton").innerText = "Login";
                 }
                }
                </script>
            ');
        }
        echo("<h1 align=center>{$FORUM_TITLE}</h1>");
        echo("<h1 align=center>{$CURRENT_THREAD} thread</h1>");
        //echo("</div>");
    }
    function print_message_input()
    {
        echo('<div style="background-color:pink;">
            <form action="index.php" method="post">
                <label>username:</label><br>');
        $username = (isset( $_SESSION["temp_username"]) ?  $_SESSION["temp_username"] : "Anonymous");
        echo("<input type=\"text\" name=\"username\" value=\"{$username}\"><br>");
        echo('<label>message:</label><br>
                <textarea name="message" rows="8" cols="80"></textarea><br>
                <input type="submit" value="Post">
            </form>
        </div>');
    }
    function print_thread_bar($threads)
    {
        echo "<h4 style=\"text-align: center;\">Threads</h4>";
        echo "<form method=\"post\" style=\"text-align: center;\">";
        foreach($threads as $t) 
            echo("<input type=\"submit\" value=\"$t\" name=\"thread\"\>");
        echo "</form>";
    }
    function print_autoreload_script()
    {
        echo('<script src="http://code.jquery.com/jquery-latest.js"></script>
        <script>
            $(document).ready(function(){
                setInterval(function() {
                    $("#messageboard").load("message.php");
                }, 5000);
            });
        </script>');
    }

    $threads = getThreads();
    $thread = getCurrentThread();
    print_header();
    print_thread_bar($threads);
    print_autoreload_script();
    print_message_input();
    echo('<div id="messageboard">');
    $thread = getCurrentThread();
    $page = print_pages($thread);
    printMessages($thread, $page);
    echo('</div>');


    function filterName()
    {
        $key = isset($_POST["username"]) ? filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $key = sanitiseusername($key);
        return $key;
    }
    function filterMessage($key)
    {
        $key = isset($_POST[$key]) ? filter_input(INPUT_POST, $key, FILTER_DEFAULT) : null;
        $key = trim($key);
        $key = str_replace(array("\r", "\n"), '\\n', $key);
        if (strlen($key) > 1024)
            $key = substr($key, 0, 1024);
        return $key;
    }
    function sanitiseusername($name)
    {
        $name = isset($name) ? $name : "";
        $name = str_replace(array("\r", "\n", "|", '|'), '', $name);
        $name = trim($name);
        $name = str_replace("|", "", $name);
        $name = ($name != "") ? $name : "Anonymous";
        return $name;
    }
    function appendmessage(string $thread, string $name, string $msg)
    {
        
        $log = fopen(getThreadFile($thread), "a");
        $id = pack("i",time());
        $timePacked = pack("i", time());
        $ip = getIp();
        fwrite($log, "{$id}{$timePacked}{$name}|{$ip}|{$msg}\n");
        fclose($log);
        //printMessage($name, $msg);
    }
    function deleteMessage($thread, $id)
    {
        $newTextDoc = "";
        $log = fopen(getThreadFile($thread), "r");
        while(!feof($log)) {
            $msglog = trim(fgets($log));
            if ($msglog == "")
                continue;
            $msg = unpackMessage($msglog);
            if (strval($msg[0]) == strval($id))
            {
                echo("Found msg to be deleted!");
                continue;
            }
            $newTextDoc = $newTextDoc . ($msglog . "\n");
        }
        fclose($log);
        $log = fopen(getThreadFile($thread), "w");
        fwrite($log, $newTextDoc);
        fclose($log);
        echo("Deleted message " . $id);
    }

    function attempt_login($user, $pw)
    {
        $log = fopen("admin.txt", "r");
        while(!feof($log)) {
            $msglog = trim(fgets($log));
            if ($msglog == "")
                continue;
            $args = explode("|", $msglog);
            if ($user == $args[0] and $pw == $args[1])
            {
                $_SESSION["admin_username"] = $user;
                echo("<meta http-equiv='refresh' content='1'>"); 
                return true;
            }
        }
        fclose($log);
        return false;
    }

    function checkDelete()
    {
        if(!isAdmin()) return;
        if (!isset($_POST["delete"]))
            return;
        $deletePacket = $_POST["delete"];
        unset($_POST["delete"]);
        if (!str_contains($deletePacket, "|"))
            return;
        $args = explode("|", $deletePacket);
        $deleteId = $args[0];
        $deleteThread = $args[1];
        echo($deleteId . "<br>");
        echo($deleteThread);
        deleteMessage($deleteThread, $deleteId);
        echo("<meta http-equiv='refresh' content='1'>"); 
    }
    function checkAdminLogin()
    {
        if ((!isset($_POST["login_user"])) or !isset($_POST["login_pw"]))
            return;
        $user = $_POST["login_user"];
        $pw = $_POST["login_pw"];
        unset($_POST["login_user"]);
        unset($_POST["login_pw"]);
        //echo("Attempting login..." . $user . " " . $pw);
        attempt_login($user, $pw);
    }
    function checkAdminLogout()
    {
        if (!isAdmin()) return;
        if (!isset($_POST["logout"]))
            return;
        unset($_POST["logout"]);
        unset($_SESSION["admin_username"]);
        echo("Logged out!");
        echo("<meta http-equiv='refresh' content='1'>"); 
    }
    if (!empty($_POST)) setcookie ("last_post", implode($_POST),time()+360000);
    if (!empty($_POST) and $_COOKIE['last_post'] == implode($_POST)) unset($_POST["message"]);



    $_SESSION["thread"] = $thread;
    checkAdminLogin();
    checkAdminLogout();
    checkDelete();
    //chmod("thread", 0755);
    if (!isset($_POST["message"]))
    {
        return;
    }
    $message = filterMessage("message");

    if (trim($message) == "")
    {
        echo "Empty message";
        return;
    }
    $username = filterName();
    setcookie("username", $username, time() + (86400 * 365), "/");
    $_SESSION["temp_username"] = $username;
    appendmessage($thread, $username, $message);
    echo("<meta http-equiv='refresh' content='1'>"); 
?>