<?php

include('dbc.php');
session_start();

if(isset($_SESSION['user_id'])){
    header('location:home.php');
}
if (isset($_POST['Login'])) {
    $query = "SELECT * FROM users WHERE email= :email";

    $statement = $dbc->prepare($query);
    $statement->execute(array(':email' => $_POST["email_login"]));
    
    $count = $statement->rowCount();
    
    if($count <=0){
        echo "No user found using this email.";
    }
    $result = $statement->fetchAll();

        foreach($result as $row){
            if(!$row['verified'] =='1'){
                echo "The account hasn't been verified yet.";
            }else{
                if(password_verify($_POST["password_login"], $row["password"])){
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['userName'];
                $sub_query = "INSERT INTO login_details (user_id)
                                VALUES ('".$row['user_id']."')";
                $statement = $dbc->prepare($sub_query);
                $statement->execute();
                $_SESSION['login_details_id'] = $dbc->lastInsertId();
                
                header("location:home.php");
            }else{
                echo"Wrong Password!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <meta >
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
  <link href="https://fonts.googleapis.com/css?family=Asap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./login.css">
</head>
<body>
<form class="form" method="POST">
    <h3>Login</h3>
    <br><input name="email_login"  type="text" placeholder="Email" required>
    <input name="password_login" type="password" placeholder="Password" required> 
    <a class="nad" href="./forgotten-password.php">Forgotten password</a>
    <button name="Login" type="submit" value="Login" >Login</button>
    <a class = "pod" href="./sign_up.php">New account</a>
    <a class="vpravo" onclick="history.back();">Back</a>
</form>
</body>
</html>