<?php
        function getThreadFile(string $thread)
        {
            return THREAD_FOLDER . "/{$thread}.txt";
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
        function unpackMessage($msg)
        {
            $id_text = substr($msg, 0, 4);
            $date_text = substr($msg, 4, 8);
            $msg_data = substr($msg, 8, strlen($msg));
            $otherarray = explode("|", $msg_data, 3);
            $array = array(unpack("i", $id_text)[1],  Date("Y-m-d g:i a", (int)unpack("i", $date_text)[1]), $otherarray[0], $otherarray[1], $otherarray[2]);
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
            if (!in_array($thread, $threads))
            {
                $thread = $threads[0];
                $_SESSION["thread"] = $thread;
            }
            return $thread;
        }
       
        function isAdmin()
        {
            return isset($_SESSION["admin_username"]);
        }
      
        function getThemes()
        {
            $themes = scandir("themes");
            $cleanThemes = array();
            for ($i =0; $i < count($themes); $i++)
            {
                if (str_replace(".", "", $themes[$i]) == "")
                    continue;
                if ($themes[$i] == "core.css")
                    continue;
                array_push($cleanThemes, $themes[$i]);
            }
            return $cleanThemes;
        }
        function getCurrentTheme()
        {
            return isset($_SESSION["theme"]) ? $_SESSION["theme"] : "default";
        }
        function checkThemeSet()
        {
            if (!isset($_POST["theme"])) return false;
            unset($_POST["theme"]);
            $currentTheme = getCurrentTheme();
            $themes = getThemes();
            $index = (in_array($currentTheme, $themes) ? array_search($currentTheme, $themes) : 0) + 1;
            if ($index >= count($themes))
                $index = 0;
            $_SESSION["theme"] = $themes[$index];
            echo("<meta http-equiv='refresh' content='1'>"); 
            return true;
        }
       // if (checkThemeSet()) { echo($_SESSION["theme"]); return;}
        
?>