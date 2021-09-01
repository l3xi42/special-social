<?php

include('dbc.php');
session_start();

if(!isset($_SESSION['user_id'])){
    header("location:login.php");
}
?>

<html>  
    <head>   
        <title>Home</title>
        <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
        <script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
        <link rel="stylesheet" href="home.css" type="text/css">
        <link rel="stylesheet" href="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.css">
    </head>  
    <body>
        <div align="center" class="menu">
            <h1>SPECIAL SOCIAL</a></h1>
            <h3>Home</h3>

            <ul>
            <li><a class="active" href="home.php">Home</a></li>
            <li><a href="newgroup.php">New group</a></li>
            <li ><a href="account.php">Account</a></li>
            <li style="float: right;"><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div align="left" class="table-responsive">
            <div id="user_details"></div>
            <div id="user_model_details"></div>
        </div>

    </body>
</html>

<script>  
$(document).ready(function(){

fetch_user();

setInterval(function(){
    fetch_user();
    update_chat_history_data();
    fetch_group_chat_history();
}, 2000);

function fetch_user(){
    $.ajax({
        url:"fetch_user.php",
        method:"POST",
        success:function(data){
        $('#user_details').html(data);
        }
    })
}

function update_chat_history_data(){
    $('.chat_history').each(function(){
        var to_key_id = $(this).data('tokeyid');
        fetch_group_chat_history(to_key_id);
    });
}

function make_chat_dialog_box(to_key_id, to_name){
    var box = '<div id="group_dialog_'+to_key_id+'" class="group_dialog" title="Group chat '+to_name+' ">';
        box += '<div class="chat_history" data-tokeyid="'+to_key_id+'" id="chat_history_'+to_key_id+'">';
        box += fetch_group_chat_history(to_key_id);
        box += '</div>';
        box += '<div class="form-group">';
        box += '<textarea name="chat_message_'+to_key_id+'" id="chat_message_'+to_key_id+'" class="form-control"></textarea>';
        box += '</div><div class="form-group" align="right">';
        box += '<button type="button" name="send_chat" id="'+to_key_id+'" class="btn btn-info send_chat">Send</button></div></div>';
    $('#user_details').html(box);
}

function make_user_list_box(to_key_id){
    var box = '<div style="height:200px; width:100px; overflow-y: scroll; margin-bottom:24px; padding:16px;" id="userlist_'+to_key_id+'" class="userlist" title="User list">';
        box += '<div class="list_user" data-tokeyid="'+to_key_id+'" id="list_user_'+to_key_id+'">';
        box += fetch_user_list(to_key_id);
        box += '</div>';
        box += '</div>';
    $('#user_details').html(box);
}

$(document).on('click', '.start_chat', function(){
    var to_key_id = $(this).data('tokeyid');
    var to_name = $(this).data('toname');
    make_chat_dialog_box(to_key_id, to_name);

    $("#group_dialog_"+to_key_id).dialog({
        autoOpen:false,
    });
    $('#chat_message_'+to_key_id).emojioneArea({
			pickerPosition:"top",
			toneStyle: "bullet"
		});
    $('#group_dialog_'+to_key_id).dialog('open');
});

$(document).on('click', '.userlist', function(){
    var to_key_id = $(this).data('tokeyid');
    make_user_list_box(to_key_id);
    $("#userlist_"+to_key_id).dialog({
        autoOpen:false,
    });
    $('#userlist_'+to_key_id).dialog('open');
});

$(document).on('click', '.send_chat', function(){
    var to_key_id = $(this).attr('id');
    var chat_message = $('#chat_message_'+to_key_id).val();
    var action = 'insert_data';
    if(chat_message != ''){
        $.ajax({
            url:"insert_chat.php",
            method:"POST",
            data:{action:action, to_key_id:to_key_id, chat_message:chat_message},
            success:function(data){
                var element = $('#chat_message_'+to_key_id).emojioneArea();
				element[0].emojioneArea.setText('');
                $('#chat_message_'+to_key_id).val('');
                $('#chat_history_'+to_key_id).html(data);
            }
        })
    }
});

function fetch_group_chat_history(to_key_id){
    var action = "fetch_data";
		$.ajax({
			url:"insert_chat.php",
			method:"POST",
			data:{action:action, to_key_id:to_key_id},
			success:function(data){
				$('#chat_history_'+to_key_id).html(data);
			}
		})                                                                                                      
	}
});

/*
$(document).on('click', '.leave_chat', function(){
    var leave = "leave";
    var to_key_id = $(this).data('tokeyid');
    $.ajax({
        url:"change_chat.php",
        method:"POST",
        data:{to_key_id:to_key_id, leave:leave},
        success:function(data){
            $(to_key_id).html(data);
        }
    })  
});
*/

/*
function change_group_chat(to_key_id){
    var leave = "auto";
    var to_key_id = $(this).data('tokeyid');
    $.ajax({
        url:"change_chat.php",
        method:"POST",
        data:{to_key_id:to_key_id, leave:leave},
        success:function(data){
            $(to_key_id).html(data);
        }
    })  
}*/
</script>