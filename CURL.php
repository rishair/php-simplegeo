<?php


class CURL{
	
	protected $Body;
	protected $Options;
	protected $OptionsReturner;
	protected $Resource;
	protected $Cookie;
	protected $ReturnHeaders;
	public $Status;
	public $Variables;
	public $URL;
	protected $Cookies;
	protected $Headers;
	protected $Method;
	public $QueryString;
	protected $Data;
	protected $SSLv3;
	protected $OptionMap = array(
		'CurlURL' => CURLOPT_URL,
		'UserPassword' => CURLOPT_USERPWD,
		'FollowLocation' => CURLOPT_FOLLOWLOCATION,
		'FreshConnection' => CURLOPT_FRESH_CONNECT,
		'Header' => CURLOPT_HEADER,
		'HTTPHeaders' => CURLOPT_HTTPHEADER,
		'Post' => CURLOPT_POST,
		'CustomRequest' => CURLOPT_CUSTOMREQUEST,
		'Upload' => CURLOPT_UPLOAD,
		'ReturnTransfer' => CURLOPT_RETURNTRANSFER,
		'BufferSize' => CURLOPT_BUFFERSIZE,
		'Timeout' => CURLOPT_TIMEOUT,
		'ConnectTimeout' => CURLOPT_CONNECTTIMEOUT,
		'UploadFileSize' => CURLOPT_INFILESIZE,
		'MaxConnections' => CURLOPT_MAXCONNECTS,
		'MaxRedirects' => CURLOPT_MAXREDIRS,
		'Port' => CURLOPT_PORT,
		'ResumeFrom' => CURLOPT_RESUME_FROM,
		'CookieData' => CURLOPT_COOKIE,
		'CookieFile' => CURLOPT_COOKIEFILE,
		'CookieJar' => CURLOPT_COOKIEJAR,
		'Encoding' => CURLOPT_ENCODING, //identity, deflate, gzip
		'FtpPort' => CURLOPT_FTPPORT,
		'PostFields' => CURLOPT_POSTFIELDS,
		'UploadFile' => CURLOPT_INFILE,
		'UserAgent' => CURLOPT_USERAGENT,
		'Encoding' => CURLOPT_ENCODING,
		'VerifySSLPeer' => CURLOPT_SSL_VERIFYPEER,
		'VerifySSLHost' => CURLOPT_SSL_VERIFYHOST,
		'SSLVersion' => CURLOPT_SSLVERSION
	);
		
	
	/**
		Initialize the CURL object with a URL
		
		@param	string	pURL	The URL to fetch
	**/
	public function CURL($pURL = false)
	{
		$this->Cookies = array();
		$this->SSLv3 = false;
		$this->Init($pURL);
	}
	
	/**
		Get the result of the previous request
		
		@return		string	Data of previous request (no headers)
	**/
	public function GetData(){
		return $this->Data;
	}
	
	
	/**
		Set method for the request
		
		@param	string pMethod	HTTP Method to use for the request (e.g. GET, PUT, POST, DELETE)
		
	**/
	public function SetMethod($pMethod) {
		$this->Method = strtoupper($pMethod);
	}
	
	/**
		Initializes the object with a URL, if no URL is passed then uses previous URL.
		
		@param	string	pURL	URL to fetch (without GET parameters)
	**/
		
	public function Init($pURL = false)
	{
		if (!is_array($this->Cookies)) $this->Cookies = array();
		$this->Variables = array();
		$this->Options = array();
		$this->Headers = array();
		$this->OptionsReturner = array();
		$this->Data = false;
		$this->Body = '';
		
		$this->SetMethod('GET');
		
		$this->ReturnTransfer = true;
		$this->FollowLocation = true;
		$this->UserAgent = 'User-Agent:Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_4; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.127 Safari/533.4';
		$this->SetHeader('Accept', 'image/gif, image/x-bitmap, image/jpeg, image/pjpeg');
		$this->SetHeader('Accept-Language', 'en-us,en;q=0.5');
		$this->Encoding = 'gzip,deflate';
		$this->SetHeader('Accept-Charset', 'ISO-8859-1,utf-8;q=0.7,*;q=0.7');
		$this->SetHeader('Keep-Alive', 300);
		$this->SetHeader('Connection', 'Keep-Alive');
		$this->Timeout = 30;
		$this->ConnectTimeout = 30;
		$this->MaxRedirects = 10;
		$this->Header = true;
		
		if ($pURL){
			$this->ChangeURL($pURL);
		}
	}
	
	/**
		Change the URL of the request
		
		@param	string	$pURL	The URL of the new request
	**/
	public function ChangeURL($pURL)
	{
		$this->URL = $pURL;
	}
	
	/**
		Set the body of the request to send
		
		@param	string $pBody	The body to send to the request
	**/
	public function SetBody($pBody) {
		$this->Body = $pBody;
	}
	
	/**
		Set the path of the cookie, disable fake cookies
		
		@param	string	pPath	The relative or full path to the cookie file
	**/
	public function SetCookiePath($pPath) {
		$this->Cookie = $pPath;
	}
	
	/**
		Add a cookie for future requests
		
		@param	string	key		The name of the cookie
		@param	string	value	The value of the cookie
	**/
	public function AddCookie($pKey, $pValue) {
		$this->Cookies[$pKey] = $pValue;
	}
	
	
	/**
		Clear a cookie
		
		@param string	pKey	Cookie name
	**/
	public function ClearCookie($pKey) {
		unset($this->Cookies[$pKey]);
	}
	
	/**
		Gets the concatenated cookie string
		
		@return	string	A string combining all the values of the cookie
	**/
	protected function GetCookieData() {
		$str = '';
		foreach ($this->Cookies as $key => $value) {
			$str .= urlencode($key) . '=' . urlencode($value) . '; ';
		}
		return $str;
	}
	
	/**
		Add a header to the request
		
		@param	string	pKey	Header key
		@param	string	pVal	Header value
	**/
	public function SetHeader($pKey, $pVal)
	{
		$this->Headers[] = $pKey . ': ' . $pVal;
	}
	
	/**
		Set an option directly to the curl resource
		
		@param	string	pOption	Option name as defined in OptionMap
		@param	string	pValue	Option value
	**/
	protected function SetOption($pOption, $pValue)
	{
		curl_setopt($this->Resource, $this->OptionMap[$pOption], $pValue);
		$this->OptionsReturner[$pOption] = $pValue;
	}
	
	/**
		Get an array containing a list of all the variables
		
		@return	array	array of arrays that contain a key and a value pair
	**/
	public function GetVariables() {
		return $this->Variables;
	}
	
	/**
		Add a variable for use in either the query string or post variables
		
		@param	string	pKey	The key of the variable
		@param	string	pVal	The value of the variable
	**/
	public function AddVar($pKey, $pVal)
	{
		array_push($this->Variables, urlencode($pKey) . '=' . urlencode($pVal));
	}
	
	/**
		Used to add several variables at from an associate array
		
		@param	array	pData	An associate array with values to add to the request
	**/
	public function AddVars($pData)
	{
		if (is_array($pData)){
			foreach ($pData as $key => $val) {
				$this->AddVar($key, $val);
			}
		}
	}
	
	/**
		Set the authentication credentials for a request
		
		@param	string	pUsername
		@param	string	pPassword
	**/
	public function SetLogin($pUsername, $pPassword) {
		$this->UserPassword = $pUsername . ':' . $pPassword;
	}
	
	public function __set($pKey, $pValue)
	{
		array_push($this->Options, array($pKey, $pValue));
		$this->OptionsReturner[$pKey] = $pValue;
	}
	
	public function __get($pKey)
	{
		return $this->OptionsReturner[$pKey];
	}
	
	/**
		Clear all the variables for the next request
	**/
	public function ClearVars()
	{
		$this->Variables = array();
	}
	
	/**
		Get the curl info from the last request
		
		@return An associative array as defined here: http://php.net/manual/en/function.curl-getinfo.php
	**/
	public function GetInfo()
	{
		return curl_getinfo($this->Resource);
	}
	
	/**
		Get the error that was returned in the last request
		
		@return string	The error number with a description
	**/
	public function GetError()
	{
		$e = curl_error($this->Resource);
		if (!$e){
			return false;
		}
		return curl_errno($this->Resource) . ': ' . $e;
	}
	
	/**
		Get the error number that was returned with the last request
		
		@return	int	The error number
	**/
	public function GetErrorNo()
	{
		return curl_errno($this->Resource);
	}
	
	/**
		Overridable function to handle errors
		
		@param	no	The curl error number
	**/
	public function HandleError($no)
	{
	}
	
	/**
		Disable SSL authentication verification on the client
	**/
	public function DisableSSL()
	{
		$this->VerifySSLPeer = 0;
		$this->VerifySSLHost = 0;
	}
	
	/**
		Get the absolute URL of the request, appends GET query string if request is GET
		
		@return A string representing the URL of the current request
	**/
	public function GetURL() {
		if ($this->Method != 'GET') {
			return $this->URL;
		}
		if ($this->Variables) {
			return $this->URL . '?' . implode('&', $this->Variables);
		}
		return $this->URL;
	}
	
	
	/**
		Creates the CURL resource and sets the appropriate options
		
		@return	CURL resource
	**/
	public function PrepareHandle() {
		$this->Resource = curl_init($this->GetURL());
		if (strpos($this->URL, 'https://') === 0){
			$this->DisableSSL();
		}
		if ($this->Headers) {
			$this->HTTPHeaders = $this->Headers;
		}
		if ($this->SSLv3){
			$this->SSLVersion = 3;
		}
		if ($this->Cookie)
		{
			$this->CookieFile = $this->Cookie;
			$this->CookieJar = $this->Cookie;
		} else {
			$this->CookieData = $this->GetCookieData();
		}
		$query = $this->Body ? $this->Body : implode('&', $this->Variables);
		$this->QueryString = $query;
		if ($this->Method != 'GET')
		{
			if ($this->Method == 'POST') $this->Post = count($this->Variables);
			else $this->CustomRequest = $this->Method;
			$this->PostFields = $query;
			$this->SetHeader('Content-Type', 'application/x-www-form-urlencoded');
			$this->SetHeader('Content-Length', strlen($query));
		}
		for ($i = 0; $i < count($this->Options); $i++){
			$this->SetOption($this->Options[$i][0], $this->Options[$i][1]);
		}
		return $this->Resource;
	}		
	
				
	/**
		Creates the request and executes it
		
		@return	string	The response from the server
	**/
	public function Get()
	{
		$this->PrepareHandle();
		for ($i = 0; $i < 10; $i++){
			$d = curl_exec($this->Resource);
            $this->Status = curl_getinfo($this->Resource, CURLINFO_HTTP_CODE); 
			if ($this->GetError()){
				if (strpos($this->GetError(), "sslv3")){
					$this->sslv3 = true;
					curl_setopt($this->Resource, CURLOPT_SSLVERSION, 3);
				}
				$this->HandleError($this->GetErrorNo());
			}
			if ($d){
				if ($this->Header) {
					$headers = array();
					while (substr($d, 0, 4) == 'HTTP') {
						$headerEnd = strpos($d, "\r\n\r\n");
						$headersSplit = explode("\n", substr($d, 0, $headerEnd));
						$d = substr($d, $headerEnd + 4);
						$headers = array();
						for ($k = 0; $k < count($headersSplit); $k++) {
							$line = $headersSplit[$k];
							$v = strpos($line, ":");
							if ($v) {
								$headers[strtolower(trim(substr($line, 0, $v)))] = trim(substr($line, $v + 1));
							}
						}
					}
					$this->ReturnHeaders = $headers;
					$this->ProcessHeaders();
				}
				$this->Data = $d;
				return $this->GetResults();
			}
		}
		return NULL;
	}
	
	
	/**
		Processes the headers returned from the last request and pulls out cookie data to simulate
		cookies for future requests.
	**/
	protected function ProcessHeaders() {
		$cookie = $this->ReturnHeader('set-cookie');
		if ($cookie) {
			$cookie = explode(';', $cookie);
			$array = array();
			foreach ($cookie as $kv) {
				$kv = explode('=', $kv);
				$key = trim($kv[0]);
				$value = trim($kv[1]);
				$array[$key] = $value;
				if ($key != 'expires' && $key != 'path' && $key != 'domain')
					$this->Cookies[$key] = $value;
			}
		}
	}
	
	
	/**
		Overridable - modify result of a Get request
		
		@return Result based on the returned results
	**/
	public function GetResults() {
		return $this->Data;
	}
	
	/**
		Get the return header of the request
		
		@param	string	pKey	The header to retrieve (e.g. location, content-type)
		
		@return	string	The value of the header
	**/
	public function ReturnHeader($pKey = false) {
		return isset($this->ReturnHeaders[$k = strtolower($pKey)]) ? $this->ReturnHeaders[$k] : NULL;
	}
	
	/**
		Close the CURL connection
	**/
	public function Close()
	{
		curl_close($this->Resource);
	}
	
	/**
		Revert this instance to its original state and set the referer for the next URL to 
		the previous URL visited
		
		@param	string	pURL	The URL for the new request
	**/
	public function Revert($pURL = false)
	{
		if ($this->Resource){
			$this->Close();
		}
		$url = $this->URL;
		$this->Init($pURL);
		if ($url) $this->SetHeader('Referer', $url);
	}
	
	public function __destroy()
	{
		$this->Close();
	}
}


/*

(The MIT License)

Copyright (c) 2011 Rishi Ishairzay <rishi [at] ishairzay [dot] com>

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


?>