<?php
    if (!FORUM_SQL_ENABLED) return;

    function isBanned_sql($ip)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        $sql = "SELECT user_ip FROM FORUM_BANS WHERE user_ip = '{$ip}'";
        $result = $conn->query($sql);
        $banned = (($result->num_rows) > 0);
        $conn->close();
        return $banned;
    }
    function banIP_sql($ip, $duration)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        $sql = "INSERT INTO FORUM_BANS (user_ip, expire_time)VALUES ('{$ip}', '{$duration}')";
        $conn->query($sql);
        $conn->close();
    }
    function getBanInfo($ip)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        $sql = "SELECT user_ip, expire_time, ban_date, user_id FROM FORUM_BANS WHERE user_ip = '{$ip}'";
        $result = $conn->query($sql);
        $banned = (($result->num_rows) > 0);
    
        $conn->close();
        if (!$banned) return null;
        $row = $result->fetch_assoc();
        //print_r($row);
        return $row;
    }
    function checkBanExpire($ip)
    {
        if (!FORUM_SQL_ENABLED) return false;
        $banInfo = getBanInfo($ip);
        if ($banInfo == null) return true;
        //echo($banInfo["ban_date"]);
        //echo($banInfo["expire_time"]);
        if (time() > strtotime($banInfo["ban_date"]) + $banInfo["expire_time"])
        {
            unbanIP_sql($ip);
            return true;
        }
        return false;
    }
    function unbanIP_sql($ip)
    {
        $conn = new mysqli(
            FORUM_SQL_SERVERNAME, 
            FORUM_SQL_USERNAME,
             FORUM_SQL_PASSWORD, "MSGBOARD");
        //$sql = "INSERT INTO FORUM_BANS (user_ip, expire_time)VALUES ('{$ip}', '{$duration}')";
        $sql = "DELETE FROM FORUM_BANS WHERE user_ip='{$ip}'";
        $conn->query($sql);
        $conn->close();
    }
?>