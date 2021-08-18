<?php
include 'db_linker.php';
include 'token.php';

function get_last_id(){
    $link = linkToLC();
    $sql="SELECT `id` FROM `users` ORDER BY `id` DESC LIMIT 1";
    $handle=$link->prepare($sql);
    $handle->execute();
    $result = $handle->fetchAll(PDO::FETCH_ASSOC);
    return $result[0]['id'];
}
function check_if_user_exist($email){
    $link = linkToLC();

    $sql="SELECT `email` FROM `users` WHERE `email`=:email";
    $handle=$link->prepare($sql);
    $handle->execute(array('email'=>$email));
    $result = $handle->fetchAll(PDO::FETCH_COLUMN);
    if(count($result)) return true;
    return false;
}
function fetch_existing_tokens(){
    $link = linkToLC();
    $sql="SELECT `token` FROM `users`";
    $handle=$link->prepare($sql);
    $handle->execute();
    return $handle->fetchAll(PDO::FETCH_COLUMN);
}
function register(){
    try {
        session_start();
        if(1
//            isset($_SESSION['bot']) && $_SESSION['bot']!=true && isset($_SESSION['action']) && $_SESSION['action']=='register'
        ) {
            if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['pass'])) {
                $link = linkToLC();
                $email = strtolower(trim($_POST['email']));
                if (check_if_user_exist($email)) {
                    return 'user_exist';
                }
                $name = $_POST['name'];
                $pass = $_POST['pass'];
                $token = getRandomToken(20);
                $existing_tokens = fetch_existing_tokens();
                while (array_search($token, $existing_tokens)) $token = getRandomToken(20);
                $sql = "INSERT INTO `users`(`name`, `email`, `password`, `token`) VALUES (:name,:email,:pass,:token)";
                $handle = $link->prepare($sql);
                $handle->execute(array(
                    'name' => $name,
                    'email' => $email,
                    'pass' => $pass,
                    'token' => $token
                ));
                $_SESSION['name'] = $name;
                $_SESSION['token'] = $token;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = get_last_id();
                $id = $_SESSION['id'];
                $_SESSION['logged_in'] = true;
                $sql = 'SELECT `id` FROM `users`';
                $handle = $link->prepare($sql);
                $handle->execute();
                if ($handle->rowCount() > 1) {
                    $result = $handle->fetchAll(PDO::FETCH_COLUMN);
                    for ($i = 0; $i < (count($result) - 1); $i++) {
                        $sql = "INSERT INTO `chat_ids` (`from_id`, `to_id`, `chat_id`) VALUES (:from_id,:to_id,:chat_id)";
                        $handle = $link->prepare($sql);
                        $handle->execute(array(
                            'from_id' => $result[$i],
                            'to_id' => $id,
                            'chat_id' => $result[$i] . '_' . $id
                        ));
                    }
                }
                return 'S';
            } else {
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
echo register();
?>