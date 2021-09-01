<?php
include ('dbc.php');
if(isset($_SESSION["user_id"])){
    header("location:account.php"); 
}

if(isset($_POST['send_email'])){
    $query1  = "SELECT user_id FROM password_reset WHERE email = :email";
    $statement1 = $dbc->prepare($query1);
    $email =filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $vkey = md5(uniqid(rand()));
   
    $statement1->execute(array(':email'=>$email));
    $row = $statement1->fetch(PDO::FETCH_ASSOC);
    if($statement1->rowCount()==1){
        $id0 = $row['user_id'];
        echo "You can only change password one time per day.";
    }elseif($statement1->rowCount()==0){
        $query3  = "SELECT user_id FROM users WHERE email=:email";
        $statement3 = $dbc->prepare($query3);
        $statement3->execute(array(":email"=>$email));
        $row3 = $statement3->fetch(PDO::FETCH_ASSOC);
        $id = $row3['user_id'];           

        if($statement3->rowCount()==1){
            $query4 = "INSERT INTO password_reset (user_id, email, vkey) VALUES(:user_id, :email, :vkey)";
            $statement4 = $dbc->prepare($query4);
            if($statement4->execute(array(":user_id"=>$id, ":email"=>$email, ":vkey"=>$vkey))){
                $to = $email;
                $subject = 'Password Reset';
                $from = 'no-reply@special.social';

                // To send HTML mail, the Content-type header must be set
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    
                // Create email headers
                $headers .= 'From: '.$from."\r\n".
                            'Reply-To: '.$from."\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                $message = '<html><head><style>
                name{color:blue;}
                #verify{color: brown;}             
                </style></head><body>';
                $message .= 'Dear <name>'.get_user_name($id, $dbc).'</name>,';
                $message .= '<br>To reset your password please click the link bellow:';
                $message .= "<br><a href='https://special.social/resetpassword.php?userid=$id&vkey=$vkey'>Reset password here</a>";
                $message .= '<br>Special Social';
                $message .= '</body></html>';            

                if(mail($to,$subject,$message,$headers)){
                    echo "We have sent an email to $email. Please click on the password reset link in the email to generate a new password.";
                }else{
                    echo "Sorry! Something went wrong.";
                }
            }
        }else{
            echo "Sorry! Wrong email address.";
        }
    }
}      
?>
<html>
    <head>
        <title>Reset your forgotten password</title>
        <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
    </head>
    <body>
        <form method="POST">
        <h3>Forgotten Password</h3>
            <br><input type="email" name="email" placeholder="Email" required>
            <button type="submit" name="send_email"> Send email </button>
            <a onclick="history.back();">Back</a>
        </form>
    </body>
</html>
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap');

form {
    overflow: hidden;
    background-color: #2b3146;
    margin: 10px;
    border: 20px;
    padding: 40px 30px 30px;
    border-radius: 10px;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 400px;
    transform: translate(-50%, -50%);
    box-shadow: 5px 10px 10px rgba(2, 128, 144, 0.2);
}
form > button {
  cursor: pointer;
  color: #fff;
  font-size: 14px;
  text-transform: uppercase;
  border: 0;
  padding: 10px 0;
  margin-top: 10px;
  margin-left: -12px;
  border-radius: 5px;
  background-color: #ff616f;
  width: 100px;
}

form > button:hover {
  background-color: #6fe0d1;
}

h3{
    color: #6fe0d1;
    position: absolute;
    font-size: 30px;
    left: 18%;
    top: 0px;
}

body{
    background-color: #6fe0d1;
    font-family: 'Montserrat', sans-serif;            
}
a{
    color: #fff;
    position: absolute;
    font-size: 14px;
    left: 85%;
    bottom: 35%;
}
form > input {
  display: block;
  border-radius: 5px;
  font-size: 16px;
  background: white;
  width: 104%;
  border: 0;
  padding: 10px 10px;
  margin: 15px -10px;
}
@media only screen and (max-width: 500px){
form {
    overflow: hidden;
    background-color: #2b3146;
    padding: 40px 30px 30px 30px;
    margin: 10px;
    border: 20px;
    border-radius: 10px;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 300px;
    height: 125px;
    transform: translate(-50%, -50%);
    box-shadow: 5px 10px 10px rgba(2, 128, 144, 0.2);

}

form > input {
  display: block;
  border-radius: 5px;
  font-size: 16px;
  background: white;
  width: 106%;
  border: 0;
  padding: 10px 10px;
  margin: 15px -10px;
}

h3{
    color: #6fe0d1;
    position: absolute;
    font-size: 30px;
    left: 7%;
    top: 0px;
}

a{
  color: #fff;
  position: absolute;
  font-size: 14px;
  left: 81%;
  bottom: 33%;

}
}
</style>