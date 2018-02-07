<?php
include ("objects.php");

$obj = new Object();
#$obj->getMarketCurrentStats("CHACLP");
$obj->getMyClosedOrders("CHACLP");/*
		207769 
		-6087
		594839.999982
   	883720.78793111	
		70436.80257212 
		19791.038 
 */
$obj->getMyClosedOrders("BTCCLP");/*96410.86690716 */
#$obj->getMyClosedOrders("ETHCLP");/*155782.6996*/
#$obj->getMyClosedOrders("XRPCLP");/*14260*//*Venta Mercado*/
#$obj->getMyClosedOrders("LTCCLP");/*5680.024*/
#$obj->getMyClosedOrders("BCHCLP");/*4000*/
#$obj->getMyClosedOrders("DASHCLP");/*3900*/


$response = userAgent($apiKey, $secretKey, $obj->json);
if(strcmp($response,"-1") !=0){
  /*Es distinto de menos 1, por tanto, no ocurrió nada en el UserAgent (OK)*/
	$result = json_decode($response, true);

	$totalOperaciones=$result["data"]["orders"]["totalCount"];
	if($result["data"]["orders"]["totalCount"]>50){
		/*Trae como tope 50*/
		#$totalOperaciones=22;
		//$totalOperaciones=50;
	}
	print_r("<pre>".$response."</pre>");

	foreach($result["data"]["orders"] as $keys=>$value){
		if($keys=="totalCount" and $value>0){
			echo "=== $keys=>$value<br>";
		
		}
		if($keys=="items"){
			foreach($value[0] as $keyItem=>$valueItem){
 				if($keyItem!="market"){
					#echo "$keyItem=>$valueItem<br>";
				}
			}
		
		}

		//echo $keys."-->".$value;
		/*foreach($array as $data){
			echo var_dump($data)."<br>";
			//echo $data;
	}	*/  
	}


	$sumaCompra=0;
	$sumaVenta=0;
	for($i=0; $i<$totalOperaciones; $i++){
		echo "I: $i: <br>";
		if($result["data"]["orders"]["items"][$i]["type"]=="limit"
					and $result["data"]["orders"]["items"][$i]["status"]=="closed"){
			$amount = $result["data"]["orders"]["items"][$i]["amount"];		
			$sell = $result["data"]["orders"]["items"][$i]["sell"];
			#$newValue = sprintf("0.%08d", $amount);
			$newValue = $amount*0.00000001;
			$limitPrice = $result["data"]["orders"]["items"][$i]["limitPrice"];

			$multiplicaMontoValor = $limitPrice*$newValue;
			#echo $multiplicaMontoValor;
			if($sell == 1){
				/*Venta*/
				echo "Venta";
				$sumaVenta=$sumaVenta+$multiplicaMontoValor;		
			}else{
				$sumaCompra=$sumaCompra+$multiplicaMontoValor;		
			}
			echo $result["data"]["orders"]["items"][$i]["_id"]."---";
					
			#echo "LimitPrice: $limitPrice - Monto: ".$amount."=> Transformado".$newValue."<br>";
			echo "LimitPrice: $limitPrice x Transformado: ".$newValue." = $multiplicaMontoValor<br>";
		}else if($result["data"]["orders"]["items"][$i]["type"]=="market"
					and $result["data"]["orders"]["items"][$i]["status"]=="closed"){
			$amount = $result["data"]["orders"]["items"][$i]["secondaryAmount"];
			$sumaCompra=$sumaCompra+$amount;
			$sell = $result["data"]["orders"]["items"][$i]["sell"];
			if($sell == 1){
				/*Venta*/
				echo "Venta a precio mercado: $sell|$amount<br>";
			}else{
				echo "Compra a precio mercado: $amount<br>";
			}				
		}
/*		foreach($result["data"]["orders"]["items"][$i] as $keyItem=>$valueItem){
 			if($keyItem!="market"){
				echo "$keyItem=>$valueItem<br>";
			}
}*/
	
	}

	echo "<br>$sumaCompra - $sumaVenta";
	$resta = $sumaCompra - $sumaVenta;
	echo "<br> --> $resta <br>";

	/*foreach ($var['data'] as $result) {
		echo $result."<BR>";
    
	}*/
	/*echo "OK<br>$response";
	echo "<br>---->$var";*/

}else{
  echo "NOK";
}
exit;


function userAgent($apiKey, $secretKey, $json ){
	try{
		/*Se obtiene TimeStamp*/
		$fecha = new DateTime();
		$timestamp = $fecha->getTimestamp();
		/*Se concatena al mensaje JSON*/
		$message = $timestamp.$json;
		/*Se encripta*/
		$apiKeySignature = hash_hmac('sha512', $message,$secretKey);

		$chObj = curl_init();
		curl_setopt($chObj, CURLOPT_URL, 'https://api2.orionx.io/graphql');
		curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'POST');
		/*Dejo de verificar el certificado*/
		curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, false);
		/*Oculto los valores retornados por el header para poder realizar json_decode*/
		//curl_setopt($chObj, CURLOPT_HEADER, true);
		/*curl_setopt($chObj, CURLOPT_CAINFO, "C:/xampp/htdocs/ConectionPhp/cert.crt"); */
		curl_setopt($chObj, CURLOPT_VERBOSE, true);
		curl_setopt($chObj, CURLOPT_POSTFIELDS, $json);
		curl_setopt($chObj, CURLOPT_HTTPHEADER,
     array(
            'User-Agent: Mozilla/5.0',
            'Accept-Language: en-US,en;q=0.5',
            'Content-Type: application/json;charset=utf-8',
						'X-ORIONX-TIMESTAMP: '.$timestamp,
						'X-ORIONX-APIKEY: '.$apiKey,
						'X-ORIONX-SIGNATURE: '.$apiKeySignature
        )
    ); 

		$response = curl_exec($chObj);
		curl_close($chObj);
		#print_r("<pre>".curl_errno($chObj)."</pre>"); 
		#print_r("<pre>".curl_error($chObj)."</pre>");
		#echo var_dump($response);
		return $response;
	
	}catch(Exception $e){
		print_r("<pre>".$e."</pre>");	
		return "-1";
	}
	

}
 






?>
