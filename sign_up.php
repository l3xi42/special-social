<?php
include ('dbc.php');

if(isset($_SESSION['user_id'])){
 header('location:home-page.php');
}

if (isset($_POST['submit'])){

  $userName = trim($_POST["userName"]);
  $email = trim($_POST['email']);
  $password1 = trim($_POST['password']);
  $password2 = trim($_POST['confirm_password']);
  $vkey = md5(time().$userName);
  $passwordHashed = password_hash($password1, PASSWORD_DEFAULT);

  $uppercase = preg_match('@[A-Z]@', $password1);
  $lowercase = preg_match('@[a-z]@', $password1);
  $number    = preg_match('@[0-9]@', $password1);

  $uNameCheck = "SELECT userName FROM users WHERE userName=:username";
  $emailCheck = "SELECT email FROM users WHERE email=:email";

  $statement1 = $dbc->prepare($uNameCheck);
  $statement2 = $dbc->prepare($emailCheck);

  if($statement1->execute(array(':username'=> $userName))){
    if($statement1->rowCount() > 0){
      echo '<p><label>Username is already taken</label></p>';
    }elseif($statement2->execute(array(':email'=>$email))){
      if($statement2->rowCount() > 0){
        echo "<br>The email is already used, please use another one!";
      }elseif(!$uppercase || !$lowercase || !$number || strlen($password1) < 8) {
        echo "<br>Password should be at least 8 characters in length and should include at least one upper case letter and one number.";
      }elseif($password1 != $password2){
        echo "<br>The passwords do not match!";
      }else{
        $insert = "INSERT INTO users (userName, email, password, vkey)
                    VALUES (:username, :email, :password, :vkey)";
        $statement = $dbc->prepare($insert);
        $statement->execute(array(':password'=>$passwordHashed, ':username'=> $userName, ':email'=>$email, ':vkey'=>$vkey));

        $to = $email;
        $subject = 'Email verification';
        $from = 'no-reply@special.social';
                
        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            
        // Create email headers
        $headers .= 'From: '.$from."\r\n".
                    'Reply-To: '.$from."\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        $message = '<html><head><style>
        p{
         
          color:blue;
        }
                      
        #verify{
          color: brown;
        }             
        </style></head><body>';
        $message .= "<p>Hi $userName</p>";
        $message .= '<br>Thank your for signing up on our website.';
        $message .= '<br>To complete signing up please verify your email by clicking on the button below:';
        $message .= "<a href='https://special.social/verify.php?n=0&vkey=$vkey'>Verify here</a>";
        $message .= '<br>In Special Social we believe that anybody can find a group with the same interests.';
        $message .= '</body></html>';
               
        // Sending email
        if(mail($to, $subject, $message, $headers)){
          header('location:thankyou.php');
        }else{
          echo 'Unable to send email. Please try again.';
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
  <title>Sign up</title>
  <link href="https://fonts.googleapis.com/css?family=Asap" rel="stylesheet">
  <link rel="stylesheet" type="text/css"  href="sign_up.css">
  </head>
  <body>
      <form class="form" method="POST">
      <h3>Sign up</h3>
      <br><center><p style="color:white;">All the fields are required.</p></center>
        <input type="text" name="userName" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email"required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm password" required>
        <button name="submit" type="submit" value="Register" >Sign up</button>
        <a class="pod" href="./login.php">Already have an account?</a>
        <a class="vpravo" onclick="history.back();">Back</a>
      </form>
  </body>
</html>