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

<form action="scripts/send_message.php" method="post" id="thread_message_input_forum" enctype="multipart/form-data">
<div class="thread_message_inputbox" id="thread_message_inputbox">
        <label>Username:</label><br>
        <?php
            $username = isset($_SESSION["temp_username"]) ? $_SESSION["temp_username"] : "Anonymous";
            echo('<input type="text" name="username" value="' . htmlspecialchars($username, ENT_QUOTES) . '" class="input_field" id="send_message_username_inp"><br>');
        ?>
        <label>Message:</label><br>
        <textarea name="message" id="messageInput" rows="6" cols="80" maxlength="1024" class="message_textarea" placeholder="Type your message..."></textarea>
        <input type="file" name="msg_attachment" id="msg_attachment"><br><br>
        <input type="submit" value="Post" class="submit_button">
        <span id="charCounter" class="char_count">1024 characters remaining</span>

        <script>
            //document.getElementById("thread_message_inputbox").style.height =  document.getElementById("messageInput").height;
            $("#thread_message_input_forum").submit(function(e) {
                /*if (document.getElementById("msg_attachment").value)
                    return;*/
                e.preventDefault(); // prevent page refresh
                if (document.getElementById("messageInput").value == "" && !document.getElementById("msg_attachment").value)
                    return;
                var data = new FormData();
                data.append("username",document.getElementById("send_message_username_inp").value);
                data.append("message",  document.getElementById("messageInput").value);
                data.append("file",$('#msg_attachment')[0].files[0]);//document.getElementById("msg_attachment").value;
                $.ajax({
                    type: "POST",
                    url: "scripts/send_message.php",
                    processData: false,
                    cache: false,
                    contentType: false,
                    data: data,// $(this).serialize(), // serialize form data
                    success: function(data) {
                        // Success ...
                        console.log(data);
                        if (data!="")
                            alert(data);
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
        </div>
</form>


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