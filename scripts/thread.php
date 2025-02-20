
<?php
    $CURRENT_THREAD = getCurrentThread();
    echo("<h1 align=center class=\"thread_title\">{$CURRENT_THREAD} thread</h1>");
?>
<h4 class = "thread_menu_title" style="text-align: center;">Threads</h4>
<form method="post" style="text-align: center;">
<?php
        $threads = getThreads();
        foreach($threads as $t) 
            echo("<input type=\"submit\" class=\"thread_select_button\" value=\"$t\" name=\"thread\"\>");
?>
</form>

<br>
<div class="thread_message_inputbox">
            <form action="index.php" method="post">
                <label>Username:</label><br>
<?php
  $username = isset($_SESSION["temp_username"]) ? $_SESSION["temp_username"] : "Anonymous";
  echo('<input type="text" name="username" value="' . htmlspecialchars($username, ENT_QUOTES) . '" class="input_field"><br>');
?>

<label>Message:</label><br>
        <textarea name="message" id="messageInput" rows="6" cols="80" maxlength="1024" class="message_textarea" placeholder="Type your message..."></textarea>
        <input type="submit" value="Post" class="submit_button">
        <span id="charCounter" class="char_count">1024 characters remaining</span>
</form>
</div>


<script>
                const inputBox = document.getElementById("messageInput");
                const counter = document.getElementById("charCounter");
                const maxLength = inputBox.getAttribute("maxlength");

                inputBox.addEventListener("input", () => {
                    let remaining = maxLength - inputBox.value.length;
                    counter.textContent = `${remaining} characters remaining`;

                    if (remaining <= 20) {
                        counter.classList.add("warning");
                    } else {
                        counter.classList.remove("warning");
                    }
                });
</script>


<div id="messageboard" class="thread_board">
</div>

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
    $(document).ready(function(){
        setInterval(function() {
            $("#messageboard").load("message.php");
        }, 5000);
        $("#messageboard").load("message.php");
    });
</script>