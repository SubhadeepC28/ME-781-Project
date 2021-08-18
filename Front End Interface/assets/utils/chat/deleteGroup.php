<?php
include '../db_linker.php';

function deleteGroup(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id'])){
            $link = linkToLC();
            $token = $_POST['token'];
            $sql = "SELECT * FROM `groups` WHERE `token`=:token AND `admin`=:id LIMIT 1";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'token'=>$token,
                'id'=>$_SESSION['id']
            ));
            $rowcount = $handle->rowCount();
            if($rowcount<=0) return 'F';
            $result = $handle->fetchAll(PDO::FETCH_ASSOC);
            $gid = $result[0]['id'];
            $sql = "DELETE FROM `groups` WHERE `id`=:gid";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'gid'=>$gid
            ));
            $sql = "DELETE FROM `group_chat` WHERE `group_id`=:gid";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'gid'=>$gid
            ));
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return 'F';
    }
}
echo deleteGroup();
?>