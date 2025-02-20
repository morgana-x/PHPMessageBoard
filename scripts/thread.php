<?php
    $CURRENT_THREAD = getCurrentThread();
    echo("<h1 align=center class=\"thread_title\" id=\"current_thread_title\">{$CURRENT_THREAD} thread</h1>");
?>

<h4 class = "thread_menu_title" style="text-align: center;">Threads</h4>

<div id="thread_select_form" style="text-align: center;" method="post" >
    <script>
            function selectThread(thread)
            {
                $.ajax({
                    type: "POST",
                    url: "scripts/select_thread.php",
                    data: {"thread":thread}, // serialize form data
                    success: function(data) {
                        // Success ...
                        document.getElementById("messageInput").value = "";
                        document.getElementById("current_thread_title").innerText = data + " thread";
                        $("#messageboard").load("scripts/get_messages.php");
                        //$("thread_menu_title").load(scripts/)
                    },
                    error: function() {
                        // Error ...
                    }
                });
            }
            
        </script>
    <?php
            foreach(getThreads() as $t) 
            {
                $script = "selectThread('$t')";
                echo("<button class=\"thread_select_button\" value=\"$t\" name=\"thread\" onclick=\"$script\">$t</button>");
            }
    ?>
</div>

<br>
<div class="thread_message_inputbox">
            <form action="index.php" method="post" id="thread_message_input_forum">
                <label>Username:</label><br>
<?php
  $username = isset($_SESSION["temp_username"]) ? $_SESSION["temp_username"] : "Anonymous";
  echo('<input type="text" name="username" value="' . htmlspecialchars($username, ENT_QUOTES) . '" class="input_field"><br>');
?>

<label>Message:</label><br>
        <textarea name="message" id="messageInput" rows="6" cols="80" maxlength="1024" class="message_textarea" placeholder="Type your message..."></textarea>
        <input type="submit" value="Post" class="submit_button">
        <span id="charCounter" class="char_count">1024 characters remaining</span>


 <script>
    $("#thread_message_input_forum").submit(function(e) {
        e.preventDefault(); // prevent page refresh

        $.ajax({
            type: "POST",
            url: "scripts/send_message.php",
            data: $(this).serialize(), // serialize form data
            success: function(data) {
                // Success ...
                document.getElementById("messageInput").value = "";
                $("#messageboard").load("scripts/get_messages.php");
                //$("thread_menu_title").load(scripts/)
            },
            error: function() {
                // Error ...
            }
        });
    });
</script>
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
<?php
if (isAdmin())
{
echo('
<script>
            function deleteMessage(thread, id)
            {
                $.ajax({
                    type: "POST",
                    url: "scripts/admin.php",
                    data: {"del_msg_thread":thread, "del_msg_id":id}, // serialize form data
                    success: function(data) {
                        // Success ...
                        $("#messageboard").load("scripts/get_messages.php");
                    },
                    error: function() {
                        // Error ...
                    }
                });
            }  
</script>'
);
}
?>
<div id="messageboard" class="thread_board">
</div>
<script>
    $(document).ready(function(){
        setInterval(function() {
            $("#messageboard").load("scripts/get_messages.php");
        }, 5000);
        $("#messageboard").load("scripts/get_messages.php");
    });
</script>