<?php
include '../db_linker.php';

function check_if_pass_same($pass,$email){
    $link = linkToLC();

    $sql="SELECT `password` FROM `users` WHERE `email`=:email";
    $handle=$link->prepare($sql);
    $handle->execute(array('email'=>$email));
    $result = $handle->fetchAll(PDO::FETCH_COLUMN);
    if($result[0]==$pass) return true;
    return false;
}

function updatePass(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id']) && isset($_POST['pass'])) {
            $link = linkToLC();
            $pass = $_POST['pass'];
            $email = $_SESSION['email'];
            if (check_if_pass_same($pass,$email)) {
                return 'pass_exist';
            }
            $id = $_SESSION['id'];
            $sql="UPDATE `users` SET `password`=:pass WHERE `id`=:id";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'pass'=>$pass,
                'id'=>$id
            ));
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
echo updatePass();
?>