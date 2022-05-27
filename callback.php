<?php

  $groupCS = "120363042064359049@g.us"; //Group yang akan menerima forward message
  $noAdmin = "6289606072760@c.us"; //No WA utama 

  $inputWA = file_get_contents('php://input');//tangkap semua input

  $dj = json_decode($inputWA, true); //decode json

 /** logger uncomment untuk menyalin semua input kedalam file 
  
  $file = fopen('log.json', 'a');
  fwrite($file, $$inputWA . "\n");
  fclose($file);
  
 */
 
 /** event logger 
    $file = fopen($dj["event"].'.json', 'w');
    fwrite($file, $inputWA );
    fclose($file);
 */
 
 //mapping variabel
    $pengirim = $dj["data"]["sender"];
    $from = $dj["data"]["from"];
    $to = $dj["data"]["to"];
    $pesanGroup = $dj["data"]["isGroupMsg"];
    $repAdmin = $dj["data"]["quotedMsg"]["from"];
    $dariUser = $dj["data"]["sender"]["isUser"];
    $pesan = $dj["data"]["text"];
    $chat = $dj["data"]["chat"];
    $tipe = $dj["data"]["type"];
    $msgId = $dj["data"]["id"];



if (!$pesanGroup && $to == $noAdmin && $dj["data"]["type"]=="list_response" && $dj["event"]=="onMessage"){
	switch ($dj["data"]["listResponse"]["rowId"]) {
		case '#store':
			$data["args"]["to"] = $dj["data"]["from"] ;
			$data["args"]["content"] =  "Toko dibuka";
			$data["args"]["quotedMsgId"] = $dj["data"]["id"];;
			$data["args"]["sendSeen"] = "true";
			$data_string = json_encode($data);
			$ch = curl_init('http://103.9.126.114:8080/reply');        
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($data_string))                                                                       
			);                                                                                                                   
			curl_exec($ch);
		  break;
		case '#regisnew':
		  //code to be executed if n=label2;
		  break;
		case '#event-01':
		  //code to be executed if n=label3;
		  break;
		  
		default:
		  //code to be executed if n is different from all labels;
	  }
	  

//reply as Main Number via group WA
}else if ($pesanGroup && $from == $groupCS && $repAdmin == $noAdmin && $dj["event"]=="onMessage"){
	$predata = explode('|',$dj["data"]["quotedMsg"]["body"]);
	$noCust = $predata[0];
	$custMsg = $predata[1];
	$data["args"]["to"] = $noCust ;
	$data["args"]["content"] =  $dj["data"]["body"];
	$data["args"]["quotedMsgId"] = $custMsg;
	$data["args"]["sendSeen"] = "true";
	
	$data_string = json_encode($data);
	$ch = curl_init('http://103.9.126.114:8080/reply');        
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                
    	 'Content-Type: application/json',                                                                                
    	 'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                   
    curl_exec($ch);
//Forward image ke dalam Group WA
}else if ((!$pesanGroup) && ($from!=$noAdmin) && $to == $noAdmin && $dj["data"]["type"]=="image" && $dj["event"]=="onMessage") {
	$data["args"]["to"] = $groupCS ;
	$data["args"]["messages"] = $dj["data"]["id"];
	$data["args"]["skipMyMessages"] =true;
	$data_string = json_encode($data);
	$ch = curl_init('http://103.9.126.114:8080/forwardMessages');        
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                
    	 'Content-Type: application/json',                                                                                
    	 'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                   
    curl_exec($ch);	
    
//Forward message ke dalam Group WA
}else if ( (!$pesanGroup) && ($from!=$noAdmin) && $to == $noAdmin && $dj["event"]=="onMessage"){
	$data["args"]["to"] = $groupCS ;
	$data["args"]["content"] =  $from.'|'.$dj["data"]["id"]."|\n *".$dj["data"]["body"]."*";
	$data_string = json_encode($data);
	$ch = curl_init('http://103.9.126.114:8080/sendText');        
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                
    	 'Content-Type: application/json',                                                                                
    	 'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                   
    curl_exec($ch);	
}
