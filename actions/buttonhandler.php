<?php
#	I denna fil bestäms vad som händer när en knapp trycks på sidan
session_start();
require '../data/evote.php';
$evote = new Evote();

if(isset($_POST["button"])){
# ------------- NAV-BUTTONS ------------------------------------	
	if($_POST["button"]=="login"){ 
		$input_ok = TRUE;
		$msg = "";	
		$msgType = "";
		if($_POST["usr"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte skrivit in något användarnamn. ";
			$msgType = "error";
		}
		if($_POST["psw"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett något lösenord ";
			$msgType = "error";
		}
		
		if($input_ok){
			$usr = $_POST["usr"];
			$psw = $_POST["psw"];
			$correct = $evote->usercheck($usr, $psw); # Kolla lösenordet mot databases. Detta sker i data/evote.php

			if($correct){
				$_SESSION["user"] = $usr;
				
			}else{
				$msg .= "Användarnamet och/eller lösenordet är fel. ";
				$msgType = "error";
			}	
		}
		$_SESSION["message"] = array("type" => $msgType, "message" => $msg);
		$redirect = $_SESSION["redirect"];
		header("Location: /".$redirect);

	}else if($_POST["button"]=="stat"){
		header("Location: /stat");

	}else if($_POST["button"]=="print"){ 
		header("Location: /actions/codeprint.php");

	}else if($_POST["button"]=="clear"){ 
		header("Location: /clear");

	}else if($_POST["button"]=="logout"){ 
		session_unset();	
		header("Location: /front");
# ------------ ACTION BUTTONS ---------------------------------
	}else if($_POST["button"]=="vote"){ # RÖSTA KNAPPEN I FRONT-PANELEN
		$input_ok = TRUE;
		$msg = "";	
		$msgType = "";
		if(!isset($_POST["person"])){
			$input_ok = FALSE;
			$msg .= "Du har inte valt någon att rösta på. ";
			$msgType = "error";
		}
		if($_POST["code1"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett någon personlig valkod. ";
			$msgType = "error";
		}
		if($_POST["code2"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett någon tillfällig valkod. ";
			$msgType = "error";
		}
		if($input_ok){
			$person_id = $_POST["person"];
			$code1 = $_POST["code1"];
			$code2 = $_POST["code2"];
			/*
			TODO
			Kolla om den personliga koden är brukbar och lägg sedan till rösten i databasen
			*/
			$msg .= "Din röst har blivit registrerad.";
			$msgType = "success";
		}
		$_SESSION["message"] = array("type" => $msgType, "message" => $msg);
		header("Location: /front");

	}else if($_POST["button"]=="create"){ # SKAPA NYTT VAL KNAPPEN
		$input_ok = TRUE;
		$msg = "";
		$msgType = "";
		if($_POST["valnamn"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett något namn på valet. ";
			$msgType = "error";
		}
		if($_POST["antal_personer"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett det maximala antalet personer. ";
			$msgType = "error";
		}
		if($input_ok){
			$name = $_POST["valnamn"];
			$nop = $_POST["antal_personer"];
			/*
			TODO
			Skapa ett nytt val
			*/
		}
		$_SESSION["message"] = array("type" => $msgType, "message" => $msg);
		header("Location: /admin");

	}else if($_POST["button"]=="begin_round"){ # STARTA NYTT VAL KNAPPEN
		$input_ok = TRUE;
		$msg = "";
		$msgType = "";
		if($_POST["round_name"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett vad som ska väljas. ";
			$msgType = "error";
		}
		if($_POST["code"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett någon tillfällig kod. ";
			$msgType = "error";
		}
		$cands[0] = "";
		$n = 0;
		foreach($_POST["candidates"] as $name){
			if($name != ""){
				$cands[$n] = $name;
				$n++;
			}
		}
		if($n < 2){
			$input_ok = FALSE;
			$msg .= "Du måste ange minst två kandidater. ";
			$msgType = "error";
			
		}
		if($input_ok){
			$round_name = $_POST["round_name"];
                        $code = $_POST["code"];

                        $evote->newRound($round_name, $code,  $cands);
			/*
			TODO
			Starta en ny valomgång.
			*/	
		}
		$_SESSION["message"] = array("type" => $msgType, "message" => $msg);
	//	header("Location: /admin");

	}else if($_POST["button"]=="end_round"){ # AVSLUTA VALOMGÅNG KNAPPEN
		header("Location: /admin");

	}else if($_POST["button"]=="delete_election"){ # TA BORT VAL KNAPPEN
		$input_ok = TRUE;
		$msg = "";
		$msgType = "";
		if($_POST["pswuser"] == ""){
			$input_ok = FALSE;
			$msg .= "Du har inte angett något lösenord. ";
			$msgType = "error";
		}
		if($_POST["pswmacapar"] == ""){
			$input_ok = FALSE;
			$msg .= "Hemsideansvaring har inte angett sitt lösenord. ";
			$msgType = "error";
		}
		$redirect = "clear";
		if($input_ok){
			$psw1 = $_POST["psw1"];
			$psw2 = $_POST["psw2"];
			$current_usr = $_SESSION["user"];
			if($evote->usercheck($current_usr, $psw1) && $evote->usercheck("macapar", $psw2)){
				/*
				TODO
				Ta bort val
				*/
				$msg .= "Valet har blivit raderat. ";
				$msgType = "success";
				$redirect = "admin";
			}else{
				$msg .= "Någon skrev in fel lösenord. ";
				$msgType = "error";
			}
		}
		$_SESSION["message"] = array("type" => $msgType, "message" => $msg);
		header("Location: /".$redirect);

	}
}
?>
