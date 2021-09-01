<?php
include ('dbc.php');
session_start();
$msg='';

if(!isset($_SESSION["user_id"])){
    header("location:login.php"); 

}else{
    $id = $_SESSION['user_id']; 
    $dataQuery = "SELECT * FROM users WHERE user_id=:id";
    $dataStatement = $dbc->prepare($dataQuery);
    $dataStatement->execute(array(":id"=>$id));
    $row = $dataStatement->fetch(PDO::FETCH_ASSOC);

    $email = $row['email'];
    $username = $row['userName'];
}

if(isset($_POST['resetpass'])){
    $id = $_SESSION['user_id'];
    $password1 = $_POST['pass'];
    $password2 = $_POST['confirm-pass'];
    $passwordHashed = password_hash($password2, PASSWORD_DEFAULT);

    $uppercase = preg_match('@[A-Z]@', $password1);
    $lowercase = preg_match('@[a-z]@', $password1); 
    $number    = preg_match('@[0-9]@', $password1);
    if(!$uppercase || !$lowercase || !$number || strlen($password2) < 8) {
        echo "<br>Password should have at least 8 characters in length and should include at least one upper case letter and one number.";
    }elseif($password1 != $password2){
        echo "<br>The passwords do not match!";
    }else{
        $query ="UPDATE users SET password=:upass WHERE user_id=:uid";
        $stmt = $dbc->prepare($query);
        $stmt->execute(array(":upass"=>$passwordHashed, ":uid"=>$id));

        echo "Password changed successfully!";
        header("refresh:5;");
    }
}elseif(isset($_POST['newusername'])){
    $id = $_SESSION['user_id'];
    $newName = $_POST['newUsername'];
    $username = get_user_name($id, $dbc);
    $uNameCheck = "SELECT * FROM users WHERE userName=:username";
    $statement1 = $dbc->prepare($uNameCheck);
    $statement1->execute(array(':username'=> $newName));
    $rowcount = $statement1->rowCount();
    if($rowcount == 0){
        $query ="UPDATE users SET userName=:uname WHERE user_id=:uid";
        $stmt = $dbc->prepare($query);
        $stmt->execute(array(":uname"=>$newName, ":uid"=>$id));

        echo "Username changed successfully!";
        //header("refresh:5;");
    }elseif($rowcount > 0){
        echo "Sorry, the username is already used!";
    }
}/*elseif(isset($_POST['newemail'])){
    $id = $_SESSION['user_id'];
    $newEmail = $_POST['newEmail'];
    $username = get_user_name($id, $dbc);
    $uNameCheck = "SELECT * FROM users WHERE email=:email";
    $statement1 = $dbc->prepare($uNameCheck);
    $statement1->execute(array(':email'=> $newEmail));
    $rowcount = $statement1->rowCount();
    if($rowcount == 0){
        $query ="UPDATE users SET email=:email WHERE user_id=:uid";
        $stmt = $dbc->prepare($query);
        $stmt->execute(array(":email"=>$newEmail, ":uid"=>$id));
        echo "One last step confirm the email adress!";
        header("refresh:5;");
    }else{
        echo "Email is being used by another account please try different email adress!";
    }
}*/
?>
<html>
    <head>
        <title>Account</title>
        <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>

    </head>
    <body>
    <div align="center" class="menu">
        <h1>SPECIAL SOCIAL</h1>
        <h3>Account</h3>
        
        <ul>
            <li><a href="./home.php">Home</a></li>
            <li><a href="./newgroup.php">New group</a></li>
            <li ><a class="active" href="./account.php">Account</a></li>
            <li style="float: right;"><a href="./logout.php">Logout</a></li>
        </ul>
    </div>
    <table align="center"class="udaje">
        <tr>
            <th>Username:</th>
            <td><?php echo $username;?></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><?php echo $email ?></td>
        </tr>
    </table>
    <p class="forms">
        <details>
            <summary>Change the password</summary>
            <form method="POST">
            <br><input type="password" name="pass" placeholder="Password">
            <br><input type="password" name="confirm-pass" placeholder="Confirm password">
            <br><button type="submit" name="resetpass">Change</button>
            </form>
        </details>
        <details>
            <summary>Change the username</summary>
            <form method="POST">
            <br><input type="text" name="newUsername" placeholder="New username">
            <br><button type="submit" name="newusername">Change</button>
            </form>        
        </details>
        <!--<details>
            <summary>To change email adress *CLICK*</summary>
            <form method="POST">
            <br><input type="email" name="newEmail" placeholder="New email">
            <br><button type="submit" name="newemail">Change</button>
            </form>        
        </details>-->
</p>
    </body>
</html>
<style>    
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap');

th{
    color: #6fe0d1;;
}
body{
    background-color:  #2b3146;
    font-family: "Montserrat", sans-serif;
    font-size: 16px;
    text-align:center;
    color: white;
}
.udaje{
    color:white;
    text-align: left;
    padding: 20px;
}
.forms{
    color:white;

}
input{
    width: 300px;
    height: 35px;
    border-radius: 5px;
    text-align: center;
    background-color: #f45b69;
    margin: 5px;
    color: #fff;
    border: none;
}
button {
    color: #fff;
    font-size: 16px;
    margin-left: 10px;
    padding: 10px ;
    border-radius: 5px;
    background-color: #6fe0d1;
    text-decoration: none;
    text-align: center;
    float: center;
    border: none;
}
button:hover {
    background-color: #ff616f;
    border: none;
}

.menu ul {
    display: inline-block;
    list-style-type: none;
    padding: 0;
    background-color:rgba(69, 105, 144, 0.73);
    top: 75;
    width: 80%;
    border: 20px #232f5a;
    border-radius: 15px 15px 15px 15px;
}

.menu li {
    float: left;
}

.menu li a {
    display: inline-block;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

.menu li a:hover:not(.active) {
    background-color: #6fe0d1;
    border-radius: 15px 15px 15px 15px;
}

.menu .active {
    background-color: #f45b69;
    border-radius: 15px 15px 15px 15px;

}
</style>