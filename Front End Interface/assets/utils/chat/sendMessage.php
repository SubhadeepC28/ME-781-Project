<?php
include '../db_linker.php';

function send(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id']) && isset($_POST['target']) && isset($_POST['text']) && isset($_POST['timestamp']) && isset($_POST['type'])) {
            $link = linkToLC();
            $email = $_SESSION['email'];
            $target = $_POST['target'];
            $type = $_POST['type'];
            $text = $_POST['text'];
            $timestamp = $_POST['timestamp'];
            if($type == 'user'){
                $sql = "SELECT `id` FROM `users` WHERE `token`=:token";
                $handle=$link->prepare($sql);
                $handle->execute(array(
                    'token'=>$target
                ));
                $user_id = $handle->fetchAll(PDO::FETCH_ASSOC);
                $user_id = $user_id[0]['id'];
                $from_id = $_SESSION['id'];
                $chat_id = (int)$from_id>(int)$user_id?$user_id.'_'.$from_id:$from_id.'_'.$user_id;
                $sql="INSERT INTO `chat`(`from_id`, `user_id`, `chat_id`, `message`, `timestamp`) VALUES (:from_id,:user_id,:chat_id,:message,NOW())";
                $handle=$link->prepare($sql);
                $handle->execute(array(
                    'from_id'=>$from_id,
                    'user_id'=>$user_id,
                    'chat_id'=>$chat_id,
                    'message'=>$text,
                ));
            }
            else{
                $id = $_SESSION['id'];
                $sql = "SELECT `id` FROM `groups` WHERE `token`=:token";
                $handle=$link->prepare($sql);
                $handle->execute(array(
                    'token'=>$target
                ));
                $group_id = $handle->fetchAll(PDO::FETCH_ASSOC);
                $group_id = $group_id[0]['id'];
                $sql="INSERT INTO `group_chat`(`from_id`, `message`, `timestamp`, `group_id`) VALUES (:from_id,:message,NOW(),:group_id)";
                $handle=$link->prepare($sql);
                $handle->execute(array(
                    'from_id'=>$id,
                    'message'=>$text,
                    'group_id'=>$group_id
                ));
            }
            return 'S';
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return $e;
    }
}
echo send();
?>