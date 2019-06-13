<?php
// Handling data in JSON format on the server-side using PHP
header("Content-Type: application/json");
// build a PHP variable from JSON sent using POST method
$arrayfromjsonmail = json_decode(stripslashes(file_get_contents("php://input")), true);
//Если без парамерта true декодируем json, то получаем объект, а не массив, тогда обращаемся к свойствам соответственным способом
//To access the object in your PHP file, use
//$v->name;
//$v->email;
//$v->subject;
//$v->message;
//echo json_encode($v);

$arrayfromjsonmail["ip"] = $_SERVER['REMOTE_ADDR'];
$arrayfromjsonmail['pageform'] = $_SERVER['HTTP_REFERER'];
$project_name = $arrayfromjsonmail['name']. '. ('. $arrayfromjsonmail['formName'].').'; //Тема письма
$admin_email  = 'joker@tjo.biz'; //На какие ящики придет сообщение отправлено

/** Начало кода по SMS оповещению **/

require_once 'sms.ru.php';

$smsru = new SMSRU('B406B5AF-D7D7-6F91-D669-39839284902'); // Ваш уникальный программный ключ, который можно получить на главной странице

$data = new stdClass();
/* Если текст на номера один */
$data->to = '66800323660,66800343991'; // Номера для отправки сообщения (От 1 до 100 шт за раз). Вторым указан городской номер, по которому будет возвращена ошибка
$data->text = 'Коммерческая недвижимость ' . $arrayfromjsonmail['tel']; // Текст сообщения
/* Если текст разный. В этом случае $data->to и $data->text обрабатываться не будут и их можно убрать из кода */
//$data->multi = array( // От 1 до 100 штук за раз
//	"79533606633" => "Hello World", // 1 номер
//	"74993221627" => "Hello World 2", // 2 номер (указан городской номер, будет возвращена ошибка)
//);
$data->from = 'Forms-BOT'; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
$data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
// $data->partner_id = '258350'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
$request = $smsru->send($data); // Отправка сообщений и возврат данных в переменную

if ($request->status == "OK") { // Запрос выполнен успешно
	foreach ($request->sms as $phone => $sms) { // Перебираем массив отправленных сообщений
		if ($sms->status == "OK") {
			$arrayfromjsonmail['smsru'] = "Сообщение на номер +$phone отправлено успешно. ID сообщения: $sms->sms_id. Ваш новый баланс: $request->balance";
		} else {
			$arrayfromjsonmail['smsru'] = "Сообщение на номер +$phone не отправлено. Код ошибки: $sms->status_code. Текст ошибки: $sms->status_text. ";
		}
	}
}
/** Сообщение на телефон тут код заканчивается**/

//Script Foreach
$c = true;

//guard for xss
	function recurse_array_HTML_safe(&$arr) {
		foreach ($arr as $key => $val)
			if (is_array($val))
				recurse_array_HTML_safe($arr[$key]);
			else
				$arr[$key] = htmlspecialchars($val, ENT_QUOTES);
	}

	recurse_array_HTML_safe($arrayfromjsonmail);

$arrayfromjsonmail["ip"] = "<a href=\"https://www.iptrackeronline.com/index.php?ip_address=" . $_SERVER['REMOTE_ADDR'] . "\" target=\"_blank\">Посмотреть где находится IP " . $_SERVER['REMOTE_ADDR'] . "</a>"; // Делаем ссылку, чтобы можно было посмотреть местоположение по IP адресу.

		$translateformnamecolumn = [
			'formName' => 'Название формы',
			'name' => 'Имя',
			'tel' => 'Телефон',
			'email' => 'Почтовый адрес',
			'message' => 'Сообщение',
			'datepicker' => 'Дата события',
			'timepicker' => 'Желаемое время',
			'multipleoptions' => 'Несколько опций на выбор из выподающего списка',
			'radiochoice' => 'Одна обязатльная опция Radio',
			'shopcheck' => 'Аренда под магазин',
			'officecheck' => 'Покупка в офис',
			'partnercheck' => 'Перепродажа через меня',
			'browser' => 'Используемая ОС и браузер',
			'language' => 'Язык браузера и ОС',
			'firstvititedsite' => 'Первое посещение сайта с этого браузера',
			'time' => 'Время у клиента в момент отправления формы и его часовой пояс',
			'countpages' => 'Сколько раз смотрел(а) страницу(ы) сайта',
			'ip' => 'IP адрес',
			'pageform' => 'Страница с которой отправлена форма',
			'smsru' => 'Статус SMS оповещения мобильного через сервис sms.ru'
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