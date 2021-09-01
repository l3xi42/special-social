<?php
if($_POST["leave"]="leave"){
    if($difference>=7){
        $DELETE = "DELETE FROM `group_users` WHERE (user_id=:uid AND key_id=:currentkeyid)";

        $deletearray = array(":uid"=>$userid, ":currentkeyid"=>$currentkeyid);
        $statement1 = $dbc->prepare($DELETE);
        if($statement1->execute($deletearray)){
            $to = get_email($userid, $dbc);
            $subject = 'Change of groups';
            $from = 'no-reply@special.social';
            $message = '<html><head><style>
            p{
                color:blue;
            }
                            
            #verify{
                color: brown;
            }             
            </style></head><body>';
            $message .= 'Hi '.get_user_name($userid, $dbc).',';
            $message .= '<br>as you wished,';
            $message .= '<br>you were deleted from the group '.get_group_name($keyid, $dbc).'.';
            $message .= '<br>Special Social';
            $message .= '</body></html>';

            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: '.$from."\r\n".
                        'Reply-To: '.$from."\r\n" .
                        'X-Mailer: PHP/' . phpversion();
    
            mail($to, $subject, $message, $headers);
        }else{
            echo "Sorry something went wrong!";
        } 
    }else{
       echo "In order to leave group you have to participate in it for at least one week!";
        }
}
?>