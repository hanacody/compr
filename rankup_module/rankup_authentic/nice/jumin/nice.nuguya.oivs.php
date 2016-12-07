<?php

///	#############################################################################
///	#####
///	#####	한국신용정보주식회사								PHP Script 소스
///	#####
///	#####	=====================================================================
///	#####
///	#####	Descriptions
///	#####		- Web Service Call & Security
///	#####
///	#####	---------------------------------------------------------------------
///	#####
///	#####	작성자			: (주)한국신용정보 (www.nice.co.kr)
///	#####	원본참조		:
///	#####	원본파일		:
///	#####	작성일자		: 2006.10.10
///	#####
///	#############################################################################

$strDelimeter				= "!#!";
$strDataDelimeter 			= "!@!";
$strPersonal 				= "0";
$strForeigner 				= "2";
$strCorperate 				= "1";
$CRNDSIZE 					= 24;

$strDomain					= "secure.nuguya.com";
$strPort					= "80";

$strPingUrl					= "http://" . $strDomain . ":" . $strPort . "/nuguya/rlnmPing.do";
$strSecureServiceUrl		= "http://" . $strDomain . ":" . $strPort . "/nuguya/ScriptProxyServlet?service=SecureService&type=PHP";
$strServiceUrl				= "http://" . $strDomain . ":" . $strPort . "/nuguya/ScriptProxyServlet?service=RealNameService&type=PHP";

//	-----------------------------------------------------
//	-----	Web Service Classes (Proxy)
//	-----------------------------------------------------

$proxies = new proxies();

//
//	Service Call Class
//
class WSService
{
	var $service;
	var $fname;
	var $params;
	var $values;
	var $rtype;

	function WSService() {}

	function serviceCall( $arg )
	{
		global $proxies;
		$this->values = (array) $arg;
		return ( $proxies->callSoap( $this ) );
	}

	function getService()
	{
		global $proxies;
		return ( $proxies->callProxy( $this ) );
	}

	function getPing()
	{
		global $proxies;
		$proxies->callPing( $this );
		return ( $proxies->returnValue );
	}
}

//
//	Proxy Class
//
class proxies
{
	var $returnValue;
	var $errorCode;
	var $errorMsg;
	var $xmldom;

	function proxies() {}

	function makeResponse( $msg, $back )
	{
		echo( '<script language="javascript">' );
		echo( "alert( \"" . $msg . "\" ); " );
		echo( "history.go(" . $back . "); " );
		echo( "</script>" );
		die();
	}

	//
	//	Transfer-Encoding : Chunked (Decoding)
	//
	function decodeChunked( $buffer )
	{
		$length = 0;
		$new = '';

		$chunkend = strpos( $buffer, "\r\n" ) + 2;
		$temp = substr( $buffer, 0, $chunkend );
		$chunk_size = hexdec( trim( $temp ) );
		$chunkstart = $chunkend;

		while( $chunk_size > 0 )
		{
			$chunkend = strpos( $buffer, "\r\n", $chunkstart + $chunk_size );

			if ( $chunkend == FALSE )
			{
				$chunk = substr( $buffer, $chunkstart );
				$new .= $chunk;
				$length += strlen( $chunk );
				break;
			}

			$chunk = substr( $buffer, $chunkstart, $chunkend - $chunkstart );
			$new .= $chunk;
			$length += strlen( $chunk );

			$chunkstart = $chunkend + 2;

			$chunkend = strpos( $buffer, "\r\n", $chunkstart ) + 2;
			if ( $chunkend == FALSE ) break;

			$temp = substr( $buffer, $chunkstart, $chunkend - $chunkstart );
			$chunk_size = hexdec( trim( $temp ) );
			$chunkstart = $chunkend;
		}

		return $new;
	}

	//
	//	Removing Header and Return Contents
	//
	function resolveResponseData( $buffer )
	{
		$data = $buffer . "\r\n\r\n\r\n\r\n";

		//	Remove 100 Header
		if ( ereg( '^HTTP/1,1 100', $data ) )
		{
			if ( $pos = strpos( $data, "\r\n\r\n" ) )
			{
				$data = ltrim( substr( $data, $pos ) );
			}
			elseif ( $pos = strpos( $data, "\r\n" ) )
			{
				$data = ltrim( substr( $data, $pos ) );
			}
		}

		//	Separate Content from Header
		if ( $pos = strpos( $data, "\r\n\r\n" ) )
		{
			$lb = "\r\n";
		}
		elseif ( $pos = strpos($data,"\n\n") )
		{
			$lb = "\n";
		}
		else
		{
			return false;
		}

		$header_data = trim( substr( $data, 0, $pos ) );
		$header_array = explode( $lb, $header_data );
		$data = ltrim( substr( $data, $pos ) );

		//	Clean Header
		if ( count( $header_array ) > 0 )
		{
			if ( ! strpos( $header_array[0], "200" ) )
			{
				$header_error = split( " ", $header_array[0] );
				$this->returnValue = null;
				$this->errorCode = $header_error[1];
				$this->errorMsg = "한국신용정보 서비스에서 수신한 정보가 올바르지 않습니다\\n\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오";
				return false;
			}
		}
		else
		{
			$this->returnValue = null;
			$this->errorCode = -1;
			$this->errorMsg = "한국신용정보 서비스에서 수신한 정보가 올바르지 않습니다\\n\\상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오";
			return false;
		}

		foreach( $header_array as $header_line )
		{
			$arr = explode( ':', $header_line );
			if ( count( $arr ) >= 2 )
				$headers[ trim( $arr[0] ) ] = trim( $arr[1] );
		}

		// decode transfer-encoding
		if ( isset( $headers['Transfer-Encoding'] ) && $headers['Transfer-Encoding'] == 'chunked')
		{
			if ( ! $data = $this->decodeChunked( $data ) )
			{
				$this->errorCode = -1;
				$this->errorMsg = "한국신용정보 서비스에서 수신한 정보를 Chunked Decode할 수 없습니다\\n\\n서비스 점검중일 수 있습니다\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오";
				return false;
			}
		}

		//	decode content-encoding
		if ( isset( $headers['Content-Encoding'] ) && $headers[ 'Content-Encoding' ] != '' )
		{
			if ( $headers['Content-Encoding'] == 'deflate' || $headers['Content-Encoding'] == 'gzip' )
			{
    			if ( function_exists( 'gzinflate' ) )
    			{
					if ( $headers['Content-Encoding'] == 'deflate' && $degzdata = @gzinflate( $data ) )
    					$data = $degzdata;
					elseif ( $headers['Content-Encoding'] == 'gzip' && $degzdata = gzinflate( substr( $data, 10 ) ) )
						$data = $degzdata;
					else
					{
						$this->errorCode = -1;
						$this->errorMsg = "한국신용정보 서비스에서 수신한 정보를 Decode할 수 없습니다\\n\\n서비스 점검중일 수 있습니다\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오";
						return false;
					}
    			}
    			else
    			{
    				$this->errorCode = -1;
					$this->errorMsg = "한국신용정보 서비스에서 수신한 정보를 압축해제할 수 없습니다\\n\\nZlib extension을 컴파일하여 사용할 수 있도록 설정하여야 합니다";
					return false;
				}
			}
		}

		if ( strlen( $data ) == 0 )
		{
			$this->errorCode = -1;
			$this->errorMsg = "한국신용정보 서비스에서 수신한 정보가 존재하지 않습니다\\n\\n서비스 점검중일 수 있습니다\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오";
			return false;
		}

		return $data;
	}

	function callTransport( $domain, $port, $reqest )
	{
		$sock = null;

		$sock = @fsockopen( $domain, (int) $port, $errno, $errstr, 10 );
		if ( ! $sock )
		{
			 $this->errorCode = -1;
			 $this->errorMsg = "한국신용정보 서비스에 접속할 수 없습니다.\\n\\n서비스 점검중일 수 있습니다.\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오." . "\\n\\n[오류코드]\\n $errstr( $errno )";
		}

		fwrite( $sock, $reqest );

		// Get Response Data
		$data = "";
		$respData = "";
		while ( !feof( $sock ) )
		{
			$data = fgets( $sock, 32768 );
			if ( $data == "0\r\n" ) break;
			$respData .= $data;
		}

		fclose( $sock );

		if ( $respData == '' )
		{
			$this->errorCode = -1;
			$this->errorMsg = "한국신용정보 서비스에서 응답을 받지 못하였습니다.\\n\\n서비스 점검중일 수 있습니다.\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오.";
			return false;
		}
		if ( ! $respData = $this->resolveResponseData( $respData ) ) return false;

		$this->returnValue = $respData;
		$this->errorCode = "";
		$this->errorMsg = "";

		return true;
	}

	//
	//	Get Proxy Info Call
	//
	function callProxy( $arg )
	{
		$p = $arg;

		$reqData = "";
		$reqData .= "GET " . str_replace( "http://" . $p->service->domain . ":" . $p->service->port, "", $p->service->url ) . " HTTP/1.1\r\n";
		$reqData .= "Host: " . $p->service->domain . ":" . $p->service->port . "\r\n";
		$reqData .= "Content-Type: text/xml; charset=utf-8\r\n";
		$reqData .= "Connection: close\r\n";
		$reqData .= "\r\n";

		if ( ! $this->callTransport( $p->service->domain, (int) $p->service->port, $reqData ) ) $this->makeResponse( $this->errorMsg, -1 );
		if ( $this->errorCode != "" ) $this->makeResponse( $this->errorMsg, -1 );
		if ( $this->returnValue == "" ) $this->makeResponse( "한국신용정보 서비스에서 응답을 받지 못하였습니다.[nice.nuguya.oivs : callProxy] \\n\\n서비스 점검중일 수 있습니다.\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오.", -1 );

		return true;
	}

	//
	//	Service Call ( like Ping )
	//
	function callPing( $arg )
	{
		$p = $arg;

		$reqData = "";
		$reqData .= "GET " . str_replace( "http://" . $p->service->domain . ":" . $p->service->port, "", $p->service->url ) . " HTTP/1.1\r\n";
		$reqData .= "Host: " . $p->service->domain . ":" . $p->service->port . "\r\n";
		$reqData .= "Content-Type: text/xml; charset=euc-kr\r\n";
		$reqData .= "Connection: close\r\n";
		$reqData .= "\r\n";

		if ( ! $this->callTransport( $p->service->domain, (int) $p->service->port, $reqData ) ) $this->makeResponse( "한국신용정보 서비스에서 필요한 정보를 수신하지 못하였습니다. [nice.nuguya.oivs : callPing]\\n\\n서비스 점검중일 수 있습니다.\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오.", -1 );
		if ( $this->errorCode != "" ) $this->makeResponse( "한국신용정보 서비스에서 필요한 정보를 수신하지 못하였습니다. [nice.nuguya.oivs : callPing]\\n\\n서비스 점검중일 수 있습니다.\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오.", -1 );

		return true;
	}

	//
	//	Web Service Call
	//
	function callSoap( $arg )
	{
		$p = $arg;

		$sendMsg = "<?xml version='1.0' encoding='utf-8'?>";
		$sendMsg = $sendMsg . "<soap:Envelope xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>";
		$sendMsg = $sendMsg . "<soap:Body>";
		$sendMsg = $sendMsg . "<";
		$sendMsg = $sendMsg . $p->fname;
		$sendMsg = $sendMsg . " xmlns=\"\">";

		// parameters
  		for( $n = 0; $n < count( $p->params ); $n++ )
  		{
  			$val = $p->params[$n];
        	$sendMsg = $sendMsg . "<";
        	$sendMsg = $sendMsg . $p->params[$n];
        	$sendMsg = $sendMsg . ">";
        	$sendMsg = $sendMsg . trim( $p->values[$n] );
        	$sendMsg = $sendMsg . "</";
        	$sendMsg = $sendMsg . $p->params[$n];
        	$sendMsg = $sendMsg . ">";
  		}

	  	// envelope end
	  	$sendMsg .= "</" . $p->fname . ">";
	  	$sendMsg .= "</soap:Body>";
	  	$sendMsg .= "</soap:Envelope>";

		$sendMsg = stripslashes( $sendMsg );

		$reqData = "";
		$reqData .= "POST " . str_replace( "http://" . $p->service->domain . ":" . $p->service->port, "", $p->service->url ) . " HTTP/1.1\r\n";
		$reqData .= "Host: {$p->service->domain}:{$p->service->port}\r\n";
		$reqData .= "SOAPAction: \"\"\r\n";
		$reqData .= "Content-Type: text/xml; charset=utf-8\r\n";
		$reqData .= "Content-Length: " . strlen( $sendMsg ). "\r\n";
		$reqData .= "Accept: */*\r\n";
		$reqData .= "\r\n";
		$reqData .= $sendMsg . "\r\n";
		$reqData .= "\r\n";

		if ( ! $this->callTransport( $p->service->domain, (int) $p->service->port, $reqData ) ) $this->makeResponse( $this->errorMsg, -1 );
		if ( $this->errorCode != "" ) $this->makeResponse( $this->errorMsg, -1 );
		if ( $this->returnValue == "" ) $this->makeResponse( "한국신용정보 서비스에서 응답을 받지 못하였습니다.[nice.nuguya.oivs : callSoap] \\n\\n서비스 점검중일 수 있습니다.\\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오.", -1 );

		$this->returnValue = strip_tags( $this->returnValue );
		$this->errorCode = "";
		$this->errorMsg = "";
		return true;
	}
}

//	-----------------------------------------------------
//	-----	Security Classes
//	-----------------------------------------------------

$cryptoObject = new CryptoObject();

class CryptoObject
{
	//
	//	Triple-DES Symmetric Key Algorithm
	//
	function des( $key, $message, $encrypt, $mode, $iv )
	{
		//	declaring this locally speeds things up a bit
		$spfunction1 = array (0x1010400,0,0x10000,0x1010404,0x1010004,0x10404,0x4,0x10000,0x400,0x1010400,0x1010404,0x400,0x1000404,0x1010004,0x1000000,0x4,0x404,0x1000400,0x1000400,0x10400,0x10400,0x1010000,0x1010000,0x1000404,0x10004,0x1000004,0x1000004,0x10004,0,0x404,0x10404,0x1000000,0x10000,0x1010404,0x4,0x1010000,0x1010400,0x1000000,0x1000000,0x400,0x1010004,0x10000,0x10400,0x1000004,0x400,0x4,0x1000404,0x10404,0x1010404,0x10004,0x1010000,0x1000404,0x1000004,0x404,0x10404,0x1010400,0x404,0x1000400,0x1000400,0,0x10004,0x10400,0,0x1010004);
		$spfunction2 = array (-0x7fef7fe0,-0x7fff8000,0x8000,0x108020,0x100000,0x20,-0x7fefffe0,-0x7fff7fe0,-0x7fffffe0,-0x7fef7fe0,-0x7fef8000,-0x80000000,-0x7fff8000,0x100000,0x20,-0x7fefffe0,0x108000,0x100020,-0x7fff7fe0,0,-0x80000000,0x8000,0x108020,-0x7ff00000,0x100020,-0x7fffffe0,0,0x108000,0x8020,-0x7fef8000,-0x7ff00000,0x8020,0,0x108020,-0x7fefffe0,0x100000,-0x7fff7fe0,-0x7ff00000,-0x7fef8000,0x8000,-0x7ff00000,-0x7fff8000,0x20,-0x7fef7fe0,0x108020,0x20,0x8000,-0x80000000,0x8020,-0x7fef8000,0x100000,-0x7fffffe0,0x100020,-0x7fff7fe0,-0x7fffffe0,0x100020,0x108000,0,-0x7fff8000,0x8020,-0x80000000,-0x7fefffe0,-0x7fef7fe0,0x108000);
		$spfunction3 = array (0x208,0x8020200,0,0x8020008,0x8000200,0,0x20208,0x8000200,0x20008,0x8000008,0x8000008,0x20000,0x8020208,0x20008,0x8020000,0x208,0x8000000,0x8,0x8020200,0x200,0x20200,0x8020000,0x8020008,0x20208,0x8000208,0x20200,0x20000,0x8000208,0x8,0x8020208,0x200,0x8000000,0x8020200,0x8000000,0x20008,0x208,0x20000,0x8020200,0x8000200,0,0x200,0x20008,0x8020208,0x8000200,0x8000008,0x200,0,0x8020008,0x8000208,0x20000,0x8000000,0x8020208,0x8,0x20208,0x20200,0x8000008,0x8020000,0x8000208,0x208,0x8020000,0x20208,0x8,0x8020008,0x20200);
		$spfunction4 = array (0x802001,0x2081,0x2081,0x80,0x802080,0x800081,0x800001,0x2001,0,0x802000,0x802000,0x802081,0x81,0,0x800080,0x800001,0x1,0x2000,0x800000,0x802001,0x80,0x800000,0x2001,0x2080,0x800081,0x1,0x2080,0x800080,0x2000,0x802080,0x802081,0x81,0x800080,0x800001,0x802000,0x802081,0x81,0,0,0x802000,0x2080,0x800080,0x800081,0x1,0x802001,0x2081,0x2081,0x80,0x802081,0x81,0x1,0x2000,0x800001,0x2001,0x802080,0x800081,0x2001,0x2080,0x800000,0x802001,0x80,0x800000,0x2000,0x802080);
		$spfunction5 = array (0x100,0x2080100,0x2080000,0x42000100,0x80000,0x100,0x40000000,0x2080000,0x40080100,0x80000,0x2000100,0x40080100,0x42000100,0x42080000,0x80100,0x40000000,0x2000000,0x40080000,0x40080000,0,0x40000100,0x42080100,0x42080100,0x2000100,0x42080000,0x40000100,0,0x42000000,0x2080100,0x2000000,0x42000000,0x80100,0x80000,0x42000100,0x100,0x2000000,0x40000000,0x2080000,0x42000100,0x40080100,0x2000100,0x40000000,0x42080000,0x2080100,0x40080100,0x100,0x2000000,0x42080000,0x42080100,0x80100,0x42000000,0x42080100,0x2080000,0,0x40080000,0x42000000,0x80100,0x2000100,0x40000100,0x80000,0,0x40080000,0x2080100,0x40000100);
		$spfunction6 = array (0x20000010,0x20400000,0x4000,0x20404010,0x20400000,0x10,0x20404010,0x400000,0x20004000,0x404010,0x400000,0x20000010,0x400010,0x20004000,0x20000000,0x4010,0,0x400010,0x20004010,0x4000,0x404000,0x20004010,0x10,0x20400010,0x20400010,0,0x404010,0x20404000,0x4010,0x404000,0x20404000,0x20000000,0x20004000,0x10,0x20400010,0x404000,0x20404010,0x400000,0x4010,0x20000010,0x400000,0x20004000,0x20000000,0x4010,0x20000010,0x20404010,0x404000,0x20400000,0x404010,0x20404000,0,0x20400010,0x10,0x4000,0x20400000,0x404010,0x4000,0x400010,0x20004010,0,0x20404000,0x20000000,0x400010,0x20004010);
		$spfunction7 = array (0x200000,0x4200002,0x4000802,0,0x800,0x4000802,0x200802,0x4200800,0x4200802,0x200000,0,0x4000002,0x2,0x4000000,0x4200002,0x802,0x4000800,0x200802,0x200002,0x4000800,0x4000002,0x4200000,0x4200800,0x200002,0x4200000,0x800,0x802,0x4200802,0x200800,0x2,0x4000000,0x200800,0x4000000,0x200800,0x200000,0x4000802,0x4000802,0x4200002,0x4200002,0x2,0x200002,0x4000000,0x4000800,0x200000,0x4200800,0x802,0x200802,0x4200800,0x802,0x4000002,0x4200802,0x4200000,0x200800,0,0x2,0x4200802,0,0x200802,0x4200000,0x800,0x4000002,0x4000800,0x800,0x200002);
		$spfunction8 = array (0x10001040,0x1000,0x40000,0x10041040,0x10000000,0x10001040,0x40,0x10000000,0x40040,0x10040000,0x10041040,0x41000,0x10041000,0x41040,0x1000,0x40,0x10040000,0x10000040,0x10001000,0x1040,0x41000,0x40040,0x10040040,0x10041000,0x1040,0,0,0x10040040,0x10000040,0x10001000,0x41040,0x40000,0x41040,0x40000,0x10041000,0x1000,0x40,0x10040040,0x1000,0x41040,0x10001000,0x40,0x10000040,0x10040000,0x10040040,0x10000000,0x40000,0x10001040,0,0x10041040,0x40040,0x10000040,0x10040000,0x10001000,0x10001040,0,0x10041040,0x41000,0x41000,0x1040,0x1040,0x40040,0x10000000,0x10041000);
		$masks = array (4294967295,2147483647,1073741823,536870911,268435455,134217727,67108863,33554431,16777215,8388607,4194303,2097151,1048575,524287,262143,131071,65535,32767,16383,8191,4095,2047,1023,511,255,127,63,31,15,7,3,1,0);

		//	create the 16 or 48 subkeys we will need
  		$keys = $this->des_createKeys ($key);
  		$m=0;
  		$len = strlen($message);
  		$chunk = 0;

  		//	set up the loops for single and triple des
  		$iterations = ((count($keys) == 32) ? 3 : 9); //single or triple des
  		if ($iterations == 3) {$looping = (($encrypt) ? array (0, 32, 2) : array (30, -2, -2));}
  		else {$looping = (($encrypt) ? array (0, 32, 2, 62, 30, -2, 64, 96, 2) : array (94, 62, -2, 32, 64, 2, 30, -2, -2));}

  		$message .= (chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0)); //pad the message out with null bytes

  		//	store the result here
  		$result = "";
  		$tempresult = "";

  		if ($mode == 1)
  		{
  			//CBC mode
    		$cbcleft = (ord($iv{$m++}) << 24) | (ord($iv{$m++}) << 16) | (ord($iv{$m++}) << 8) | ord($iv{$m++});
    		$cbcright = (ord($iv{$m++}) << 24) | (ord($iv{$m++}) << 16) | (ord($iv{$m++}) << 8) | ord($iv{$m++});
    		$m=0;
  		}

  		//	loop through each 64 bit chunk of the message
  		while ($m < $len)
  		{
    		$left = (ord($message{$m++}) << 24) | (ord($message{$m++}) << 16) | (ord($message{$m++}) << 8) | ord($message{$m++});
    		$right = (ord($message{$m++}) << 24) | (ord($message{$m++}) << 16) | (ord($message{$m++}) << 8) | ord($message{$m++});

    		//	for Cipher Block Chaining mode, xor the message with the previous result
    		if ($mode == 1) {if ($encrypt) {$left ^= $cbcleft; $right ^= $cbcright;} else {$cbcleft2 = $cbcleft; $cbcright2 = $cbcright; $cbcleft = $left; $cbcright = $right;}}

    		//	first each 64 but chunk of the message must be permuted according to IP
    		$temp = (($left >> 4 & $masks[4]) ^ $right) & 0x0f0f0f0f; $right ^= $temp; $left ^= ($temp << 4);
    		$temp = (($left >> 16 & $masks[16]) ^ $right) & 0x0000ffff; $right ^= $temp; $left ^= ($temp << 16);
    		$temp = (($right >> 2 & $masks[2]) ^ $left) & 0x33333333; $left ^= $temp; $right ^= ($temp << 2);
    		$temp = (($right >> 8 & $masks[8]) ^ $left) & 0x00ff00ff; $left ^= $temp; $right ^= ($temp << 8);
    		$temp = (($left >> 1 & $masks[1]) ^ $right) & 0x55555555; $right ^= $temp; $left ^= ($temp << 1);

    		$left = (($left << 1) | ($left >> 31 & $masks[31]));
    		$right = (($right << 1) | ($right >> 31 & $masks[31]));

    		//	do this either 1 or 3 times for each chunk of the message
    		for ($j=0; $j<$iterations; $j+=3)
    		{
      			$endloop = $looping[$j+1];
      			$loopinc = $looping[$j+2];

				//	now go through and perform the encryption or decryption
      			for ($i=$looping[$j]; $i!=$endloop; $i+=$loopinc)
      			{
      				//	for efficiency
        			$right1 = $right ^ $keys[$i];
        			$right2 = (($right >> 4 & $masks[4]) | ($right << 28)) ^ $keys[$i+1];
        			//	the result is attained by passing these bytes through the S selection functions
        			$temp = $left;
        			$left = $right;
        			$right = $temp ^ ($spfunction2[($right1 >> 24 & $masks[24]) & 0x3f] | $spfunction4[($right1 >> 16 & $masks[16]) & 0x3f]
              			| $spfunction6[($right1 >>  8 & $masks[8]) & 0x3f] | $spfunction8[$right1 & 0x3f]
              			| $spfunction1[($right2 >> 24 & $masks[24]) & 0x3f] | $spfunction3[($right2 >> 16 & $masks[16]) & 0x3f]
              			| $spfunction5[($right2 >>  8 & $masks[8]) & 0x3f] | $spfunction7[$right2 & 0x3f]);
      			}

      			$temp = $left; $left = $right; $right = $temp; //unreverse left and right
    		} //	for either 1 or 3 iterations

    		//	move then each one bit to the right
    		$left = (($left >> 1 & $masks[1]) | ($left << 31));
    		$right = (($right >> 1 & $masks[1]) | ($right << 31));

    		//	now perform IP-1, which is IP in the opposite direction
    		$temp = (($left >> 1 & $masks[1]) ^ $right) & 0x55555555; $right ^= $temp; $left ^= ($temp << 1);
    		$temp = (($right >> 8 & $masks[8]) ^ $left) & 0x00ff00ff; $left ^= $temp; $right ^= ($temp << 8);
    		$temp = (($right >> 2 & $masks[2]) ^ $left) & 0x33333333; $left ^= $temp; $right ^= ($temp << 2);
    		$temp = (($left >> 16 & $masks[16]) ^ $right) & 0x0000ffff; $right ^= $temp; $left ^= ($temp << 16);
    		$temp = (($left >> 4 & $masks[4]) ^ $right) & 0x0f0f0f0f; $right ^= $temp; $left ^= ($temp << 4);

    		//	for Cipher Block Chaining mode, xor the message with the previous result
    		if ($mode == 1) {if ($encrypt) {$cbcleft = $left; $cbcright = $right;} else {$left ^= $cbcleft2; $right ^= $cbcright2;}}
    		$tempresult .= (chr($left>>24 & $masks[24]) . chr(($left>>16 & $masks[16]) & 0xff) . chr(($left>>8 & $masks[8]) & 0xff) . chr($left & 0xff) . chr($right>>24 & $masks[24]) . chr(($right>>16 & $masks[16]) & 0xff) . chr(($right>>8 & $masks[8]) & 0xff) . chr($right & 0xff));

    		$chunk += 8;
    		if ($chunk == 512) {$result .= $tempresult; $tempresult = ""; $chunk = 0;}
  		} //for every 8 characters, or 64 bits in the message

  		//	return the result as an array
  		return ($result . $tempresult);
	} //	end of des

	//
	//	Triple-DES Key Generating
	//
	function des_createKeys( $key )
	{
		//	declaring this locally speeds things up a bit
  		$pc2bytes0  = array (0,0x4,0x20000000,0x20000004,0x10000,0x10004,0x20010000,0x20010004,0x200,0x204,0x20000200,0x20000204,0x10200,0x10204,0x20010200,0x20010204);
  		$pc2bytes1  = array (0,0x1,0x100000,0x100001,0x4000000,0x4000001,0x4100000,0x4100001,0x100,0x101,0x100100,0x100101,0x4000100,0x4000101,0x4100100,0x4100101);
  		$pc2bytes2  = array (0,0x8,0x800,0x808,0x1000000,0x1000008,0x1000800,0x1000808,0,0x8,0x800,0x808,0x1000000,0x1000008,0x1000800,0x1000808);
  		$pc2bytes3  = array (0,0x200000,0x8000000,0x8200000,0x2000,0x202000,0x8002000,0x8202000,0x20000,0x220000,0x8020000,0x8220000,0x22000,0x222000,0x8022000,0x8222000);
  		$pc2bytes4  = array (0,0x40000,0x10,0x40010,0,0x40000,0x10,0x40010,0x1000,0x41000,0x1010,0x41010,0x1000,0x41000,0x1010,0x41010);
  		$pc2bytes5  = array (0,0x400,0x20,0x420,0,0x400,0x20,0x420,0x2000000,0x2000400,0x2000020,0x2000420,0x2000000,0x2000400,0x2000020,0x2000420);
  		$pc2bytes6  = array (0,0x10000000,0x80000,0x10080000,0x2,0x10000002,0x80002,0x10080002,0,0x10000000,0x80000,0x10080000,0x2,0x10000002,0x80002,0x10080002);
  		$pc2bytes7  = array (0,0x10000,0x800,0x10800,0x20000000,0x20010000,0x20000800,0x20010800,0x20000,0x30000,0x20800,0x30800,0x20020000,0x20030000,0x20020800,0x20030800);
  		$pc2bytes8  = array (0,0x40000,0,0x40000,0x2,0x40002,0x2,0x40002,0x2000000,0x2040000,0x2000000,0x2040000,0x2000002,0x2040002,0x2000002,0x2040002);
  		$pc2bytes9  = array (0,0x10000000,0x8,0x10000008,0,0x10000000,0x8,0x10000008,0x400,0x10000400,0x408,0x10000408,0x400,0x10000400,0x408,0x10000408);
  		$pc2bytes10 = array (0,0x20,0,0x20,0x100000,0x100020,0x100000,0x100020,0x2000,0x2020,0x2000,0x2020,0x102000,0x102020,0x102000,0x102020);
  		$pc2bytes11 = array (0,0x1000000,0x200,0x1000200,0x200000,0x1200000,0x200200,0x1200200,0x4000000,0x5000000,0x4000200,0x5000200,0x4200000,0x5200000,0x4200200,0x5200200);
  		$pc2bytes12 = array (0,0x1000,0x8000000,0x8001000,0x80000,0x81000,0x8080000,0x8081000,0x10,0x1010,0x8000010,0x8001010,0x80010,0x81010,0x8080010,0x8081010);
  		$pc2bytes13 = array (0,0x4,0x100,0x104,0,0x4,0x100,0x104,0x1,0x5,0x101,0x105,0x1,0x5,0x101,0x105);
  		$masks = array (4294967295,2147483647,1073741823,536870911,268435455,134217727,67108863,33554431,16777215,8388607,4194303,2097151,1048575,524287,262143,131071,65535,32767,16383,8191,4095,2047,1023,511,255,127,63,31,15,7,3,1,0);

  		//	how many iterations (1 for des, 3 for triple des)
  		$iterations = ((strlen($key) >= 24) ? 3 : 1);
  		//	stores the return keys
  		$keys = array (); // size = 32 * iterations but you don't specify this in php
  		//	now define the left shifts which need to be done
  		$shifts = array (0, 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0);
  		//	other variables
  		$m=0;
  		$n=0;

  		for ($j=0; $j<$iterations; $j++)
  		{
  			//	either 1 or 3 iterations
    		$left = (ord($key{$m++}) << 24) | (ord($key{$m++}) << 16) | (ord($key{$m++}) << 8) | ord($key{$m++});
    		$right = (ord($key{$m++}) << 24) | (ord($key{$m++}) << 16) | (ord($key{$m++}) << 8) | ord($key{$m++});

    		$temp = (($left >> 4 & $masks[4]) ^ $right) & 0x0f0f0f0f; $right ^= $temp; $left ^= ($temp << 4);
    		$temp = (($right >> 16 & $masks[16]) ^ $left) & 0x0000ffff; $left ^= $temp; $right ^= ($temp>>16 & 0x0000ffff) + (($temp & 0x0000ffff) << 16);
    		$temp = (($left >> 2 & $masks[2]) ^ $right) & 0x33333333; $right ^= $temp; $left ^= ($temp << 2);
    		$temp = (($right >> 16 & $masks[16]) ^ $left) & 0x0000ffff; $left ^= $temp; $right ^= ($temp>>16 & 0x0000ffff) + (($temp & 0x0000ffff) << 16);
    		$temp = (($left >> 1 & $masks[1]) ^ $right) & 0x55555555; $right ^= $temp; $left ^= ($temp << 1);
    		$temp = (($right >> 8 & $masks[8]) ^ $left) & 0x00ff00ff; $left ^= $temp; $right ^= ($temp << 8);
    		$temp = (($left >> 1 & $masks[1]) ^ $right) & 0x55555555; $right ^= $temp; $left ^= ($temp << 1);

    		//	the right side needs to be shifted and to get the last four bits of the left side
    		$temp = ($left << 8) | (($right >> 20 & $masks[20]) & 0x000000f0);
    		//	left needs to be put upside down
    		$left = ($right << 24) | (($right << 8) & 0xff0000) | (($right >> 8 & $masks[8]) & 0xff00) | (($right >> 24 & $masks[24]) & 0xf0);
    		$right = $temp;

    		//now go through and perform these shifts on the left and right keys
    		for ($i=0; $i < count($shifts); $i++)
    		{
      			//shift the keys either one or two bits to the left
      			if ($shifts[$i] > 0)
      			{
      			   $left = (($left << 2) | ($left >> 26 & $masks[26]));
      			   $right = (($right << 2) | ($right >> 26 & $masks[26]));
      			}
      			else
      			{
      			   $left = (($left << 1) | ($left >> 27 & $masks[27]));
      			   $right = (($right << 1) | ($right >> 27 & $masks[27]));
      			}
      			$left = $left & -0xf;
      			$right = $right & -0xf;

		      	//now apply PC-2, in such a way that E is easier when encrypting or decrypting
		      	//this conversion will look like PC-2 except only the last 6 bits of each byte are used
		      	//rather than 48 consecutive bits and the order of lines will be according to
		      	//how the S selection functions will be applied: S2, S4, S6, S8, S1, S3, S5, S7
		      	$lefttemp = $pc2bytes0[$left >> 28 & $masks[28]] | $pc2bytes1[($left >> 24 & $masks[24]) & 0xf]
		              | $pc2bytes2[($left >> 20 & $masks[20]) & 0xf] | $pc2bytes3[($left >> 16 & $masks[16]) & 0xf]
		              | $pc2bytes4[($left >> 12 & $masks[12]) & 0xf] | $pc2bytes5[($left >> 8 & $masks[8]) & 0xf]
		              | $pc2bytes6[($left >> 4 & $masks[4]) & 0xf];
		      	$righttemp = $pc2bytes7[$right >> 28 & $masks[28]] | $pc2bytes8[($right >> 24 & $masks[24]) & 0xf]
		                | $pc2bytes9[($right >> 20 & $masks[20]) & 0xf] | $pc2bytes10[($right >> 16 & $masks[16]) & 0xf]
		                | $pc2bytes11[($right >> 12 & $masks[12]) & 0xf] | $pc2bytes12[($right >> 8 & $masks[8]) & 0xf]
		                | $pc2bytes13[($right >> 4 & $masks[4]) & 0xf];
		      	$temp = (($righttemp >> 16 & $masks[16]) ^ $lefttemp) & 0x0000ffff;
		      	$keys[$n++] = $lefttemp ^ $temp; $keys[$n++] = $righttemp ^ (($temp & 0x0000ffff) << 16);
    		}
  		} //	for each iterations

  		//	return the keys we've created
  		return $keys;
	} //	end of des_createKeys

	//
	//	Random Key Generate
	//
	function getRandomKey( $digits )
	{
		$keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$rndKey = "";
		$nIndex = 0;

		do
		{
			$nIndex = rand( 0, strlen( $keyStr ) - 1 );
			$rndKey = $rndKey . substr( $keyStr, $nIndex, 1 );
		}
		while ( strlen( $rndKey ) < $digits );

		return($rndKey);
	}
}

//	-----------------------------------------------------
//	-----	Web Service Call Proxy
//	-----------------------------------------------------

//	Ping Service
$proxies->NuguyaPingService->url = $strPingUrl;
$proxies->NuguyaPingService->domain = $strDomain;
$proxies->NuguyaPingService->port = $strPort;

$proxies->NuguyaPingService->getPingInfo = new WSService;
$proxies->NuguyaPingService->getPingInfo->service = $proxies->NuguyaPingService;

//	Secure Service
$proxies->NuguyaSecureService->url = $strSecureServiceUrl;
$proxies->NuguyaSecureService->domain = $strDomain;
$proxies->NuguyaSecureService->port = $strPort;

$proxies->NuguyaSecureService->getServiceProxy = new WSService;
$proxies->NuguyaSecureService->getServiceProxy->service = $proxies->NuguyaSecureService;

$retVal = $proxies->NuguyaSecureService->getServiceProxy->getService();

if ( $retVal )
	eval( $proxies->returnValue );
else
	$proxies->makeResponse( '한국신용정보 보안서비스 호출 Proxy를 생성할 수 없습니다. (Secure Service Proxy Call) \n\n서비스 점검중일 수 있습니다.\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오.', -1 );

//	Web Service Proxy
$proxies->NuguyaProxyService->url = $strServiceUrl;
$proxies->NuguyaProxyService->domain = $strDomain;
$proxies->NuguyaProxyService->port = $strPort;

$proxies->NuguyaProxyService->getServiceProxy = new WSService;
$proxies->NuguyaProxyService->getServiceProxy->service = $proxies->NuguyaProxyService;

$retVal = $proxies->NuguyaProxyService->getServiceProxy->getService();
if ( $retVal )
	eval( $proxies->returnValue );
else
	$proxies->makeResponse( '한국신용정보 서비스 호출 Proxy를 생성할 수 없습니다. (Service Proxy Call) \n\n서비스 점검중일 수 있습니다.\n상태가 지속되면 한국신용정보(주) (1588-2486)로 연락하십시오.', -1 );

//	-----------------------------------------------------
//	-----	Service Object Classes
//	-----------------------------------------------------

$oivsObject = new OivsObject();

class OivsObject
{
	var $clientData = "";
	var $niceId 	= "";
	var $pingInfo 	= "";
	var $userNm 	= "";
	var $resIdNo 	= "";
	var $inqRsn 	= "";
	var $foreigner 	= "";
	var $minor		= "";
	var $message	= "";
	var $retCd		= "";
	var $retDtlCd	= "";
	var $ckData		= "";
	var $skData		= "";
	var $identifier	= "";
	var $dupeinfo	= "";

	function OivsObject()
	{
		global $proxies;
		$this->pingInfo = trim( $proxies->NuguyaPingService->getPingInfo->getPing() );
	}

	function makeResponse( $message, $back )
	{
		$Msg = $message;
		$Msg = str_replace( "\n", "\\n", $Msg );
		echo ( '<html><head><meta http-equiv="Content-Type" content="text/html; charset=euc-kr"></head>' );
		echo ( '<script language="javascript">' );
		echo ( "alert( '" . trim( $Msg ) . "' ); " );
		echo ( 'history.go(' . $back . '); ' );
		echo ( '</script>' );
		echo ( '</head></html>' );
		die();
	}


	//
	//	클라이언트 정보를 복호화하여 설정한다.
	//
	function desClientData(){
		global $proxies;
		global $cryptoObject;
		global $strDelimeter;
		global $CRNDSIZE;
		global $strPersonal;

		$strCDESInfo = base64_decode( $this->clientData );
		$aryValues = split( $strDelimeter, $strCDESInfo );
		$retVal;

		//	클라이언트 정보를 수신하여 설정한다.
		if ( count( $aryValues ) < 2 )
		{
			$this->makeResponse( "사용자의 입력정보를 보안처리 하는 중에 오류가 발생하였습니다. [nice.nuguya.oivs : callService(client data decryption)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오", -1 );
		}
		else
		{
			$this->ckData = trim( $aryValues[0] );
			$strCDESInfo = trim( $aryValues[1] );

			$strCDESInfo = $cryptoObject->des( $this->ckData, $strCDESInfo, 0, 1, $this->ckData );
			$aryValues = split( $strDelimeter, $strCDESInfo );
		}

		if ( count( $aryValues ) < 4 )
		{
			$this->makeResponse( "사용자의 입력정보가 부족합니다. [nice.nuguya.oivs : callService(client data decryption)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오", -1 );
		}

		$this->userNm 		= urldecode( trim( $aryValues[0] ) );
		$this->resIdNo 		= trim( $aryValues[1] );
		$this->inqRsn 		= trim( $aryValues[2] );
		$this->foreigner	= trim( $aryValues[3] );
	}

	//
	//	한신정 서비스를 처리한다.
	//
	function callService()
	{
		global $proxies;
		global $cryptoObject;
		global $strDelimeter;
		global $CRNDSIZE;
		global $strPersonal;

		//	한신정 서비스와 연계하기 위한 정보를 설정한다.
		$this->ckData = $cryptoObject->getRandomKey( $CRNDSIZE );

		$strCHashedInfo = md5( $this->niceId . $this->pingInfo );
		$strCDESInfo = $CRNDSIZE . $strDelimeter .
			base64_encode( $this->ckData .
				$cryptoObject->des( $this->ckData, $this->niceId . $strDelimeter . $this->pingInfo . $strDelimeter . $strCHashedInfo, 1, 1, $this->ckData ) );

		$proxies->SecureServiceService->getServiceKey->func = null;
		$retVal = $proxies->SecureServiceService->getServiceKey->serviceCall( $strCDESInfo );

		if ( ( ! $retVal ) || $proxies->errorCode != "" )
		{
			$this->makeResponse( "한국신용정보 실명확인 서비스에 대한 보안 호출에 실패하였습니다.[nice.nuguya.oivs : callService(server getServiceKey)] \\n\\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오\\n\\n[오류코드]" . $proxies->errorCode . "\\n[오류메시지]\\n" . $proxies->errorMsg, -1 );
		}

		$aryRtnValues = $this->resolveReturnValue( $proxies->returnValue );

		if ( count( $aryRtnValues ) < 5 )
		{
			$this->makeResponse( "실명확인 서비스와의 보안 통신에 필요한 정보가 부족합니다. [nice.nuguya.oivs : callService(server getServiceKey)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오", -1 );
		}
		else if ( $aryRtnValues[3] != "" )
		{
			$this->makeResponse( "실명확인 서비스와의 보안 통신중에 오류가 발생하였습니다. [nice.nuguya.oivs : callService(server getServiceKey)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오" .
				" \\n\\n[오류정보]\\n\\n" . iconv( "utf-8", "euc-kr", $aryRtnValues[3]), -1 );
		}

		$this->retCd 	= trim( $aryRtnValues[1] );
		$this->retDtlCd	= trim( $aryRtnValues[2] );

		if ( $this->retCd == "0" && $this->retDtlCd == "0" )
		{
			$this->skData = $aryRtnValues[4];
			$this->identifier = $aryRtnValues[0];

			$this->skData = $cryptoObject->des( $this->ckData, base64_decode( $this->skData ), 0, 1, $this->ckData );
			$strCDESInfo = base64_encode( $this->identifier . $strDelimeter . $strPersonal . $strDelimeter .
				base64_encode( $cryptoObject->des( $this->skData, $this->niceId . $strDelimeter . urlencode( $this->userNm ) . $strDelimeter .
					$this->resIdNo . $strDelimeter . $this->inqRsn . $strDelimeter . $this->foreigner, 1, 1, $this->skData ) ) );

			$proxies->RealNameServiceService->checkRealName->func = null;
			$retVal = $proxies->RealNameServiceService->checkRealName->serviceCall( $strCDESInfo );

			if ( ( ! $retVal ) || $proxies->errorCode != "" )
			{
				$this->makeResponse( "한국신용정보 실명확인 서비스 호출에 실패하였습니다.[nice.nuguya.oivs : callService(server checkRealName)] \\n\\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오\\n\\n[오류코드]" . $proxies->errorCode . "\\n[오류메시지]\\n" . $proxies->errorMsg, -1 );
			}

			$aryRtnValues = $this->resolveReturnValue( $proxies->returnValue );

			if ( count( $aryRtnValues ) < 5 )
			{
				$this->makeResponse( "실명확인 서비스의 결과 정보가 부족합니다. [nice.nuguya.oivs : callService(server checkRealName)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오", -1 );
			}
			else if ( $aryRtnValues[3] != "" )
			{
				$this->makeResponse( "실명확인 서비스 진행 중에 오류가 발생하였습니다. [nice.nuguya.oivs : callService(server checkRealName)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오" .
					" \\n\\n[오류정보]\\n\\n" . iconv( "utf-8", "euc-kr", $aryRtnValues[3]), -1 );
			}

			if ( $aryRtnValues[0] != md5( $this->resIdNo ) )
			{
				$this->makeResponse( "실명확인 서비스의 결과가 변형되어 정확한 정보가 아닙니다. [nice.nuguya.oivs : callService(server checkRealName.. hash mismatch)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오", -1 );
			}

			$this->retCd	= trim( $aryRtnValues[1] );
			$this->retDtlCd	= trim( $aryRtnValues[2] );
			$this->message	= trim( $aryRtnValues[3] );
			$this->minor	= trim( $aryRtnValues[4] );
			if ( count( $aryRtnValues ) > 5 )
				$this->dupeinfo = trim( $aryRtnValues[5] );
		}
		else
		{
			$this->makeResponse( "실명확인 서비스와의 보안 통신을 계속 진행할 수 없습니다. [nice.nuguya.oivs : callService(server checkRealName)] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오" .
				"\\n\\n[결과코드]\\n\\n" . $aryRtnValues[1] . "," . $aryRtnValues[2], -1 );
		}
	}

	//
	//	한신정 서비스에서 반환한 결과를 분리하여 반환한다.
	//
	function resolveReturnValue( $returnValue )
	{
		global $strDelimeter;
		global $cryptoObject;

		$aryValues = split( $strDelimeter, $returnValue );

		if ( count( $aryValues ) < 5 )
		{
			$this->makeResponse( "실명확인 서비스에서 반환한 결과를 처리할 수 없습니다. [nice.nuguya.oivs : resolveReturnValue] \\n\\n고객상담센터(☎ 1588-2486) 로 연락하십시오", -1 );
		}

		if ( ! ( strpos( $aryValues[4], "_E_" ) === false ) )
		{
			$test = base64_decode( substr( $aryValues[4], 3, strlen( $aryValues[4] ) ) );
			$aryValues[4] = preg_replace( "/[\1\2\3\4\5\6\7]/", '', $cryptoObject->des( $this->skData, $test, 0, 1, $this->skData ) );
		}

		return( $aryValues );
	}

	//
	//	한신정 서비스에서 반환한 데이터를 분리하여 반환한다.
	//
	function resolveDataValue( $returnValue )
	{
		global $strDataDelimeter;

		$aryValues = split( $strDataDelimeter, $returnValue );
		return $aryValues;
	}
}

?>