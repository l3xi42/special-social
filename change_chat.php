<?php
include('dbc.php');
session_start();

$currentkeyid = $_REQUEST['to_key_id'];
$userid = $_SESSION['user_id'];
echo "$currentkeyid, $userid";

$now_date = date("Y-m-d\TH:i:sP");
$user_date = get_user_time($userid, $currentkeyid, $dbc);
$date1=date_create($now_date);
$date2=date_create($user_date);
$diff=date_diff($date1,$date2);
$difference = $diff->format("%d");

$query = "SELECT * FROM `groups` WHERE key_id = :keyid";
$statement = $dbc->prepare($query);
$statement->execute(array(":keyid"=>$currentkeyid));
$row = $statement->fetchAll();

$g_hobby = $row['hobby'];
$g_level = $row['level'];
$g_lang = $row['lang'];
$g_place = $row['place'];

if(isset($_GET['vkey'])){
    $keyid = $_GET['vkey'];
    $userid = $_SESSION['userid'];
    $jop = 1;
    $query1  = "SELECT * FROM group_users WHERE key_id=:keyid AND user_id=:uid ";
    $statement1 = $dbc->prepare($query1);
    $statement1->execute(array(':keyid'=>$keyid, ":uid"=>$userid));
    $row = $statement1->fetch(PDO::FETCH_ASSOC);

    if($statement1->rowCount()==1){
        $setVerified=1;
        $query2 = "UPDATE group_users SET change=:jop WHERE key_id=:keyid AND user_id=:uid ";
        $stmt2 = $dbc->prepare($query2);
        $stmt2->execute(array(':keyid'=>$keyid, ":uid"=>$userid, ':jop'=>$jop));

        header("home.php");
    }else{
        echo "This account is invalid.";
    }
}elseif($_POST["leave"]="auto"){
    if($difference=28){
        // skupina
        $SELECT = "WITH POC_UID AS
        (   SELECT key_id,
            COUNT(DISTINCT(user_id)) AS POCET_UID 
            FROM `group_users`
            GROUP BY key_id
        )
        SELECT
                G.*,
                POCET_UID
            FROM `groups` G 
            LEFT JOIN POC_UID
                ON POC_UID.key_id = G.key_id
            WHERE G.hobby = :hobby
            AND G.level = :level
            AND G.language = :lang
            AND G.place = :place
            AND NOT G.key_id = :currentkeyid
            AND POCET_UID<4
            ORDER BY POCET_UID ASC
            LIMIT 1 ";
        $statement1 = $dbc->prepare($SELECT);
        $data = array(":hobby"=>$g_hobby, ":level"=>$g_level, ":lang"=>$g_lang,":place"=>$g_place, ":currentkeyid"=>$currentkeyid);
        $statement1->execute($data);
        $count = $statement1->rowCount();
        $row = $statement1->fetchAll();

        if($count > 0){
            $newKID = $row['key_id'];
            $userInsert = "INSERT INTO `group_users`(`user_id`, `key_id`, `hobby`) VALUES (:user_id, :keyid, :hobby)";
            $statement3 = $dbc->prepare($userInsert);   

            $DELETE = "DELETE FROM `group_users` WHERE (user_id=:uid AND key_id=:currentkeyid)";
            $deletearray = array(":uid"=>$userid, ":currentkeyid"=>$currentkeyid);
            $statement1 = $dbc->prepare($DELETE);

            if(($statement1->execute($deletearray))&&($statement3->execute(array(":user_id"=>$userid, ":keyid"=>$newKID, ":hobby"=>$g_hobby)))){
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
                $message .= '<p>Hi '.get_user_name($userid, $dbc).',</p>';
                $message .= '<br>the month has passed so far,';
                $message .= '<br>and you were switched in groups.';
                $message .= '<br>Special Social';
                $message .= '</body></html>';
                
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: '.$from."\r\n".
                            'Reply-To: '.$from."\r\n" .
                            'X-Mailer: PHP/' . phpversion();
        
                mail($to, $subject, $message, $headers);
            }
        }else{
            $SELECT = "WITH CAS_UID AS
            (   SELECT *, 
                DATEDIFF(CURRENT_TIMESTAMP, timestamp) AS datum 
                FROM `group_users`
            )
            SELECT
                    G.*,
                    datum
                FROM `groups` G 
                LEFT JOIN CAS_UID
                    ON CAS_UID.key_id = G.key_id
                WHERE G.hobby = :hobby
                AND G.level = :level
                AND G.language = :lang
                AND G.place = :place
                AND NOT G.key_id = :currentkeyid
                AND datum>=28
                ORDER BY datum ASC
                LIMIT 1 ";
            $statement1 = $dbc->prepare($SELECT);
            $data = array(":hobby"=>$g_hobby, ":level"=>$g_level, ":lang"=>$g_lang,":place"=>$g_place, ":currentkeyid"=>$currentkeyid);
            $statement1->execute($data);
            $count = $statement1->rowCount();
            $row= $statement1->fetchAll();
            if($count==1){
                $newKID = $row['key_id'];
                $newUID = $row['user_id'];

                $UPDATE1 = "UPDATE `group_users` SET user_id=:uid, timestamp=CURRENT_TIMESTAMP WHERE user_id=:newUID AND key_id=:newKID";
                $updatearray1 = array(":newUID"=>$newUID,":uid"=>$userid, ":newKID"=>$newKID);
                $statement3  = $dbc->prepare($UPDATE1);   

                $UPDATE = "UPDATE `group_users` SET user_id=:newUID, timestamp=CURRENT_TIMESTAMP WHERE user_id=:uid AND key_id=:currentkeyid";
                $updatearray = array(":newUID"=>$newUID,":uid"=>$userid, ":currentkeyid"=>$currentkeyid);
                $statement1 = $dbc->prepare($UPDATE);
                if(($statement3->execute($updatearray1) && ($statement1->execute($updatearray)))){
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
                    $message .= '<p>Hi '.get_user_name($userid, $dbc).',</p>';
                    $message .= '<br>the month has just flown by,';
                    $message .= '<br>and you switched groups.';
                    $message .= '<br>Special Social';
                    $message .= '</body></html>';
                    
                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= 'From: '.$from."\r\n".
                                'Reply-To: '.$from."\r\n" .
                                'X-Mailer: PHP/' . phpversion();
            
                    mail($to, $subject, $message, $headers);
                }
            }elseif($count==0){
                $ChangeS = "SELECT change FROM `group_users` WHERE (user_id=:uid AND key_id=:currentkeyid)";
                $ChangeSarray = array(":uid"=>$userid, ":currentkeyid"=>$currentkeyid);
                $statement = $dbc->prepare($ChangeS);
                $statement->execute($ChangeSarray);
                $row = $statement->fetchALL();
                if($row['change']==0){
                    $DELETE = "DELETE FROM `group_users` WHERE (user_id=:uid AND key_id=:currentkeyid)";
                    $DELETEarray = array(":uid"=>$userid, ":currentkeyid"=>$currentkeyid);
                    $statement1 = $dbc->prepare($DELETE);
                    if($statement1->execute($DELETEarray)){
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
                        $message .= '<p>Hi '.get_user_name($userid, $dbc).',</p>';
                        $message .= '<br>unfortunately we were not able to find you a new group,';
                        $message .= '<br>as you wished you were deleted from the group '.get_group_name($keyid, $dbc).'.';
                        $message .= '<br>Special Social';
                        $message .= '</body></html>';

                        $headers  = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        $headers .= 'From: '.$from."\r\n".
                                    'Reply-To: '.$from."\r\n" .
                                    'X-Mailer: PHP/' . phpversion();
                
                        mail($to, $subject, $message, $headers);
                    }
                }
            }
        }
    }elseif($difference=21){
        $to = get_email($userid, $dbc);
        $subject = 'Change groups';
        $from = 'no-reply@special.social';
        $message = '<html><head><style>
        p{
            
            color:blue;
        }
                        
        #verify{
            color: brown;
        }             
        </style></head><body>';
        $message .= '<p>Hi '.get_user_name($userid, $dbc).'</p>';
        $message .= '<br> we would like to let you know that,';
        $message .= '<br>you have been in '.get_group_name($keyid, $dbc).' for 3 week,';
        $message .= '<br>after every month the user switches a group,';
        $message .= '<br>but if we dont find one the user either stays in the group or is excluded from it.';
        $message .= '<br>After one week you will be switched to another group but,';
        $message .= '<br>in case there is no group you can stay in the previous group, please click this button: ';
        $message .= '<a href="http://special.social/change_chat.php?n=1&vkey=$currentkeyid"><input type="button" value="verify"/></a>';
        $message .= '<br>If you do not do anything you will be switched automaticaly and you can enjoy your stay :)';
        $message .= '<br>Special Social';
        $message .= '</body></html>';
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: '.$from."\r\n".
                    'Reply-To: '.$from."\r\n" .
                    'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }
}
?>