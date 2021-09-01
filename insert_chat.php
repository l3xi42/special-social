
<?php
include('dbc.php');

session_start();
if($_POST["action"] == "insert_data"){
    $data = array(
        ':to_key_id'=> $_POST['to_key_id'],
        ':from_user_id'  => $_SESSION['user_id'],
        ':chat_message'  => $_POST['chat_message'],
    );
    $query = "INSERT INTO chat_message (key_id, from_user_id, chat_message) 
                VALUES (:to_key_id, :from_user_id, :chat_message)";
    $statement = $dbc->prepare($query);
    $statement->execute($data);

    echo fetch_group_chat_history($dbc);
    
}

if($_POST["action"] == "fetch_data"){
    echo fetch_group_chat_history($dbc);
}
?>