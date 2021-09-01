<?php 
if(isset($_POST['send'])){
    $email = $_POST['email'];
    $msg = $_POST['message'];
    $subject = $_POST['subject'];
    if($subject == "feedback"){
        $to = 'feedback@special.social';
        $subject = 'Feedback';
        $from = 'feedback@special.social';
        $message = '<html><head><style>
        p{
            color:blue;
        }
                        
        #verify{
            color: brown;
        }             
        </style></head><body>';
        $message .= "Feedback from '.$email.',";
        $message .= "$msg";
        $message .= '<br>Special Social';
        $message .= '</body></html>';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: '.$from."\r\n".
                    'Reply-To: '.$from."\r\n" .
                    'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
        $error =  'Thank you for your feedback. We try to improve everyday.';

    }elseif($subject == "new-group"){
        $to = 'support@special.social';
        $subject = $subject;
        $from = 'feedback@special.social';
        $message = '<html><head><style>
        p{
            color:blue;
        }
        #verify{
            color: brown;
        }             
        </style></head><body>';
        $message .= "New hobby request from '.$email.',";
        $message .= "$msg";
        $message .= '<br>Special Social';
        $message .= '</body></html>';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: '.$from."\r\n".
                    'Reply-To: '.$from."\r\n" .
                    'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
        $error =  'Thank you. This hobby should be added in the next three days!';
    }
}
?>

<html>
<head>
    <title>Feedback/Support</title>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
</head>
<body>
    <form method="post">
        <?php if($error){echo $error;} ?>
        <h3>Feedback/Support</h3>
        <br><input type="email" name="email" placeholder="Your Email">
        <select class="subject" name="subject" placeholder="Subject">
            <option value="feedback">Feedback</option>
            <option value="new-hobby">New hobby</option>
            <option value="support">Support</option>
        </select>
        <textarea name="message" placeholder="Message"></textarea>
        <button name="send" type="submit">SEND</button>
        <a onclick="history.back();">Back</a>
    </form>
</body>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap');

form {
    overflow: hidden;
    background-color: #2b3146;
    padding: 40px 30px 30px 30px;
    border-radius: 10px;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 460px;
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
form > select{
    display: block;
    border-radius: 5px;
    font-size: 16px;
    background: white;
    width: 104%;
    border: 0;
    padding: 10px 10px;
    margin: 15px -10px;

}
form > textarea{
    display: block;
    border-radius: 5px;
    font-size: 16px;
    background: white;
    width: 104%;
    height: 100px;
    border: none;
    padding: 10px 10px;
    margin: 15px -10px;

}
form > button:hover {
  background-color: #6fe0d1;
}

h3{
    color: #6fe0d1;
    position: absolute;
    font-size: 30px;
    left: 24%;
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
  left: 88%;
  bottom: 20%;

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
@media only screen and (max-width: 600px){
form {
    overflow: hidden;
    background-color: #2b3146;
    padding: 40px 30px 30px;
    border-radius: 10px;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 300px;
    transform: translate(-50%, -50%);
    box-shadow: 5px 10px 10px rgba(2, 128, 144, 0.2);

}
form > input {
    width: 106%;
}
form > select{
    width: 106%;
}
form > textarea{
    width: 106%;
}
h3{
    color: #6fe0d1;
    position: absolute;
    font-size: 30px;
    left: 10%;
    top: 0px;
}
a{
  color: #fff;
  position: absolute;
  font-size: 14px;
  left: 82%;
  bottom: 20%;

}
}
</style>
</html>
