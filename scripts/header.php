<?php
    $FORUM_TITLE = FORUM_TITLE;
    $CURRENT_THEME = getCurrentTheme();
    echo("<title>{$FORUM_TITLE}</title>");
    echo( str_replace(array("{", "}"), "", "<link rel=\"stylesheet\" id=\"forum_theme_link\" href=\"themes\{$CURRENT_THEME}\styles.css\">"));
    echo("<link rel=\"stylesheet\" href=\"themes/core.css\">");
    echo('</head>');
    echo("<body>");
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
        echo('<div id="loginpanel" style="display:none;">
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
?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<div class="theme-menu" style="text-align: left; position: absolute;top: 0px;">
<form method="post" id="theme_input_form"> <!-- action="scripts/theme.php"> -->
<button type="submit" class="theme-option" style="width: 50px; height:50px;position: absolute;top: 0px;background-color: transparent;border: none; padding: 0;" value ="default" name="theme">🎨</button>
<script>
     $("#theme_input_form").submit(function(e) {
        e.preventDefault(); // prevent page refresh
        $.ajax({
            type: "POST",
            url: "scripts/theme.php",
            data: $(this).serialize(), // serialize form data
            success: function(data) {document.getElementById("forum_theme_link").setAttribute("href", data); },
            error: function() {
                // Error ...
            }
        });
    });
</script>
</form>
</div>
<?php
    echo("<h1 align=center class=\"forum_title\">{$FORUM_TITLE}</h1>");
?>