<?php
include ("objects.php");

$obj = new Object();
$obj->getMarketCurrentStats("CHACLP");
echo $obj->json;
#exit;

#echo $query;
#echo "<br>";
#echo $variables;

#$variables = '';

#$json = json_encode(['query' => $query, 'variables' => $variables ]);
#echo "<br>$json";
#exit;
#$json = '{"query":"query getUserWallets {  me { _id wallets {  currency {code __typename  } availableBalance ...walletListItem  __typename } __typename  }} fragment walletListItem on Wallet {  _id  balance  currency {code units name symbol format isCrypto minimumAmountToSend __typename  }  __typename}"}';

$fecha = new DateTime();
$timestamp = $fecha->getTimestamp();

$apiKey = "";
$secretKey = "";


#$timestamp = "1517930991166";
#$apiKeySignature = "2b09128d922921dace6df68432745b20d0655ac45429a1baa44c60c1afc97e6d429d04e45cde46c9699e1414b9607471f0a6438946d21472a4c65b1c8d2c3d93";

$json = $obj->json;
echo $json."<br>";exit;
#$message = $timestamp.$json;
$message = $timestamp.$json;

#echo $message;
$apiKeySignature = hash_hmac('sha512', $message,$secretKey);
#$apiKeySignature = implode(unpack("H*",$apiKeySignature));
#echo "<br>";



#echo $apiKeySignature;
#exit;

 


$chObj = curl_init();
curl_setopt($chObj, CURLOPT_URL, 'https://api2.orionx.io/graphql');
curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);    
curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'POST');
/*Dejo de verificar el certificado*/
curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chObj, CURLOPT_HEADER, true);
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

#'Authorization: bearer '.GITHUB_TOKEN 
#echo "antes de enviar";
$response = curl_exec($chObj);
#print_r("<pre>".curl_errno($chObj)."</pre>"); 
#print_r("<pre>".curl_error($chObj)."</pre>");
#echo "despues";
echo var_dump($response);



?>
