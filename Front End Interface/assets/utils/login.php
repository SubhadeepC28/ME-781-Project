<?php

include 'db_linker.php';

function login(){
    try {
        session_start();
        if(1
//            isset($_SESSION['bot']) && $_SESSION['bot']!=true &&
//            isset($_SESSION['action']) && $_SESSION['action']=='login'
        ) {
            if (isset($_POST['email']) && isset($_POST['pass'])) {
                $email = strtolower(trim($_POST['email']));
                $password = $_POST['pass'];
                $link = linkToLC();
                $sql = "SELECT `id`,`name` FROM `users` WHERE `email`=:email";
                $handle = $link->prepare($sql);
                $handle->execute(array(
                    'email' => $email,
                ));
                if (!$handle->rowCount()) return 'email_not_found';
                $sql = "SELECT `id`,`name`,`token` FROM `users` WHERE `email`=:email AND `password`=:password";
                $handle = $link->prepare($sql);
                $handle->execute(array(
                    'email' => $email,
                    'password' => $password,
                ));
                if (!$handle->rowCount()) return 'pass_wrong';
                $result = $handle->fetchAll(PDO::FETCH_ASSOC);
                $result = $result[0];
                $_SESSION['name'] = $result['name'];
                $_SESSION['token'] = $result['token'];
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $result['id'];
                $_SESSION['logged_in'] = true;
                $sql = "INSERT INTO `activity`(`user_id`) VALUES(:user_id)";
                $handle = $link->prepare($sql);
                $handle->execute(array(
                    'user_id' => $result['id'],
                ));
                return 'S';
            }
            else {
                return 'F';
            }
        }
        else{
            return 'bot_detected';
        }
    }
    catch(Exception $e){
        return "F";
    }
}
echo login();
?>