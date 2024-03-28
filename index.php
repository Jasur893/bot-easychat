<?php
set_time_limit(0);
ob_start();

const TG_TOKEN = "6206608323:AAG9Zy_mKNccA5IX0bCACV3ziyGgQETUi1M";
const ADMIN = "1078608772";

// for connect bot to server with webhook method
// https://api.telegram.org/bot6206608323:AAG9Zy_mKNccA5IX0bCACV3ziyGgQETUi1M/setWebHook?url= https://9688-94-232-25-6.ngrok-free.app/bot-easychat

function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" .TG_TOKEN . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    $res = curl_exec($ch);
    curl_close($ch);
    if (curl_error($ch)){
        var_dump(curl_error($ch));
    } else {
        return json_decode($res, true);
    }
}

function sendMessage($chat_id, $text): void
{
    bot('sendMessage', [
        'chat_id' =>  $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ]);
}

//function forwardMessage($profile ,$chat_id, $message_id): void
//{
//    bot('forwardMessage', [
//        'chat_id' =>  $profile,
//        'from_chat_id' => $chat_id,
//        'message_id' => $message_id,
//    ]);
//}

$update = file_get_contents('php://input');
file_put_contents('data.json', $update);
$update = json_decode($update);

// message variables
$message  = $update->message;
$text = $message->text;
$chat_id = $message->chat->id;
$from_id = $message->from->id;
$message_id = $message->message_id;
$first_name = $message->from->first_name;
$last_name = $message->from->last_name;
$full_name = $first_name. " " .$last_name;

// reply message
$reply_to_message = $message->reply_to_message;
$reply_chat_id = $message->reply_to_message->forward_origin->sender_user->id;
$reply_text = $message->text;



// agar yozgan odam admin bolmasa
if ($chat_id != ADMIN) {
    if ($text == "/start") {
        // Foygdalanuvchiga Admin yoki kompaniya nomidan javob yollaymiz
        $reply = "Assalomu Alaykum <b>". $full_name ."</b>, ". "Qabul Botiga Xush Kelibsiz! \nMurojat Yo'llashingiz mumkin 👇";
        sendMessage($chat_id, $reply);

        // Yangi foydalanuvchi ma'lumotlarini adminga aniq vaqt bilan yuboramiz
        $reply = "<strong>Yangi mijoz: </strong> \n". $full_name. "\n👉 👉 <a href='tg://user?id=".$from_id."'>".$from_id."</a> \n".date('Y-m-d H-i-s');
        sendMessage(ADMIN, $reply);

        // Foydalanuvchidan kelgan ilk /start habarini javob bera olishi uchun adminga yuboramiz
        bot('forwardMessage', [
            'chat_id' => ADMIN,
            'from_chat_id' => $chat_id,
            'message_id' => $message_id
        ]);
    }
    if ($text != "/start") {
        // Foydalanuvchidan kelgan habarni javob bera olishi uchun adminga yuboramiz
        bot('forwardMessage', [
            'chat_id' => ADMIN,
            'from_chat_id' => $chat_id,
            'message_id' => $message_id
        ]);
    }
}
// agar admin yozgan bo'lsa
if ($chat_id == ADMIN){
    // agar admin bot qayta yuborgan hatga javob berish orqali habar yuborsa,
    if (isset($reply_to_message)) {
        sendMessage($reply_chat_id, $reply_text);
    }
    // admin profilidan botni tekshirib korish uchun botdan adminga salom!
    if ($text == "hi" or $text == "/start") {
        sendMessage(ADMIN, "Salom Admin");
    }

}