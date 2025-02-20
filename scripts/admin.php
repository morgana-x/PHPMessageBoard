<?php
    function checkAdminLogin()
    {
        if ((!isset($_POST["login_user"])) or !isset($_POST["login_pw"]))
            return;
        $user = $_POST["login_user"];
        $pw = $_POST["login_pw"];
        unset($_POST["login_user"]);
        unset($_POST["login_pw"]);
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
    checkAdminLogin();
    checkAdminLogout();
    checkDelete();
?>