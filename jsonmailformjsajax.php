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

//guard for xss
	function recurse_array_HTML_safe(&$arr) {
		foreach ($arr as $key => $val)
			if (is_array($val))
				recurse_array_HTML_safe($arr[$key]);
			else
				$arr[$key] = htmlspecialchars($val, ENT_QUOTES);
	}

	recurse_array_HTML_safe($arrayfromjsonmail);

		$translateformnamecolumn = [
			formName => 'Название формы',
			name => 'Имя',
			tel => 'Телефон',
			email => 'Почтовый адрес',
			message => 'Сообщение',
			browser => 'Используемая ОС и браузер',
			language => 'Язык браузера и ОС',
			time => 'Время у клиента в момент отправления формы и его часовой пояс',
			ip => 'IP адрес',
			pageform => 'Страница с которой отправлена форма'
//			countpage => 'Сколько раз были на странице ',
//			time => 'Первое посещение сайта ',
//			yandexwebvisor => 'Ссылка на Яндекс Вебвизор '
		];

		$resulttomail = array(); // Делаем результирующий массив на отправку на почту

		function resultdata ($translateformnamecolumn, $arrayfromjsonmail, &$resulttomail) {
			foreach ($arrayfromjsonmail as $key => $value) {
				if ($arrayfromjsonmail[$key] !== '') {
					$resulttomail[$translateformnamecolumn["$key"]] = $value;
				}
			}
		}

		resultdata($translateformnamecolumn, $arrayfromjsonmail, $resulttomail);

	foreach ( $resulttomail as $key => $value ) {
		if ( $value != "" && $key != "formName" ) {
			$message .= "
			" . ( ($c = !$c) ? '<tr>': '<tr style="background-color: #d4fef2;">') . "
				<td style='padding: 10px; border: #d3cdf8 1px solid; max-width: 250px'><b>$key</b></td>
				<td style='padding: 10px; border: #d3cdf8 1px solid;'>$value</td>
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