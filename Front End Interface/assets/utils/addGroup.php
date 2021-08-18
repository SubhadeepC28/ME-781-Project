<?php
include 'db_linker.php';
include 'token.php';


function check_if_group_exist($name){
    $link = linkToLC();
    $sql="SELECT `id` FROM `groups` WHERE `name`=:name";
    $handle=$link->prepare($sql);
    $handle->execute(array('name'=>$name));
    $result = $handle->fetchAll(PDO::FETCH_COLUMN);
    if(count($result)) return true;
    return false;
}

function fetch_existing_tokens(){
    $link = linkToLC();
    $sql="SELECT `token` FROM `groups`";
    $handle=$link->prepare($sql);
    $handle->execute();
    return $handle->fetchAll(PDO::FETCH_COLUMN);
}

function addGroup(){
    try {
        session_start();
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['id'])){
            $link = linkToLC();
            $name = $_POST['name'];
            if(check_if_group_exist($name)){
                return 'group_exist';
            }
            $token = getRandomToken(20);
            $existing_tokens = fetch_existing_tokens();
            while(array_search($token,$existing_tokens)) $token = getRandomToken(20);
            $admin = $_SESSION['id'];
            $sql="INSERT INTO `groups`(`name`, `admin`, `token`) VALUES (:name,:admin,:token)";
            $handle=$link->prepare($sql);
            $handle->execute(array(
                'name'=>$name,
                'admin'=>$admin,
                'token'=>$token
            ));
            return 'S';
        }
        else{
            return 'F';
        }
    }
    catch(Exception $e){
        return 'F';
    }
}
echo addGroup();
?>