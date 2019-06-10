<?php
// Handling data in JSON format on the server-side using PHP
header("Content-Type: application/json");
// build a PHP variable from JSON sent using POST method
$arrayfromjsonmail = json_decode(stripslashes(file_get_contents("php://input")), true);
//echo json_encode($v);
$arrayfromjsonmail["ip"] = $_SERVER['REMOTE_ADDR'];
$arrayfromjsonmail['pageform'] = $_SERVER['HTTP_REFERER'];

//Script Foreach
$c = true;

	$project_name = $arrayfromjsonmail['name']. '. ('. $arrayfromjsonmail['formName'].').'; //Тема письма
	$admin_email  = 'joker@tjo.biz'; //На какие ящики придет сообщение отправлено

	foreach ( $arrayfromjsonmail as $key => $value ) {
		if ( $value != "" && $key != "formName" ) {
			$message .= "
			" . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
				<td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
				<td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
			</tr>
			";
		}
}
$message = "<table style='width: 100%;'>$message</table>";
function adopt($text) {
	return '=?UTF-8?B?'.Base64_encode($text).'?=';
}
$headers = "MIME-Version: 1.0" . PHP_EOL .
	"Content-Type: text/html; charset=utf-8" . PHP_EOL .
	'From: '.adopt($project_name).' <'.$admin_email.'>' . PHP_EOL .
	'Reply-To: '.$admin_email.'' . PHP_EOL;

if (mail($admin_email, adopt($project_name), $message, $headers )) {
	$jsonresponse = ['name' => $arrayfromjsonmail["name"],
		'message' => $arrayfromjsonmail["message"]];
} else {
	$jsonresponse = [$arrayfromjsonmail["name"] => 'Возникла ошибка при отработке функции почты на каком-то из серверов! Попробуйте повторить через минуту или свяжитесь с нами другим способом.'];
}
echo json_encode($jsonresponse);

?>