<?php
        session_start();
        include("../config.php");
        include("../scripts/util.php");
        if (isBanned(getIp())) return;
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
            if (FORUM_SQL_ENABLED)
                return 1;
            $page = isset($_POST["page"]) ? filter_input(INPUT_POST, "page", FILTER_SANITIZE_NUMBER_INT) : 0;
            $numPages = getNumberOfPages($thread);
           
            if ($page > $numPages)
                $page = $numPages-1;
            if ($numPages == 1) return $page;
            echo("Page: {$page}<br>");
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
            echo("<div>");  
            $msgs = get_messages($thread);
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
			$banned = isBanned($ipAddr);
            $ipAddrCensored = substr(hash('crc32', $ipAddr),0,5);
			$tag = "";
			if ($banned)
				$tag .= '<span style="color:red;">[BANNED] </span>';
            $offset *= 20;
            $msg = str_replace(array("\\n", "\\r", "\n", "\r"), "<br>", $msg);
            echo("<div id=\"{$id}\" class = \"thread_message\" style=\"margin-left:{$offset}px; width:90%\">");
            echo("<h6 style=\"margin-top:0px; margin-bottom:0px;\">{$date}</h5>");
            echo("<h5 style=\"margin-top:0px; margin-bottom:0px;\">{$tag}{$name}({$ipAddrCensored}):</h5>");
            //echo("<h5>{$name}:</h5>");
            echo("<p style=\"margin-top:0px; margin-bottom:0px;\">{$msg}</p>");
            $thread = getCurrentThread();
            if (isAdmin())
            {
                //echo "<form method=\"post\" style=\"text-align: center;\">";
                echo("<button onclick=\"deleteMessage('$thread', '$id');\">Delete</button>");
				if (!$banned)
					echo("<button onclick=\"banMessage('$thread', '$id');\">Ban</button>");
				else
					echo("<button onclick=\"unbanMessage('$thread', '$id');\">Unban</button>");
                //echo "</form>";
            }
            echo("</div><br>");
        }
        $thread = getCurrentThread();
        $page = print_pages($thread);
        printMessages($thread, $page);
?>