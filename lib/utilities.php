<?PHP
function multidimensionalArrayMap() {    
    $aParams = func_get_args();
    $func = $aParams[0];
    $data = $aParams[1];
    unset($aParams[0], $aParams[1]);
    
    $newArr = array();

    foreach( $data as $key => $value) {
        $newsParams = $aParams;    
        $newParams[1] = $aParams[1] = $value;
        $newParams[0] = $func;
        ksort($aParams);
        ksort($newParams);
        //echo "aParams vale : <pre>".print_r($aParams, true)."</pre>";
        //echo "newsParams vale : <pre>".print_r($newsParams, true)."</pre>";
        
        $newArr[$key] = ( is_array( $value ) ? call_user_func_array("multidimensionalArrayMap", $newParams) : call_user_func_array($func, $aParams));
    }

    return $newArr;
}

function formatDateSD($date, $delimiter="/") {
    $out = "0000-00-00 00:00:00";   
    
    if (!empty($date)) {
        $aTmp = explode(" ", $date);    
                        
        if (!empty($aTmp[0])) {
            $aDate = explode($delimiter, $aTmp[0]);                        
            $out = count($aTmp) > 1 ? "{$aDate[2]}-{$aDate[1]}-{$aDate[0]} {$aTmp[1]}" : "{$aDate[2]}-{$aDate[1]}-{$aDate[0]}";            
        }             
        
        return $out;
    }
    else 
        return $out;
}

function microtime_float() {        
    list($utime, $time) = explode(" ", microtime());
    return ((float)$utime + (float)$time);
}

function showMsg($msg, $redirect=false) {
    $code = "<script type='text/javascript'>alert('$msg');";
    
    if($redirect) {
        $code .= "top.location.href = '$redirect';";
    }
    
    $code .= "</script>";
    
    echo $code;
}

function recursiveArraySearch($haystack, $needle, $index = null) {
    $aIt = new RecursiveArrayIterator($haystack);
    $it  = new RecursiveIteratorIterator($aIt);
   
    while($it->valid())
    {       
        if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {
            return $aIt->key();
        }
       
        $it->next();
    }
   
    return false;
}

function createTextF ($data) {    
    $aHead = array_keys(current($data));    
    $str = "";

    foreach ($aHead as $i => $head) 
        $str .= trim($head)."\t";
    
    $str .= "\r\n";
    
    foreach ($data as $row) {            
        foreach ($row as $val)
            $str .= trim($val)."\t";
        
        $str .= "\r\n";
    }
    
    return $str;
}

/*function conversor_monedas($moneda_origen,$moneda_destino,$cantidad) {
    $cantidad = urlencode($cantidad);
    $moneda_origen = urlencode($moneda_origen);
    $moneda_destino = urlencode($moneda_destino);
    $url = "http://www.google.com/ig/calculator?hl=en&q=$cantidad$moneda_origen=?$moneda_destino";
    $ch = curl_init();
    $timeout = 0;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $rawdata = curl_exec($ch);
    curl_close($ch);
    $data = explode('"', $rawdata);
    $data = explode(' ', $data['3']);
    $var = $data['0'];
    return round($var,3);
}*/

function exchangeRateOld($amount, $currency, $exchangeIn) {
    $url = @ 'http://www.google.com/ig/calculator?hl=en&q='.urlEncode($amount . $currency . '=?' . $exchangeIn);
    $data = @ file_get_contents($url);
 
    if(!$data) {
        throw new Exception('Could not connect');
    }
 
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE); 
    $array = $json->decode($data);
 
    if(!$array) {
        throw new Exception('Could not parse the JSON');
    }
 
    if($array['error']) {
        throw new Exception('Google reported an error: ' . $array['error']);
    }
 
    return (float) $array['rhs'];
}

// Ejemplo: Esto mostrará el valor actual de 1 DOLAR en el EUROS
// echo conversor_monedas("USD","EUR",1);

function getRate($amount,$currency,$exchangeIn){
	if($currency == $exchangeIn)
		return $amount;
	$url = "http://www.google.com/finance/converter?a=$amount&from=$currency&to=$exchangeIn";
	$data = @file_get_contents($url);
	require_once "lib/simple_html_dom.inc.php";
	$html = str_get_html($data);
	$span = $html->find("#currency_converter_result span.bld");
	if(count($span) == 0){
		return false;
	}
	$amountString = $span[0]->innertext;
	list($result,$currencyCode) = explode(" ",$amountString);
	return $result;
}
function updateCacheCurrency(){
	global $currency, $exchanges;
	$jsonFile = "cache/curency.json";
	$currentDate = date("Y-m-d");
	$newArray = array();
	if(!file_exists($jsonFile)){
		@mkdir("cache",0777);
		$return = file_put_contents($jsonFile,json_encode(array()));
		if($return === false)
			die("File: $jsonFile not found, support mail: it@latinoaustralia.com");
		chmod($jsonFile,0777);
	}
	if(!is_readable($jsonFile)){
		die("File: $jsonFile is not readable, support mail: it@latinoaustralia.com");
	}
	if(!is_writable($jsonFile)){
		die("File: $jsonFile is not writable, support mail: it@latinoaustralia.com");
	}
	$json_array = json_decode(file_get_contents($jsonFile),true);
	if(($json_array["updated"] != $currentDate) && !isset($_SERVER["REMOTE_ADDR"])){//refreshCache
		$body = "Actualizando Currency" . "\r\n " . print_r($GLOBALS, true);
		@mail("jose.nobile@latinoaustralia.com",
				"Actualizando currency ".date("Y-m-d H:i:s"), $body, "From:database@cali.latinoaustralia.com");
		foreach($exchanges as $exchange){
			foreach($exchanges as $exchange2){
				if($exchange2 == $exchange){
					$newArray[$exchange][$exchange2] = 1;
				}else{
					//echo "$exchange -&gt; $exchange2 <br />\r\n";
					$result = getRate(1, $exchange, $exchange2);
					if($result===false){
						//echo "Invalid convertion from $exchange to $exchange2 skiping <br />\r\n";
						continue;
					}
					$newArray[$exchange][$exchange2] = $result;					
				}
				//file_put_contents($jsonFile,json_encode($newArray));
			}
		}
		//$json_array["olds"][$currentDate] = $newArray;//uncomment to save the historial
		$json_array["updated"] = $currentDate;
		$json_array["current"] = $newArray;
		file_put_contents($jsonFile,json_encode($json_array));
		$body = "Currency actualizada" . "\r\n " . print_r($GLOBALS, true);
		@mail("jose.nobile@latinoaustralia.com",
				"Actualizada currency ".date("Y-m-d H:i:s"), $body, "From:database@cali.latinoaustralia.com");
	}
	$currency = $json_array["current"];	
}

function exchangeRate($amount, $currencyIn, $exchangeIn){
	global $currency;
	if(!isset($currency[$currencyIn][$exchangeIn])){
		return false;
	}
	return $amount*$currency[$currencyIn][$exchangeIn];
}

function encodeData($data) {
    return $data;
}

function decodeData($data) {
    return $data;
}

function abbrText($longString, $maxlength=38, $separator="...") {
    if (strlen($longString) > 40) {    
        $separatorlength = strlen($separator) ;
        $maxlength = $maxlength - $separatorlength;
        $start = $maxlength / 2 ;
        $trunc =  strlen($longString) - $maxlength;
        return substr_replace($longString, $separator, $start, $trunc);
    } else
        return $longString;    
}

function displayDate($strDate, $toFormat = "Y/m/d") {
	if($strDate == "0000-00-00")
		return "";
	if($strDate == "0000-00-00 00:00:00")
		return "";
	if($strDate == "")
		return "";
	if(!in_array($toFormat, array("Y/m/d", "Y-m-d"))) { // From DB to Other Format    
        if (($timestamp = strtotime($strDate)) === false)
            return false;
        else
            return date($toFormat, $timestamp);
    } else {    // To DB Format 
		//2011-03-04, added support for datetime
		$parts = explode(" ",$strDate,2);
		$time = '';
		if(isset($parts[2])){
			$time = date(" H:i:s",strtotime($strDate));
		}
        // Test For Format dd/mm/YYYY, dd-mm-YYYY, dd.mm.YYYY
        $testFormat = '/(\d\d?)[-|.|\/](\d\d?)[-|.|\/](\d\d\d\d)$/';

        if (preg_match($testFormat, $strDate, $aMatches) !== false) {
            if(checkdate($aMatches[2], $aMatches[1], $aMatches[3]))
                return date("{$aMatches[3]}-{$aMatches[2]}-{$aMatches[1]}").$time;
            else
                return false;                                     
        } else {
            return false;
        }        
    }
}

function parseFloat($ptString) {
	if (strlen($ptString) == 0)
		return false;       
   
	$pString = str_replace(" ", "", $ptString);
   
	if (substr_count($pString, ",") > 1)
		$pString = str_replace(",", "", $pString);
   
	if (substr_count($pString, ".") > 1)
		$pString = str_replace(".", "", $pString);
   
	$pregResult = array();

	$commaset = strpos($pString,',');
	if ($commaset === false) {$commaset = -1;}

	$pointset = strpos($pString,'.');
	if ($pointset === false) {$pointset = -1;}

	$pregResultA = array();
	$pregResultB = array();

	if ($pointset < $commaset) {
		preg_match('#(([-]?[0-9]+(\.[0-9])?)+(,[0-9]+)?)#', $pString, $pregResultA);
	}
	preg_match('#(([-]?[0-9]+(,[0-9])?)+(\.[0-9]+)?)#', $pString, $pregResultB);
	if ((isset($pregResultA[0]) && (!isset($pregResultB[0])
			|| strstr($preResultA[0],$pregResultB[0]) == 0
			|| !$pointset))) {
		$numberString = $pregResultA[0];
		$numberString = str_replace('.','',$numberString);
		$numberString = str_replace(',','.',$numberString);
	}
	elseif (isset($pregResultB[0]) && (!isset($pregResultA[0])
			|| strstr($pregResultB[0],$preResultA[0]) == 0
			|| !$commaset)) {
		$numberString = $pregResultB[0];
		$numberString = str_replace(',','',$numberString);
	}
	else {
		return false;
	}
	$result = (float)$numberString;
	return $result;
}

function sendMail($address, $subject, $content, $attachments=array(), $isSMTP=false, $from="cali@latinoaustralia.com", $from_name="LAE Cali") {
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
             
    try {
        if($isSMTP) {
            $mail->IsSMTP(); 
            //$mail->Mailer        = "smtp";
            //$mail->Host          = "ssl://smtp.gmail.com";
            $mail->SMTPDebug     = 2; 
            $mail->SMTPAuth      = true;                  
            $mail->SMTPKeepAlive = true;
            $mail->SMTPSecure    = "ssl";                   
            $mail->Host          = "smtp.gmail.com"; 
            $mail->Port          = 465;                    
            $mail->Username      = "jalvarado2@latinoaustralia.com"; 
            $mail->Password      = "jalvarado12";  
        }
        
        $mail->SetFrom($from, $from_name);
        $mail->AddReplyTo($from, $from_name);
        
        $mail->AddBCC("jalvarado@latinoaustralia.com", "Julian Andres Alvarado");
        $mail->AddAddress($address);
        $mail->Subject = $subject;
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
        $mail->MsgHTML($content);
        
        if(!empty($attachments)) {
            foreach($attachments as  $file)
                $mail->AddAttachment($file);
        }
        
        if(!$mail->Send()) {
          die("Mailer Error: " . $mail->ErrorInfo);
          return false;
        }
        else 
          return true;
    }
    catch(phpmailerException $e) {
        echo $e->errorMessage();
    }
    catch (Exception $e) {
      echo $e->getMessage();
    }
} 

function getMimeLinux($filePath) {
    if(!file_exists($filePath))
        return false;
    
    $rs = system("file -bi '$filePath'");
    $aParts = explode(";", $rs);
    return $aParts[0];
}

function getMime($filename){
    preg_match("/\.(.*?)$/", $filename, $m);    # Get File extension for a better match                
    $m[1] = str_replace(".", "", $m[1]);
    $m[1] = trim($m[1]);
    //print_r($m);
    
    switch(strtolower($m[1])) {            
        case "js" :
            return "application/x-javascript";

        case "json" :
            return "application/json";

        case "jpg" :
        case "jpeg" :
        case "jpe" :
            return "image/jpg";

        case "png" :
        case "gif" :
        case "bmp" :
        case "tiff" :
            return "image/".strtolower($m[1]);

        case "css" :
            return "text/css";

        case "xml" :
            return "application/xml";

        case "doc" :
        case "docx" :
            return "application/msword";

        case "xls" :
        case "xlt" :
        case "xlm" :
        case "xld" :
        case "xla" :
        case "xlc" :
        case "xlw" :
        case "xll" :
            return "application/vnd.ms-excel";

        case "ppt" :
        case "pps" :
            return "application/vnd.ms-powerpoint";

        case "rtf" :
            return "application/rtf";

        case "pdf" :
            return "application/pdf";

        case "html" :
        case "htm" :
        case "php" :
            return "text/html";

        case "txt" :
            return "text/plain";

        case "mpeg" :
        case "mpg" :
        case "mpe" :
            return "video/mpeg";

        case "mp3" :
            return "audio/mpeg3";

        case "wav" :
            return "audio/wav";

        case "aiff" :
        case "aif" :
            return "audio/aiff";

        case "avi" :
            return "video/msvideo";

        case "wmv" :
            return "video/x-ms-wmv";

        case "mov" :
            return "video/quicktime";

        case "zip" :
            return "application/zip";

        case "tar" :
            return "application/x-tar";

        case "swf" :
            return "application/x-shockwave-flash";                        
        
        default:
            if(function_exists("mime_content_type")){ # if mime_content_type exists use it.
               $m = mime_content_type($filename);
            }else if(function_exists("")){    # if Pecl installed use it
               $finfo = finfo_open(FILEINFO_MIME);
               $m = finfo_file($finfo, $filename);
               finfo_close($finfo);
            }else{    # if nothing left try shell
               if(strstr($_SERVER[HTTP_USER_AGENT], "Windows")){ # Nothing to do on windows
                   return ""; # Blank mime display most files correctly especially images.
               }
               if(strstr($_SERVER[HTTP_USER_AGENT], "Macintosh")){ # Correct output on macs
                   $m = trim(exec('file -b --mime '.escapeshellarg($filename)));
               }else{    # Regular unix systems
                   $m = trim(exec('file -bi '.escapeshellarg($filename)));
               }
            }
            $m = split(";", $m);
            return trim($m[0]);
    }
}  

function check_email($email) {
    if(preg_match('/^\w[-.\w]*@(\w[-._\w]*\.[a-zA-Z]{2,}.*)$/', $email, $matches))
    {
        if(function_exists('checkdnsrr'))
        {
            if(checkdnsrr($matches[1] . '.', 'MX')) return true;
            if(checkdnsrr($matches[1] . '.', 'A')) return true;
        }else{
            if(!empty($hostName))
            {
                if( $recType == '' ) $recType = "MX";
                exec("nslookup -type=$recType $hostName", $result);
                foreach ($result as $line)
                {
                    if(eregi("^$hostName",$line))
                    {
                        return true;
                    }
                }
                return false;
            }
            return false;
        }
    }
    return false;
}

function validate_email($str){ 
	$str = strtolower($str); 
/* Agrega todas las extensiones que quieras
*/
if(preg_match("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$",$str)){ 
	return 1; 
} else { 
	return 0; 
} 
} 


function removeAccents($string) {
 $string = html_entity_decode($string,ENT_COMPAT,"UTF-8");
    $stripthese = ",|~|!|@|%|^|(|)|<|>|:|;|{|}|[|]|&|`|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½";
    $sreplacements = "Š|S, Œ|O, Ž|Z, š|s, œ|oe, ž|z, Ÿ|Y, ¥|Y, µ|u, À|A, Á|A, Â|A, Ã|A, Ä|A, Å|A, Æ|A, Ç|C, È|E, É|E, Ê|E, Ë|E, Ì|I, Í|I, Î|I, Ï|I, Ð|D, Ñ|N, Ò|O, Ó|O, Ô|O, Õ|O, Ö|O, Ø|O, Ù|U, Ú|U, Û|U, Ü|U, Ý|Y, ß|s, à|a, á|a, â|a, ã|a, ä|a, å|a, æ|a, ç|c, è|e, é|e, ê|e, ë|e, ì|i, í|i, î|i, ï|i, ð|o, ñ|n, ò|o, ó|o, ô|o, õ|o, ö|o, ø|o, ù|u, ú|u, û|u, ü|u, ý|y, ÿ|y, ß|ss";

    $replacements = array();
    $items = explode(',', $sreplacements);
    foreach ($items as $item) {
        if (!empty($item)) { //  better protection. Returns null array if empty
            @list($src, $dst) = explode('|', trim($item));
            $replacements[trim($src)] = trim($dst);
        }
    }
    $string = strtr(utf8_decode($string), $replacements);
 $stripCharList = explode('|', $stripthese);
    $string = str_replace($stripCharList, '', $string);
 return $string;
}

function seoUrl($url) {
    /*
    Funcion que deja purita un cadena, como para una SEF URL
    */
 $url = html_entity_decode($url,ENT_COMPAT,"UTF-8");
    $stripthese = ",|~|!|@|%|^|(|)|<|>|:|;|{|}|[|]|&|`|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½|ï¿½";
    $sreplacements = "Š|S, Œ|O, Ž|Z, š|s, œ|oe, ž|z, Ÿ|Y, ¥|Y, µ|u, À|A, Á|A, Â|A, Ã|A, Ä|A, Å|A, Æ|A, Ç|C, È|E, É|E, Ê|E, Ë|E, Ì|I, Í|I, Î|I, Ï|I, Ð|D, Ñ|N, Ò|O, Ó|O, Ô|O, Õ|O, Ö|O, Ø|O, Ù|U, Ú|U, Û|U, Ü|U, Ý|Y, ß|s, à|a, á|a, â|a, ã|a, ä|a, å|a, æ|a, ç|c, è|e, é|e, ê|e, ë|e, ì|i, í|i, î|i, ï|i, ð|o, ñ|n, ò|o, ó|o, ô|o, õ|o, ö|o, ø|o, ù|u, ú|u, û|u, ü|u, ý|y, ÿ|y, ß|ss";

    $replacements = array();
    $items = explode(',', $sreplacements);
    foreach ($items as $item) {
        if (!empty($item)) { //  better protection. Returns null array if empty
            @list($src, $dst) = explode('|', trim($item));
            $replacements[trim($src)] = trim($dst);
        }
    }
    $url = strtr(utf8_decode($url), $replacements);
 $stripCharList = explode('|', $stripthese);
    $url = str_replace($stripCharList, '', $url);
    //refinar mas la url
    $nurl = '';
    for ($i = 0; $i < strlen($url); $i++) {
        $rchr = $url{$i};
        $chr = ord($rchr); //ASCII
        if ((!($chr <= 47 || ($chr >= 58 && $chr <= 64) || ($chr >= 91 && $chr <= 96) ||
            $chr >= 123)) || $chr == 32 || $rchr == '-' || $rchr == '/') { //Escrito a mano limpia
            $nurl .= $rchr;
        } //if

    } //for
    $nurl = str_replace(' ', "-", $nurl);
    //Eliminar todos los dobles "-"
    while (strstr($nurl, "--")) {
        $nurl = str_replace("--", "-", $nurl);
    }
    return htmlentities($nurl, ENT_COMPAT, "UTF-8");
}

function redirect($url, $params) {
    header("Location: $url?".http_build_query($params));
}

function getIp(){
	$ipAddr = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER["REMOTE_ADDR"];
	//fix multiple
	$tmp =  explode(",",$ipAddr);
	$ipAddr = array_shift($tmp);
	return $ipAddr;
}

function getTableUserFull(){
	global $session;
	$userTable = array(
			"ip_address" => getIP(),
			"owner_user_id"=>$session->userInfo["user_id"],
			"updater_user_id"=>$session->userInfo["user_id"],
			"created_at"=>date("Y-m-d H:i:s"),
			"updated_at"=>date("Y-m-d H:i:s")
	);
	return $userTable;
}
function getTableUserUpdate(){
	global $session;
	$userTable = array(
			"ip_address" => getIP(),
			"updater_user_id"=>$session->userInfo["user_id"],
			"updated_at"=>date("Y-m-d H:i:s")
	);
	return $userTable;
}
/*
function money_format($format, $number) 
{ 
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'. 
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/'; 
    if (setlocale(LC_MONETARY, 0) == 'C') { 
        setlocale(LC_MONETARY, ''); 
    } 
    $locale = localeconv(); 
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER); 
    foreach ($matches as $fmatch) { 
        $value = floatval($number); 
        $flags = array( 
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? 
                           $match[1] : ' ', 
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0, 
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? 
                           $match[0] : '+', 
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0, 
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0 
        ); 
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0; 
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0; 
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits']; 
        $conversion = $fmatch[5]; 

        $positive = true; 
        if ($value < 0) { 
            $positive = false; 
            $value  *= -1; 
        } 
        $letter = $positive ? 'p' : 'n'; 

        $prefix = $suffix = $cprefix = $csuffix = $signal = ''; 

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign']; 
        switch (true) { 
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+': 
                $prefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+': 
                $suffix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+': 
                $cprefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+': 
                $csuffix = $signal; 
                break; 
            case $flags['usesignal'] == '(': 
            case $locale["{$letter}_sign_posn"] == 0: 
                $prefix = '('; 
                $suffix = ')'; 
                break; 
        } 
        if (!$flags['nosimbol']) { 
            $currency = $cprefix . 
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . 
                        $csuffix; 
        } else { 
            $currency = ''; 
        } 
        $space  = $locale["{$letter}_sep_by_space"] ? ' ' : ''; 

        $value = number_format($value, $right, $locale['mon_decimal_point'], 
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']); 
        $value = @explode($locale['mon_decimal_point'], $value); 

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]); 
        if ($left > 0 && $left > $n) { 
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0]; 
        } 
        $value = implode($locale['mon_decimal_point'], $value); 
        if ($locale["{$letter}_cs_precedes"]) { 
            $value = $prefix . $currency . $space . $value . $suffix; 
        } else { 
            $value = $prefix . $value . $space . $currency . $suffix; 
        } 
        if ($width > 0) { 
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? 
                     STR_PAD_RIGHT : STR_PAD_LEFT); 
        } 

        $format = str_replace($fmatch[0], $value, $format); 
    } 
    return $format; 
}
*/
function showMoney($float,$currencyIn = "AUD", $currencyTo = ''){
	if($currencyTo == '')
		$currencyTo = $currencyIn;
	/*if(function_exists("money_format")===TRUE){
		$money = money_format('%i', $float);
	}*/
	$money = exchangeRate($float, $currencyIn, $currencyTo);
	$money = number_format($money,2,".",",");
	$money = $currencyTo.$money;
	return $money;
}
?>
