<?php
    date_default_timezone_set("Asia/kolkata");
    //Data From Webhook
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);
    $chat_id = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];
    $message_id = $update["message"]["message_id"];
    $id = $update["message"]["from"]["id"];
    $username = $update["message"]["from"]["username"];
    $firstname = $update["message"]["from"]["first_name"];
    $start_msg = $_ENV['START_MSG']; 

if($message == "/start"){
    send_message($chat_id,$message_id, "***Olá $firstname \nUse !bin xxxxxx para verificar BIN \n$start_msg***");
}

//Bin Lookup
if(strpos($message, "!bin") === 0){
    $bin = substr($message, 5);
    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_URL => "https://binssuapi.vercel.app/api/".$bin,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
    "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
    "accept-language: es-PE,es;q=0.9,pt-BR;q=0.8,pt;q=0.7,en-US;q=0.6,en;q=0.5",
    "sec-fetch-dest: document",
    "sec-fetch-site: none",
    "user-agent: Mozilla/5.0 (Linux; Android 8.1.0; SM-J260M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Mobile Safari/537.36"
   ],
   ]);

 $result = curl_exec($curl); 
 curl_close($curl);
 $data = json_decode($result, true);
 $bank = $data['data']['bank'];
 $country = $data['data']['country'];
 $brand = $data['data']['vendor'];
 $level = $data['data']['level'];
 $type = $data['data']['type'];
$flag = $data['data']['countryInfo']['emoji'];
 $result1 = $data['result'];

    if ($result1 == true) {
    send_message($chat_id,$message_id, "***✅ BIN válido
Bin: $bin
Marca: $brand
Level: $level
Banco: $bank
País: $country $flag
Modelo:$type
Checked By @$username ***");
    }
else {
    send_message($chat_id,$message_id, "***Insira o BIN válido***");
}
}
    function send_message($chat_id,$message_id, $message){
        $text = urlencode($message);
        $apiToken = $_ENV['API_TOKEN'];  
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chat_id&reply_to_message_id=$message_id&text=$text&parse_mode=Markdown");
    }
?>
