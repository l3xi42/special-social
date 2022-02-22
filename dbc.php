<?php
$servername = "";
$severuname = "";
$password = "";
$dbname = "";

$dsn = "mysql:host=$servername;dbname=$dbname";
$dbc = new PDO($dsn, $severuname, $password);

date_default_timezone_set('Europe/Bratislava');

function get_user_name($user_id, $dbc){
	$query = "SELECT userName FROM users WHERE user_id = '$user_id'";
	$statement = $dbc->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['userName'];
	}
}

function get_user_time($userid, $keyid, $dbc){
	$query = "SELECT timestamp FROM group_users WHERE user_id=:userid AND key_id =:keyid";
    $dataarray= array(":userid"=>$userid, ":keyid"=>$keyid);
	$statement = $dbc->prepare($query);
	$statement->execute($dataarray);
	$result = $statement->fetchAll();
	foreach($result as $row){
		return $row['timestamp'];
	}
}

function get_email($userid, $dbc){
	$query = "SELECT email FROM users WHERE user_id=:userid";
    $dataarray= array(":userid"=>$userid);
	$statement = $dbc->prepare($query);
	$statement->execute($dataarray);
	$result = $statement->fetchAll();
	foreach($result as $row){
		return $row['email'];
	}
}

function get_group_name($keyid, $dbc){
	$query = "SELECT hobby FROM `groups` WHERE key_id = :keyid";
	$dataarray= array(":keyid"=>$keyid);
	$statement = $dbc->prepare($query);
	$statement->execute($dataarray);
	$result = $statement->fetchAll();
	foreach($result as $row){
		return $row['hobby'];
	}
}

function fetch_group_chat_history(){ 
    
    $servername = "mysql80.websupport.sk:3314";
    $severuname = "Laura713";
    $password = "Rj9rB`IIy.";
    $dbname = "Registraci";

    $dsn = "mysql:host=$servername;dbname=$dbname";
    $dbc = new PDO($dsn, $severuname, $password);

    $keyid= $_POST['to_key_id'];
    $query = " SELECT from_user_id, chat_message, timestamp FROM chat_message WHERE key_id = :keyid ORDER BY timestamp ASC";
	$statement = $dbc->prepare($query);

	$statement->execute(array(":keyid"=>$keyid));

	$result = $statement->fetchAll();
    $output = '<div>';
    foreach($result as $row){
		$username = '';
		if($row["from_user_id"] == $_SESSION["user_id"]){
			$username = '<b>You</b>';
            $output .= '
		<div class="text-success">
			<p>'.$username.' 
            <br>'.$row['chat_message'].' 
			<br><small><em>'.$row['timestamp'].'</em></small>

			</p>
		</div>
		';
		}else{
			$username = '<b>'.get_user_name($row['from_user_id'], $dbc).'</b>';
            $output .= '
		<div class="text-danger">
			<p>'.$username.'
            <br>'.$row['chat_message'].' 
				<div>
					<small><em>'.$row['timestamp'].'</em></small>
				</div>
			</p>
		</div>
		';
		}
	}
    $output .= '</div>';
    print($output);
}
function userlist(){
    $servername = "mysql80.websupport.sk:3314";
    $severuname = "Laura713";
    $password = "Rj9rB`IIy.";
    $dbname = "Registraci";

    $dsn = "mysql:host=$servername;dbname=$dbname";
    $dbc = new PDO($dsn, $severuname, $password);

    $keyid= $_POST['to_key_id'];
    $query = " SELECT user_id, userName, timestamp FROM users WHERE key_id = :keyid";

	$statement = $dbc->prepare($query);
	$statement->execute(array(":keyid"=>$keyid));
	$result = $statement->fetchAll();
    $output = '<div>';
    foreach($result as $row){
		$username = '';
		if($row["user_id"] == $_SESSION["user_id"]){
			$username = '<b>You</b>';
            $output .= '
		<div class="text-success">
			<p>'.$username.'  
			</p>
		</div>';
		}else{
			$username = '<b>'.get_user_name($row['user_id'], $dbc).'</b>';
            $output .= '
		<div class="text-danger">
			<p>'.$username.'
			</p>
		</div>
		';
		}
	}
    $output .= '</div>';
    print($output);
}
?>
