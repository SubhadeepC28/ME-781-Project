<?php

include '../db_linker.php';
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');

        $link = linkToLC();

        date_default_timezone_set("Asia/Kolkata");

        $t = time();

        $curr_timestamp = (date("Y-m-d H:i:s", $t));

    while (1) {
// 1 is always true, so repeat the while loop forever (aka event-loop)
        $type = 'group_';
        $sql = 'SELECT `id`,`token` FROM `groups`';
        $handle = $link->prepare($sql);
        $handle->execute();

        if ($handle->rowCount()) {

            $result = $handle->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0 ; $i < count($result) ; $i++){
                $token = $result[$i]['token'];
                $event=$type.$token;
                $id = $result[$i]['id'];
                $sql = "SELECT u.`name`,u.`email`, gc.`message`, gc.`timestamp` FROM `group_chat` gc
                    LEFT JOIN `groups` g ON g.id=gc.group_id
                    LEFT JOIN `users` u ON u.id=gc.from_id
                    WHERE (DATE_FORMAT(gc.`timestamp`,'%Y-%m-%d %H:%i:%s')>DATE_FORMAT('$curr_timestamp','%Y-%m-%d %H:%i:%s')) AND gc.group_id=:gid
                    ";
                $handle = $link->prepare($sql);
                $handle->execute(array('gid'=>$id));
                if ($handle->rowCount()) {

                    $result = json_encode($handle->fetchAll(PDO::FETCH_ASSOC));
                    $t = time();
                    $curr_timestamp = (date("Y-m-d H:i:s", $t));
                    echo "event: ".$event."\n",
                        'data: ' . $result, "\n\n";
                    while (ob_get_level() > 0) {
                        ob_end_flush();
                    }
                    flush();
                }
//                else{
//
//                    echo "event: debug\n",
//                    "data: {'token':'".$event."','timestamp':'".$curr_timestamp."','gid':'".$id."'}\n\n";
//                    while (ob_get_level() > 0) {
//                        ob_end_flush();
//                    }
//                    flush();
//                }
            }
        }

        $type = 'user_';
        $sql = 'SELECT `chat_id` FROM `chat_ids`';
        $handle = $link->prepare($sql);
        $handle->execute();
        if ($handle->rowCount()) {
            $result = $handle->fetchAll(PDO::FETCH_ASSOC);
            for($i = 0 ; $i < count($result) ; $i++){
                $chat_id = $result[$i]['chat_id'];
                $sql = "SELECT u.`name`,u.`email`,u.`token`, c.`message`, c.`timestamp` FROM `chat` c
                        LEFT JOIN `users` u ON u.id=c.from_id
                        WHERE (DATE_FORMAT(c.`timestamp`,'%Y-%m-%d %H:%i:%s')>DATE_FORMAT('$curr_timestamp','%Y-%m-%d %H:%i:%s')) AND c.chat_id=:cid
                        ";
                $handle = $link->prepare($sql);
                $handle->execute(array('cid'=>$chat_id));
                if ($handle->rowCount()) {
                    $result = $handle->fetchAll(PDO::FETCH_ASSOC);
                    for($j=0;$j<count($result);$j++){
                        $push = array();
                        $token = $result[$j]['token'];
                        $event=$type.$token;
                        array_push($push,$result[$j]);
                        $push = json_encode($push);
                        $t = time();
                        $curr_timestamp = (date("Y-m-d H:i:s", $t));
                        echo "event: ".$event."\n",
                            'data: ' . $push, "\n\n";
                        while (ob_get_level() > 0) {
                            ob_end_flush();
                        }
                        flush();
                        echo "event: debug\n",
                        "data: {'token':'".$event."','data':'".$push."'}\n\n";
                        while (ob_get_level() > 0) {
                            ob_end_flush();
                        }
                        flush();
                    }
                }
//                else{
//                    echo "event: debug\n",
//                    "data: {'token':'user','timestamp':'".$curr_timestamp."','cid':'id'}\n\n";
//                    while (ob_get_level() > 0) {
//                        ob_end_flush();
//                    }
//                    flush();
//                }
            }
        }


//         Send a simple message at random intervals.

//         flush the output buffer and send echoed messages to the browser

//         break the loop if the client aborted the connection (closed the page)

        if ( connection_aborted() ) {
//            session_start();
//            $id = $_SESSION['id'];
            $sql="INSERT INTO `activity`(`user_id`,`status`) VALUES(:user_id,0)";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'user_id'=>5,
            ));
            break;
        }

//         sleep for 1 second before running the loop again

        sleep(1);
//
    }

//    header('Content-Type: text/event-stream');
//    header('Cache-Control: no-cache');
//    echo "event: logged_in\n",
//        'data: {"status":"false"}', "\n\n";
//    while (ob_get_level() > 0) {
//        ob_end_flush();
//    }
