<?php
include ("objects.php");

$obj = new Object();
$calculo=0.00000001;
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
#$obj->getMyClosedOrders("XRPCLP");/*14260*//*Venta Mercado*/$calculo=0.000001;
#$obj->getMyClosedOrders("LTCCLP");/*5680.024*/
#$obj->getMyClosedOrders("BCHCLP");/*4000*/
#$obj->getMyClosedOrders("DASHCLP");/*3900*/

/*$apiKey = "";
$secretKey = "";*/

$response = userAgent($apiKey, $secretKey, $obj->json);
if(strcmp($response,"-1") !=0){
  /*Es distinto de menos 1, por tanto, no ocurrió nada en el UserAgent (OK)*/
	print_r("<pre>".$response."</pre>");
	$result = json_decode($response, true);

	$sumaCompra=0;
	$sumaVenta=0;

	$sumaVentaMarket=0;
	$sumaCompraMarket=0;
	for($i=0; $i<count($result["data"]["orders"]["items"]); $i++){
		#echo "I: $i: <br>";
		if($result["data"]["orders"]["items"][$i]["status"]=="closed"){
			#echo " ".$result["data"]["orders"]["items"][$i]["_id"]." <br>";
			if($result["data"]["orders"]["items"][$i]["type"]=="limit"){
				$sell = $result["data"]["orders"]["items"][$i]["sell"];
				list($totalCompra, $totalVenta,$coin,$price) = getAmountSellBuy($result["data"]["orders"]["items"][$i]["trades"],$sell);
				$milSeconds = $result["data"]["orders"]["items"][$i]["createdAt"] / 1000;
				#echo date('d/m/Y H:i:s',$milSeconds);	echo " ====> ".$result["data"]["orders"]["items"][$i]["_id"]."<br>";
			}else if($result["data"]["orders"]["items"][$i]["type"]=="market"){
				$sell = $result["data"]["orders"]["items"][$i]["sell"];
				list($totalCompra, $totalVenta,$coin,$price) = getAmountSellBuy($result["data"]["orders"]["items"][$i]["trades"],$sell);
			}
			#echo " => Compra($totalCompra) - Venta($totalVenta)<br>";
			echo "$totalCompra;$totalVenta;".str_replace(".",",",$coin).";$price<br>";
			$sumaCompra=$sumaCompra+$totalCompra;
			$sumaVenta=$sumaVenta+$totalVenta;
		}
	}


	echo "<br>$sumaCompra - $sumaVenta";
	$resta = $sumaCompra - $sumaVenta;

	echo "<br>$sumaCompraMarket - $sumaVentaMarket";
	$restaMarket = $sumaCompraMarket - $sumaVentaMarket;

	echo "<br> -->Limit: $resta <br>";
	echo "<br> -->Market: $restaMarket <br>";


	echo "<br>SUMA TOTAL: ".($resta+$restaMarket);
	/*foreach ($var['data'] as $result) {
		echo $result."<BR>";
    
	}*/
	/*echo "OK<br>$response";
	echo "<br>---->$var";*/

}else{
  echo "NOK";
}
exit;



function getAmountSellBuy($trades,$sell){
	$sumaVenta=0;
	$sumaCompra=0;
	$coin=0;
	$price=0;
	$averageCounter=1;
	foreach($trades as $array){							
		foreach($array as $itemTrades=>$valuesTrades){
			#echo "==========> $itemTrades => $valuesTrades <br>";
			if($itemTrades=="totalCost"){
				if($sell == 1){
					/*Venta*/
					$sumaVenta=$sumaVenta+$valuesTrades;
				}else{
					$sumaCompra=$sumaCompra+$valuesTrades;
				}				
			}
			if($itemTrades=="amount"){
				$coin=$coin+$valuesTrades*0.00000001;
			}
			if($itemTrades=="price"){
				if($averageCounter==1){
					$price=$valuesTrades;
					$averageCounter++;
				}else{
					$price=round(($valuesTrades+$price)/$averageCounter);
				}
				//$price=$price+$valuesTrades;
			}
		}
	}
	return array($sumaCompra, $sumaVenta, $coin, $price);
}

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
