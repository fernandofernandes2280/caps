<?php

namespace App\Utils;

if(isset($_POST['action']) && $_POST['action'] == "view"){
	
	$ob = "teste";
	echo json_encode(Soma());
	
	
}

function Soma(){
	
	return 20+30;
	
}