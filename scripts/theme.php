<?php
        session_start();
        include("../config.php");
        include("util.php");
        if (isBanned(getIp())) return;
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