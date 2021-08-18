<?php
include 'db_linker.php';

function getDetails(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id'])){
            $link = linkToLC();
            $id = $_SESSION['id'];
            $sql="SELECT `name`,`email` FROM `users` WHERE `id`=:id";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'id'=>$id
            ));
            $result = $handle->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($result[0]);
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return $e;
    }
}
echo getDetails();
?>