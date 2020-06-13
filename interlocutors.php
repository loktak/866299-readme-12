<?php
$user_data = $_SESSION['user'] ?? null;
if (!empty($user_data)) {
    $profile_id = $user_data['id'];

    $interlocutors = get_interclutors($link, $profile_id); //получаем список собеседников

    $unread_messages_count = 0;

    foreach ($interlocutors as $key => $interlocutor) {
        $id = $interlocutor['id'];

        if ($interlocutor['sender_id'] === $profile_id) {
            $user_id = $interlocutor['receiver_id'];
        } else {
            $user_id = $interlocutor['sender_id'];
        }
        $last_dialog_date = $_COOKIE["last_dialog_$user_id"] ?? null;
        if (!empty($last_dialog_date)) {
            $sql = "SELECT COUNT(*) as new_messages FROM messages m
            JOIN interlocutors i ON i.id = $id
            WHERE m.user_id = $user_id AND m.userto_id = $profile_id AND m.message_date > '$last_dialog_date'";

            $result = get_data($link, $sql)[0];
        }
        $interlocutors[$key]['new_messages'] = $result['new_messages'] ?? 0;
        $unread_messages_count = $unread_messages_count + $interlocutors[$key]['new_messages'];
    }

    return [$unread_messages_count, $interlocutors, $profile_id];
}
