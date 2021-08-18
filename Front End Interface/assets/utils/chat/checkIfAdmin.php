<?php
include '../db_linker.php';

function checkIfAdmin(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id'])){
            $link = linkToLC();
            $token = $_POST['token'];
            $sql = "SELECT * FROM `groups` WHERE `token`=:token AND `admin`=:id";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'token'=>$token,
                'id'=>$_SESSION['id']
            ));
            $rowcount = $handle->rowCount();
            if($rowcount<=0) return 'F';
            else return 'S';
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return 'F';
    }
}
echo checkIfAdmin();
?>