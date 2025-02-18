<?php
    define("FORUM_TITLE", "Cool Messaging Board :3");
    
    define("PAGE_SIZE", 50);

    session_start();
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

    
    $threads = getThreads();
    $thread = getCurrentThread();
    function print_header()
    {
        $FORUM_TITLE = FORUM_TITLE;
        $CURRENT_THREAD = getCurrentThread();
        echo('<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">
            <title>Message Board</title>
            <style TYPE="text/css">
                body,tr,td,th,input,textarea { font-family: "Comic Sans MS", cursive, sans-serif; }
                textarea {background-color:rgb(200, 200, 200)}

            </style>
        </head>');
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
        echo("</body>");
    }
    function print_message_input()
    {
        echo('<body style="background-color:pink;">
            <form action="index.php" method="post">
                <label>username:</label><br>
                <input type="text" name="username" value="Anonymous"><br>
                <label>message:</label><br>
                <textarea name="message" rows="8" cols="80"></textarea><br>
                <input type="submit" value="Post">
            </form>
        </body>');
    }
    function print_thread_bar($threads)
    {
        echo "<h4 style=\"text-align: center;\">Threads</h4>";
        echo "<form method=\"post\" style=\"text-align: center;\">";
        foreach($threads as $t) 
            echo("<input type=\"submit\" value=\"$t\" name=\"thread\"\>");
        echo "</form>";
    }
    function print_pages($thread)
    {
        $page = isset($_POST["page"]) ? filter_input(INPUT_POST, "page", FILTER_SANITIZE_NUMBER_INT) : 0;
        $numPages = getNumberOfPages($thread);
       
        if ($page > $numPages)
            $page = $numPages-1;
        echo("Page: {$page}<br>");
        if ($numPages == 1) return $page;
        echo "<form method=\"post\">";
        for ($i =0; $i<15;$i++)
        {  
            $p = $page - 5 + $i;
            if ($p < 0) continue;
            if ($p >= $numPages) break;
            echo("<input type=\"submit\" value=\"$p\" name=\"page\"\>");
        }
        echo "</form>";
        return $page;
    }

    print_header();
    print_thread_bar($threads);
    print_message_input();
    $page = print_pages($thread);

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
    function getThreadFile(string $thread)
    {
        return "threads/{$thread}.txt";
    }
    function appendmessage(string $thread, string $name, string $msg)
    {
        
        $log = fopen(getThreadFile($thread), "a");
        $id = pack("i",time());
        $timePacked = pack("i", time());
        $ip = getIp();
        fwrite($log, "{$id}|{$name}|{$ip}|{$timePacked}|{$msg}\n");
        fclose($log);
        //printMessage($name, $msg);
    }
    function unpackMessage($msg)
    {
        $lastSepPos = 0;
        $array = explode("|", $msg, 5);
        $time = unpack("i", $array[3])[1];
        $array[0] = unpack("i", $array[0])[1];
        $array[3] = Date("Y-m-d g:i a", (int)$time);
        return $array;
    }
    function getIp() {
        if ($_SERVER['REMOTE_ADDR'] == "::1") return "127.0.0.1";
        $ip = getenv('HTTP_CLIENT_IP')
            ?: getenv('HTTP_X_FORWARDED_FOR')
            ?: getenv('HTTP_X_FORWARDED')
            ?: getenv('HTTP_FORWARDED_FOR')
            ?: getenv('HTTP_FORWARDED')
            ?: getenv('REMOTE_ADDR');   
        return $ip;
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
    function isAdmin()
    {
        return isset($_SESSION["admin_username"]);
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
    function printMessage($msg, int $offset=1)//$name, $msg, int $offset=1)
    {
        $id = $msg[0];
        $name = $msg[1];
        $ipAddr = $msg[2];
        try
        {
            $ipOctets = explode('.', $ipAddr);
            $ipAddr = "";
            $count = (count($ipOctets) <= 3 ? count($ipOctets) : 3);
            for ($i=0; $i<  $count; $i++)
                $ipAddr = $ipAddr . ( ($i<2? $ipOctets[$i] :  preg_replace('/./', '*', $ipOctets[$i])) . ($i!=$count-1 ? "." : "") );
        }
        catch(Exception $error)
        {

        }
        $date = $msg[3]; // date("Y/m/d g:i a", (int)$msg[2]);
        $msg = $msg[4];
        $offset *= 20;
        $msg = str_replace(array("\\n", "\\r"), "<br>", $msg);
        echo("<div id=\"{$id}\" style=\"background-color:pink; margin-left:{$offset}px; margin-bottom:0px; margin-top:0px; padding-top: 0px; padding-left: 5px;padding-bottom:5px;width:90%\">");
        echo("<h6>{$date}</h5>");
        echo("<h5>{$name}({$ipAddr}):</h5>");
        echo("<p>{$msg}</p>");
        $thread = getCurrentThread();
        $deletePacket = "{$id}|{$thread}";
        if (isAdmin())
        {
            echo "<form method=\"post\" style=\"text-align: center;\">";
            echo("<button type=\"submit\" value=\"{$deletePacket}\" name=\"delete\"\>Delete</button>");
            echo "</form>";
        }
        echo("</div><br>");
    }
    function printMessages(string $thread, $page=0)
    {
        $log = fopen(getThreadFile($thread), "r");
        echo("<div style=\"background-color:powderblue; margin-top:10px; padding-top: 5px; padding-bottom: 0px; width: 95%; margin-bottom:0px;\">");
        $msgs = array();
        $numberOfItems = 0;
        while(!feof($log)) {
            $msglog = trim(fgets($log));
            if ($msglog == "")
                continue;
            $numberOfItems++;
            $msgs[$numberOfItems-1] = unpackMessage($msglog);
        }
        if (count($msgs) == 0) return;
        $numberOfItems = 0;
        $startIndex = count($msgs)-1;
        $startIndex = $startIndex > ($page*PAGE_SIZE) ? $startIndex -= $page*PAGE_SIZE : $startIndex;
        for ($i = $startIndex; $i>=0; $i--)
        {
            $numberOfItems++;
            if ($numberOfItems >= PAGE_SIZE) break;
            printMessage($msgs[$i]);
        }
        echo("</div>");
    }
    function getNumberOfPages($thread)
    {
        $linecount = 0; 
        $handleFile = fopen(getThreadFile($thread), "r"); 
        while(!feof($handleFile)){ 
            $line = fgets($handleFile, 4096);
            $linecount = $linecount + substr_count($line, PHP_EOL); 
        } 
        fclose($handleFile); 
        return floor($linecount/PAGE_SIZE)+1;
    }
    function getThreads()
    {
        $tempthreads = array();
        $threads_file = fopen("threads.txt", "r");
        $i=0;
        while(!feof($threads_file)) {
            $t = fgets($threads_file);
            $t = trim($t);
            if ($t == "") continue;
            $tempthreads[$i] = $t;
            $i++;
        }
        fclose($threads_file);
        return $tempthreads;
    }
    function getCurrentThread()
    {
        $threads = getThreads();
        $thread = isset($_SESSION["thread"]) ? $_SESSION["thread"] : $threads[0];
        if (isset($_POST["thread"]))// : $threads[0];
        {
            $thread= filter_input(INPUT_POST, "thread", FILTER_SANITIZE_SPECIAL_CHARS);
            unset($_POST["thread"]);
            if (!in_array($thread, $threads))
                $thread = $threads[0];
            $_SESSION["thread"] = $thread;
        }
        return $thread;
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
    printMessages($thread, $page);
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
    appendmessage($thread, $username, $message);
    echo("<meta http-equiv='refresh' content='1'>"); 
?>