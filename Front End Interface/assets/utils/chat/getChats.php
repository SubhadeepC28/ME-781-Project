<?php
include '../db_linker.php';

function getChats(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id'])){
            $link = linkToLC();
            $token = $_POST['token'];
            $type = $_POST['type'];
            if($type=='user') {
                $sql = "SELECT `id` FROM `users` WHERE `token`=:token";
                $handle=$link->prepare($sql);
                $handle->execute(array(
                    'token'=>$token
                ));
                $from_id = $handle->fetchAll(PDO::FETCH_ASSOC);
                $from_id = $from_id[0]['id'];
                $to_id = $_SESSION['id'];
                $chat_id = (int)$from_id>(int)$to_id?$to_id.'_'.$from_id:$from_id.'_'.$to_id;
                $sql = "SELECT u.`name`, u.`email`, u.`token` as from_token, c.`message`, c.`timestamp` FROM `chat` c
                        LEFT JOIN `users` u ON (u.id=c.from_id)
                        WHERE c.chat_id = :chat_id";
                $handle=$link->prepare($sql);
                $handle->execute(array(
                    'chat_id'=>$chat_id
                ));
            }
            else {
                $sql = "SELECT gc.`message`,gc.`timestamp`,u.`name`,u.`email`,u.`token` as from_token FROM `group_chat` gc
                        LEFT JOIN `groups` g ON g.id=gc.group_id
                        LEFT JOIN `users` u ON gc.from_id=u.id
                         WHERE g.token=:token AND u.reports<11";
                $handle=$link->prepare($sql);
                $handle->execute(array(
                    'token'=>$token
                ));
            }
            $result = $handle->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($result);
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return 'F';
    }
}
echo getChats();
?>