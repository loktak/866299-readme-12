<?php
session_start();
date_default_timezone_set("Europe/Moscow");
$is_auth = rand(0, 1);
$user_name = 'Арсений'; // укажите здесь ваше имя

require_once('functions.php');
require_once('helpers.php');
require_once('db_requests.php');

$link = database_conecting('localhost', 'root', 'root', 'readme');

define("PHOTO", 1);
define("VIDEO", 2);
define("TEXT", 3);
define("QUOTE", 4);
define("LINK", 5);

$user_data = $_SESSION['user'] ?? NULL;
if (!empty($user_data)) {
    $profile_id = $user_data['id'];

    $interlocutors = get_interclutors($link, $profile_id); //получаем список собеседников
    
    $unreaded_messages_count = 0;
    
    
    foreach ($interlocutors as $key => $interlocutor) {
        $id = $interlocutor['id'];
    
        if ($interlocutor['sender_id'] === $profile_id) {
            $user_id = $interlocutor['receiver_id'];
        } else {
            $user_id = $interlocutor['sender_id'];
        }
        $last_dialog_date = $_COOKIE["last_dialog_$user_id"] ?? NULL;
        if (!empty($last_dialog_date)) {
            $sql = "SELECT COUNT(*) as new_messages FROM messages m
            JOIN interlocutors i ON i.id = $id
            WHERE m.user_id = $user_id AND m.userto_id = $profile_id AND m.message_date > '$last_dialog_date'";
    
            $result = get_data($link, $sql)[0];     
        }
        $interlocutors[$key]['new_messages'] = $result['new_messages'] ?? 0;
        $unreaded_messages_count = $unreaded_messages_count + $interlocutors[$key]['new_messages'];
    }
}
