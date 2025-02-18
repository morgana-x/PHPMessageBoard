<?php

        define("PAGE_SIZE", 50);
        function getThreadFile(string $thread)
        {
            return "threads/{$thread}.txt";
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
            return $thread;
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
        function isAdmin()
        {
            return isset($_SESSION["admin_username"]);
        }
        function printMessage($msg, int $offset=1)//$name, $msg, int $offset=1)
        {
            $id = $msg[0];
            $date = $msg[1]; // date("Y/m/d g:i a", (int)$msg[2]);
            $name = $msg[2];
            $ipAddr = $msg[3];
            $msg = $msg[4];
            /*try
            {
                $ipOctets = explode('.', $ipAddr);
                $ipAddr = "";
                $count = (count($ipOctets) <= 3 ? count($ipOctets) : 3);
                for ($i=0; $i<  $count; $i++)
                {
                    $ipAddr = $ipAddr . ( ($i<2? $ipOctets[$i] :  preg_replace('/./', '*', $ipOctets[$i])) . ($i!=$count-1 ? "." : "") );
                    if ($i>2)
                        break;
                }
            }
            catch(Exception $error)
            {

            }*/
            $ipAddr = substr(hash('crc32', $ipAddr),0,5);

            $offset *= 20;
            $msg = str_replace(array("\\n", "\\r"), "<br>", $msg);
            echo("<div id=\"{$id}\" style=\"background-color:pink; margin-left:{$offset}px; margin-bottom:0px; margin-top:0px; padding-top: 0px; padding-left: 5px;padding-bottom:5px;width:90%\">");
            echo("<h6>{$date}</h5>");
            echo("<h5>{$name}({$ipAddr}):</h5>");
            //echo("<h5>{$name}:</h5>");
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


?>