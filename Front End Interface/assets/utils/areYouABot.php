<?php
include "db_config.php";
function areYouABot(){
    try {
        session_start();
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])){
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = RECAPTCHA_SECRET;
            $recaptcha_response = $_POST['token'];

            // Make and decode POST request:
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);

            // Take action based on the score returned:
            session_start();
            if ($recaptcha->success) {
                if ($recaptcha->score >= 0.5) {
                    $_SESSION['bot']=false;
                    $_SESSION['action']=$recaptcha->action;
                    $_SESSION['host']=$recaptcha->hostname;
                    return 'S';
                } else {
                    $_SESSION['bot']=true;
                    return 'F';
                }
            }
            else{
                $_SESSION['bot']=true;
                return 'F';
            }
        }
    }
    catch(Exception $e){
        return "F";
    }
}
echo areYouABot();
?>