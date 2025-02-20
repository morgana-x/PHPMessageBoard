<?php
        session_start();
        include("../config.php");
        include("util.php");
        function filterMessage($key)
        {
            $key = isset($_POST[$key]) ? filter_input(INPUT_POST, $key, FILTER_DEFAULT) : null;
            $key = trim($key);
            $key = str_replace(array("\r", "\n"), '\\n', $key);
            if (strlen($key) > 1024)
                $key = substr($key, 0, 1024);
            return $key;
        }
        function filterName()
        {
            $name = isset($_POST["username"]) ? filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS) : null;
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

        if (!isset($_POST["message"]))
            return;
        $message = filterMessage("message");
        if (trim($message) == "")
            return;
        $thread = getCurrentThread();
        $username = filterName();
        // setcookie("username", $username, time() + (86400 * 365), "/");
        $_SESSION["temp_username"] = $username;
        appendmessage($thread, $username, $message);
        //echo("<meta http-equiv='refresh' content='1'>"); 
?>