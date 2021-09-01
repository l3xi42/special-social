<?php
include('dbc.php');
session_start();

/*$query = "SELECT * FROM group_users WHERE key_id=:keyid";
$statement = $dbc->prepare($query);
$statement->execute(array(':keyid'=>$g_hobby));*/

$id =  $_SESSION['user_id'];
$query = "SELECT * FROM group_users WHERE user_id = :userid";
$statement = $dbc->prepare($query);
$statement->execute(array(":userid"=>$id));
$result = $statement->fetchAll();

$output = '
<table class="table table-bordered table-striped">
 <tr>
   
  <td style="font-size: 18px;">Your groups:</td>
 </tr>
';

foreach($result as $row){
    $output .= '
    <tr>
    <td><button style="border: none; background-color: #ff616f; color: #fff; border-radius: 5px; width: 80px; height: 35px; font-size: 16px;" type="button" class="btn btn-info btn-xs start_chat" data-tokeyid="'.$row['key_id'].'" data-toname="'.$row['hobby'].'">'.$row['hobby'].'</button></td>
    </tr>';
}

$output .= '</table>';

echo $output;

?>