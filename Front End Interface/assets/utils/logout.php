<?php
function Logout(){
    // Initialize the session.
    // If you are using session_name("something"), don't forget it now!
    session_start();

    if(isset($_SESSION['id'])){
        include 'db_linker.php';
        $link = linkToLC();
        $sql="INSERT INTO `activity`(`user_id`,`status`) VALUES(:user_id,0)";
        $handle=$link->prepare($sql);
        $handle->execute(array(
            'user_id'=>$_SESSION['id'],
        ));
    }

    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
    return true;
}
echo Logout();
?>