<?php
include '../db_linker.php';

function getUsers(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id'])){
            $link = linkToLC();
            $token = $_SESSION['token'];
            $sql="SELECT `name`,`token` FROM `users` WHERE reports<11 AND `token`!=:token";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'token'=>$token
            ));
            $result = $handle->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($result);
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return $e;
    }
}
echo getUsers();
?>