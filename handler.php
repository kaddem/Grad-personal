<?php

function CRMExchange( $url, $params = null )
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_ENCODING ,"");
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function LeadMail( $from, $to, $subject, $message )
{
	$host = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : "Сайт компании";
	$headers = "Content-type: text/html; charset=utf-8 \r\n"."From: ".$host." <".$from.">\r\n";
	$answer = (mail($to, $subject, $message, $headers)) ? 1 : 0;
	return $answer;
}

function SetSubject( $action )
{
	$action = (!empty($action)) ? $action : "default";	

	$subject = array(
			 "customer" => "Интерес к аренде персонала", 
			 "worker"   => "Отклик на вакансию",
			 "default"  => "Заявка с сайта"
			);
	$result = (!empty($subject[$action])) ? $subject[$action] : $subject["default"];
	return $result; 
}

function Mailer( $data )
{
	$to = "";
	$from = "";

	$logo = array("url"=>"http://gradpersonal.ru/images/main-logo.png", "size"=>array("width"=>133, "height"=>44));

	$subject = SetSubject($data->action);

	$dopInfo = "
		<p>Информация о работнике занесена в CRM-систему.</p>
		<p>Для уточнения данных свяжитесь с работником<br>и занесите полученные сведения в карточку работника.</p>	
	";

	$dopInfo = ($data->action == "worker") ? $dopInfo : "";
	
	$message =  "
    			<html>
    				<head>
    					<meta charset='utf-8>
    					<title>".$subject."</title>
    				</head>
    				<body>
    					<table>
        					<tbody>
         						<tr>
                						<td style='padding:5px;' colspan=2><strong>".$subject."</strong></td>
            						</tr>
            						<tr>
                						<td style='padding:5px;'><strong>Телефон</strong></td>
                						<td style='padding:5px;'>".$data->telEmail."</td>
            						</tr>
        					</tbody>
    					</table>
            				".$dopInfo."
            				<img style='margin-top:10px; border:none;' src='".$logo["url"]."' width='".$logo["width"]."' height='".$logo["height"]."'>
    				</body>
    			</html>
		";

	return LeadMail($from, $to, $subject, $message);
}

/*интеграционный ключ*/

$key = "";

/*********************/

	$data = json_decode(file_get_contents('php://input')); 

	if(!empty($data))
	{
		$result = Mailer($data);
		echo $result;
	}

	$params = json_encode(array("key"=>$key, "action"=>$data->action, "tel"=>$data->tel));
	$result = CRMExchange("http://crm.yastaff.ru/server/inter/inter.php", $params);
?>
