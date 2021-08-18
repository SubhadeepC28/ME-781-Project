<?php
include '../db_linker.php';

function check_if_user_exist($email){
    $link = linkToLC();

    $sql="SELECT `email` FROM `users` WHERE `email`=:email";
    $handle=$link->prepare($sql);
    $handle->execute(array('email'=>$email));
    $result = $handle->fetchAll(PDO::FETCH_COLUMN);
    if(count($result)) return true;
    return false;
}

function updateDetails(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id']) && isset($_POST['name']) && isset($_POST['email'])) {
            $link = linkToLC();
            $email = strtolower(trim($_POST['email']));
            if ($_SESSION['email'] != $email){
                if (check_if_user_exist($email)) {
                    return 'user_exist';
                }
            }
            $name=$_POST['name'];
            $id = $_SESSION['id'];
            $sql="UPDATE `users` SET `name`=:name,`email`=:email WHERE `id`=:id";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'name'=>$name,
                'email'=>$email,
                'id'=>$id
            ));
            return 'S';
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return "F";
    }
}
echo updateDetails();
?>