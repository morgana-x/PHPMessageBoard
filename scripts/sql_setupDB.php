<?php
    $conn = new mysqli(
        FORUM_SQL_SERVERNAME, 
        FORUM_SQL_USERNAME,
         FORUM_SQL_PASSWORD);
     //   'msgboard_messages', 
     //   3306); // you can omit the last argument
    $conn->set_charset('utf8mb4'); // always set the charset
    $conn->query("CREATE DATABASE IF NOT EXISTS MSGBOARD");
    $conn->close();
    $conn = new mysqli(
        FORUM_SQL_SERVERNAME, 
        FORUM_SQL_USERNAME,
         FORUM_SQL_PASSWORD, "MSGBOARD");

    /*
    $id_text = substr($msg, 0, 4);
            $date_text = substr($msg, 4, 8);
            $msg_data = substr($msg, 8, strlen($msg));
            $otherarray = explode("|", $msg_data, 3);
            $array = array(unpack("i", $id_text)[1],  Date("Y-m-d g:i a", (int)unpack("i", $date_text)[1]), $otherarray[0], $otherarray[1], $otherarray[2]);
  
    */
    /*
          $id = $msg[0];
            $date = $msg[1]; // date("Y/m/d g:i a", (int)$msg[2]);
            $name = $msg[2];
            $ipAddr = $msg[3];
            $msg = $msg[4];
    */
    $sql = "CREATE TABLE IF NOT EXISTS FORUM_MESSAGES (
        thread VARCHAR(30),
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        post_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        user_id INT,
        user_name VARCHAR(30) NOT NULL,
        user_ip VARCHAR(30),
        message_text VARCHAR(2048) NOT NULL
        )";
    $conn->query($sql);
    $sql = "CREATE TABLE IF NOT EXISTS FORUM_ACCOUNTS (
        user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        user_name VARCHAR(30) NOT NULL,
        user_ip VARCHAR(30),
        user_rank INT
        )";
    $conn->query($sql);
    $sql = "CREATE TABLE IF NOT EXISTS FORUM_BANS (
            ban_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            expire_time INT,
            user_id INT,
            user_ip VARCHAR(30)
            )";
    $conn->query($sql);
    $conn->close();
?>