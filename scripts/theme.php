<?php
        session_start();
        include("../config.php");
        include("util.php");
        if (isBanned(getIp())) return;
        function getThemes()
        {
            $themes = scandir("../themes");
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
            /*if (!isset($_POST["theme"])) return;
            unset($_POST["theme"]);*/
            $currentTheme = getCurrentTheme();
            $themes = getThemes();
            $index = (in_array($currentTheme, $themes) ? array_search($currentTheme, $themes) : 0) + 1;
            if ($index >= count($themes))
                $index = 0;
            $_SESSION["theme"] = $themes[$index];
        }
        checkThemeSet();
        echo( "themes/". $_SESSION["theme"] . "/styles.css"); 
?>