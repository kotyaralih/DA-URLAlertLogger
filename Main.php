<?php

class Main{
	
	public $runcount = 0;
	public $lastdonationid = null;
	
	public function onStartup(){
		$token = file_get_contents("token") ? file_get_contents("token") : null;
		if(!file_exists("token") or $token === null or $token === "private"){
			echo "Для использования программы, вам необходимо иметь токен от вашего DonationAlerts!" . PHP_EOL;
			echo "Инструкция по его получению указана на гитхабе программы: https://github.com/kotyaralih/DA-URLAlertLogger" . PHP_EOL . PHP_EOL;
			echo "Введите свой токен от DonationAlerts: ";
			$token = rtrim(fgets(STDIN));
			echo chr(27).chr(91)."H".chr(27).chr(91)."J"; //очищаем кмдшку, чтобы не спалить токен случайно!!!
			if($token !== "veryprivate"){
				echo "Хотите ли вы сохранить свой токен, чтобы не вводить его каждый раз при запуске программы?" . PHP_EOL;
				echo "ВНИМАНИЕ!!! Если вы согласитесь, то он будет храниться в незашифрованном виде, в файле token, в папке, из которой вы запустили программу." . PHP_EOL;
				echo "Для согласия, введите английскую Y" . PHP_EOL;
				echo "Для отказа, введите английскую N" . PHP_EOL;
				$answer = rtrim(fgets(STDIN));
				if(strtolower($answer) === "y"){
					if(file_put_contents("token", $token)){
						echo "Ваш токен был успешно сохранен!" . PHP_EOL;
					} else {
						echo "Произошла неизвестная ошибка." . PHP_EOL;
						echo "Скорее всего, вы открываете программу из папки, к которой нет доступа у обычного пользователя!" . PHP_EOL;
						die();
					}
				} else {
					if(file_put_contents("token", "private")){ //не сохраняем токен
						echo "Хорошо, мы не сохранили ваш токен в системе." . PHP_EOL;
						echo "Хотите, чтобы мы запомнили ваш выбор не сохранять токен?" . PHP_EOL;
						echo "Для согласия, введите английскую Y" . PHP_EOL;
						echo "Для отказа, введите английскую N" . PHP_EOL;
						$annoyingquestion = rtrim(fgets(STDIN));
						if(strtolower($annoyingquestion) === "y"){
							echo "Готово." . PHP_EOL;
							file_put_contents("token", "veryprivate"); //не сохраняем токен, и еще вырубаем "допрос" при запуске проги
						} else {
							echo "Ладно." . PHP_EOL;
						}
					} else {
						echo "Произошла неизвестная ошибка." . PHP_EOL;
						echo "Скорее всего, вы открываете программу из папки, к которой нет доступа у обычного пользователя!" . PHP_EOL;
						die();
					}
				}
			}
		}
		$this->runApp($token);
	}
	
	public function runApp($token){

		echo "Программа успешно запущена." . PHP_EOL;
		if(is_string(json_decode($this->sendAPIRequest("user/oauth", $token), true)["data"]["name"])){
			echo "Здравствуйте, " . json_decode($this->sendAPIRequest("user/oauth", $token), true)["data"]["name"] . "!" . PHP_EOL;

			$this->runcount = 0;

			while(true){
				$this->runcount++;
				$this->sendAPIRequest("alerts/donations", $token);
				sleep(20);
			}
		} else {
			echo "Не авторизован (возможно токен введен неверно)! Изменить его можно в файле token" . PHP_EOL;
			die();
		}
	}

	public function sendAPIRequest($method, $token){
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://www.donationalerts.com/api/v1/" . $method);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	    curl_setopt($ch, CURLOPT_HEADER, 0);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "{}");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    // Таймаут в секундах
	    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

	    $output = curl_exec($ch);
	    if($method === "alerts/donations"){
			if($output === null or !isset(json_decode($output, true)["data"])) return false;
			$lastdonation = json_decode($output, true)["data"][0];
			if($this->runcount < 1 or $lastdonation["id"] === $this->lastdonationid) return false;
			if($this->lastdonationid === null){
				$this->lastdonationid = $lastdonation["id"];
				return false; //некий фикс того, что донат сделанный до открытия проги вновь выводился в консольку и записывался в лог
			}
			$this->lastdonationid = $lastdonation["id"];
			
			$regex = "((https?|ftp)\:\/\/)?"; // SCHEME 
		    $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass 
		    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP 
		    $regex .= "(\:[0-9]{2,5})?"; // Port 
		    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path 
		    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query 
		    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 

			if(preg_match_all("/".$regex."/", $lastdonation["message"], $links, PREG_PATTERN_ORDER)){ 
				foreach($links[1] as $link){
					echo $lastdonation["created_at"] . " " . $lastdonation["username"] . " (" . $lastdonation["amount"] . " " . $lastdonation["currency"] . ") - " . $lastdonation["message"] . PHP_EOL;
					if(!file_exists("urlDons.log")) file_put_contents("urlDons.log", $lastdonation["created_at"] . " " . $lastdonation["username"] . " (" . $lastdonation["amount"] . " " . $lastdonation["currency"] . ") - " . $lastdonation["message"]); else file_put_contents("urlDons.log", file_get_contents("urlDons.log") . PHP_EOL . $lastdonation["created_at"] . " " . $lastdonation["username"] . " (" . $lastdonation["amount"] . " " . $lastdonation["currency"] . ") - " . $lastdonation["message"]);
				} 
			}
		}
	    return $output;
	}
}
$main = new Main();
$main->onStartup();
?>