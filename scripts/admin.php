<?php
    session_start();
    include("../config.php");
    include("util.php");
    if (isBanned(getIp())) return;
    
    function checkAdminLogin()
    {
        if ((!isset($_POST["login_user"])) or !isset($_POST["login_pw"]))
            return;
        $user = $_POST["login_user"];
        $pw = $_POST["login_pw"];
        unset($_POST["login_user"]);
        unset($_POST["login_pw"]);
        attempt_login($user, $pw);
        echo("logged in");
    }
    function checkAdminLogout()
    {
        if (!isAdmin()) return;
        if (!isset($_POST["logout"]))
            return;
        unset($_POST["logout"]);
        unset($_SESSION["admin_username"]);
        echo("Logged out!");
        //echo("<meta http-equiv='refresh' content='1'>"); 
    }
    
    function attempt_login($user, $pw)
    {
        $log = fopen(ADMIN_FILE, "r");
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
    function deleteMessage($thread, $id)
    {
        $newTextDoc = "";
        $log = fopen(getThreadFile($thread), "r");
        while(!feof($log)) {
            $msglog = trim(fgets($log));
            if ($msglog == "")
                continue;
            $msg = unpackMessage($msglog);
            strval($msg[0]);
            if (strval($msg[0]) == strval($id))
                continue;
            $newTextDoc = $newTextDoc . ($msglog . "\n");
        }
        fclose($log);
        $log = fopen(getThreadFile($thread), "w");
        fwrite($log, $newTextDoc);
        fclose($log);
        echo("Deleted message " . $id);
    }
    function banMessagePoster($thread, $id)
    {
        $log = fopen(getThreadFile($thread), "r");
        while(!feof($log)) {
            $msglog = trim(fgets($log));
            if ($msglog == "")
                continue;
            $msg = unpackMessage($msglog);
            strval($msg[0]);
            if (strval($msg[0]) != strval($id))
                continue;
            banIP($msg[3]);
            break;
        }
        fclose($log);
        deleteMessage($thread, $id);
    }


    function checkDelete()
    {
        if(!isAdmin()) return;
        if ((!isset($_POST["del_msg_thread"])) or !isset($_POST["del_msg_id"]))
            return;
        echo("Check delete!2");
        $deleteId = $_POST["del_msg_id"];
        $deleteThread = $_POST["del_msg_thread"];
        unset($_POST["del_msg_thread"]);
        unset($_POST["del_msg_id"]);
        echo($deleteId . "<br>");
        echo($deleteThread);
        deleteMessage($deleteThread, $deleteId);
    }
    function checkBan()
    {
        if(!isAdmin()) return;
        if ((!isset($_POST["ban_msg_thread"])) or !isset($_POST["ban_msg_id"]))
            return;
        echo("Check ban!2");
        $deleteId = $_POST["ban_msg_id"];
        $deleteThread = $_POST["ban_msg_thread"];
        unset($_POST["ban_msg_thread"]);
        unset($_POST["ban_msg_id"]);
        echo($deleteId . "<br>");
        echo($deleteThread);
        banMessagePoster($deleteThread, $deleteId);
    }
    checkAdminLogin();
    checkAdminLogout();
    checkDelete();
    checkBan();
?>