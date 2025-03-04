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
        echo('<script>function logout()
            {
                  $.ajax({
                    type: "POST",
                    url: "api/admin.php",
                    data: {"logout":"true"}, // serialize form data
                    success: function(data) {
                        // Success ...
                        location.reload()
                    },
                    error: function() {
                        // Error ...
                    }
                });
            }</script>');
        echo("<div align=right>");
        echo("<h4 align=right>{$_SESSION["admin_username"]}</h4>");
        //echo('<form align=right action="scripts/admin.php" method="post">');
       // echo('<button type="submit" name="logout" value="logout">Logout</button>');
        echo('<button onclick="logout()">Logout</button>');
        //echo('</form>');
        echo("</div>");
    }
    else
    {
        echo("<div align=right><button id=\"loginbutton\" align=right onclick=\"toggleAdminPanel()\">Login</button></div>");
        echo('<div id="loginpanel" style="display:none;" align=right>');
          //  <form align=right action="scripts/admin.php" method="post">
          //login(document.getElementById("login_user_input").value,document.getElementById("login_pw_input").value);
        echo('<label>username:</label><br>
                <input type="text" name="login_user" value="" id="login_user_input"><br>
                <label>password:</label><br>
                <input type="text" name="login_pw" value="" id="login_pw_input"><br>
                <button onclick="login(document.getElementById(`login_user_input`).value,document.getElementById(`login_pw_input`).value);">Log in</button>'); //          <input type="submit" value="Login">');
          //  </form>');
        echo("</div>");
        echo('<script>
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
            function login(user,pw)
            {
                $.ajax({
                    type: "POST",
                    url: "api/admin.php",
                    data: {"login_user":user, "login_pw":pw}, // serialize form data
                    success: function(data) {
                        console.log(data);
                        // Success ...
                        if (data == "logged in")
                            location.reload()
                        
                    },
                    error: function() {
                        // Error ...
                    }
                });
            }
            </script>
        ');
    }
?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<div class="theme-menu" style="text-align: left; position: absolute;top: 0px;">
<button type="submit" class="theme-option" style="width: 50px; height:50px;position: absolute;top: 0px;background-color: transparent;border: none;" value ="default" name="theme" id="theme_input_button">🎨</button>


<script>
    var lastData = "";
    function refreshMessages()
    {
        $.ajax({
            type: "POST",
            url: "api/message_get.php",
            processData: false,
            cache: false,
            contentType: false,
            data: {},// $(this).serialize(), // serialize form data
            success: function(data) {
                // Success ...
                if (lastData == data)
                    return;
                lastData = data;
                document.getElementById("messageboard").innerHTML = data;
                //$("#messageboard").load("scripts/get_messages.php");
                //$("thread_menu_title").load(scripts/)
    
            },
            error: function() {
                // Error ...
            }
        });
    }
     document.getElementById("theme_input_button").onclick = function() {
        $.ajax({
            type: "POST",
            url: "api/theme.php",
            data:  {"theme": "main"}, // serialize form data
            success: function(data) {document.getElementById("forum_theme_link").setAttribute("href", data);},
            error: function() {
                // Error ...
            }
        });
    };
</script>
</div>
<?php
    echo("<h1 align=center class=\"forum_title\">{$FORUM_TITLE}</h1>");
?>