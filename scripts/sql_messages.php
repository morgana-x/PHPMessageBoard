<?php
    if (!FORUM_SQL_ENABLED) return;
    /*
      $sql = "CREATE TABLE IF NOT EXISTS FORUM_MESSAGES (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        post_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        user_id INT,
        user_name VARCHAR(30) NOT NULL,
        user_ip VARCHAR(30),
        message_text VARCHAR(2048) NOT NULL
        )";
    */
    function get_messages_sql($thread, $page=0)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        $limit = PAGE_SIZE;
        $startindex = $page*PAGE_SIZE;
        $sql = "SELECT id, post_date, user_id, user_name, user_ip, message_text FROM FORUM_MESSAGES WHERE thread='{$thread}' ORDER BY post_date LIMIT {$limit}";
        $result = $conn->query($sql);
        $messages = array();
        while($result->num_rows > 0 && $row = $result->fetch_assoc()) {
            $msg = array(
                $row["id"], 
                $row["post_date"], 
                $row["user_name"], 
                $row["user_ip"], 
                $row["message_text"], 
                $row["user_id"]);
            array_push($messages, ($msg));
            //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
        }
        $conn->close();
        return $messages;
    }
    function delete_message_sql($thread, $id)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        $sql = "DELETE FROM FORUM_MESSAGES WHERE thread='{$thread}' AND id={$id}";
        $result = $conn->query($sql);
        $conn->close();
        return;
    }
    function send_message_sql($thread, $username, $ipAddr, $msg)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        $msg = str_replace("'", "''", $msg); // do for username
        $username = str_replace("'", "''", $username);
        $thread = str_replace("'", "''", $thread);
        $sql = "INSERT INTO FORUM_MESSAGES (thread, user_name, user_ip, message_text)VALUES ('{$thread}', '{$username}', '{$ipAddr}', '{$msg}')";
        $conn->query($sql);
        $conn->close();
    }
    function migrate_message_sql($thread, $username, $ipAddr, $msg, $date)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        $msg = str_replace("'", "''", $msg); // do for username
        $username = str_replace("'", "''", $username);
        $thread = str_replace("'", "''", $thread);
        $sql = "INSERT INTO FORUM_MESSAGES (thread, user_name, user_ip, message_text, post_date)VALUES ('{$thread}', '{$username}', '{$ipAddr}', '{$msg}', '$date')";
        $conn->query($sql);
        $conn->close();
    }
?>