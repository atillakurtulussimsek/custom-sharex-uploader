<?php
 // The length here is taken into account when naming your file.
 define('FILE_NAME_LENGTH', 6);

 // You must set a secret key for security. 
 // Installation is not complete unless this key is correct.
 define('ADMIN_KEY','YOUR_SECRET_KEY');

 // Specify the directory where your files will be uploaded. 
 // If there is no directory, type './'
 define('DIR','./');

 // Specify the characters you want in the names of your files.
 // You can optionally leave it as default.
 define('KEY','abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

 // Type the URL of your site.
 // ATTENTION! Do not add / to the end of the URL.
 define('URL','YOUR_URL');

 define('DISCORD_WEBHOOK_SEND_STATUS', true); // true or false
 define('DISCORD_WEBHOOK_URL',''); // Your webhook url.

function random_str($length = FILE_NAME_LENGTH, $keyspace = KEY)
{
	$result = '';
	$max = strlen($keyspace) - 1;
	for ($i = 0; $i < $length; $i++) {
		$result .= $keyspace[random_int(0, $max)];
	}
	return $result;
}

function discord_webhook($msg,$status = DISCORD_WEBHOOK_SEND_STATUS, $url = DISCORD_WEBHOOK_URL)
{
	if( $status == true ){
	 $json_data = json_encode(["content" => $msg,"username" => "AlbaySIMSEK Custom ShareX Uploader","avatar_url" => "https://media.discordapp.net/attachments/613015555447980067/613061456296017921/test2.png"], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	 $ch = curl_init( $url );
	  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	  curl_setopt( $ch, CURLOPT_POST, 1);
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
	  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	  curl_setopt( $ch, CURLOPT_HEADER, 0);
	  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
     $response = curl_exec($ch);
	}
}

 function file_size($klasor = DIR)
 {
    $dizi = array(); 
    $open = opendir($klasor); 
        while($q=readdir($open)) {
            if ($q != "." && $q != "..") { $dizi[] = $q; }
        }
    $sayi = count($dizi); 
    closedir($open);  
    return $sayi;
}

if (isset($_FILES['image'])) {
  if($_FILES['image']['error'] != 0){
  	$array = array("status" => "500","message" => "Internal Server Error!");
    $json = json_encode($array);
    echo $json;
  	discord_webhook(":warning: Content upload failed. Error code: ``".$_FILES['image']['error']."``");
  }else{
     $isim = $_FILES['image']['name'];
     $uzanti = explode('.', $isim);
     $uzanti = $uzanti[count($uzanti)-1];

     if($_POST["key"] != ADMIN_KEY){
     	$array = array("status" => "401","message" => "Unauthorized");
        $json = json_encode($array);
        echo $json;
     	discord_webhook(":warning: Unauthorized access to the content upload system was detected. ``IP: ".$_SERVER['REMOTE_ADDR']."``");
     }else{
     	$new_name = random_str();
     	$new_name = $new_name.'.'.$uzanti;
     	$dosya = $_FILES['image']['tmp_name'];
     	$file_size = file_size();
        copy($dosya, DIR.'/'.$new_name);
        $array = array("status" => "200","url" => URL.'/'.$new_name);
        $json = json_encode($array);
        echo $json;
        discord_webhook(":ok_hand: Content uploaded successfully! You have a total of **".file_size()."** files. ``URL: ".URL.'/'.$new_name.'``');
     }
  }
} else {
	echo "Done!";
}