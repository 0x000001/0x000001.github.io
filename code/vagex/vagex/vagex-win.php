<?php
ini_set('date.timezone','Asia/Shanghai');
/**
 * Created by JetBrains PhpStorm.
 * User: horsley
 * Date: 13-2-2
 * Time: 上午10:39
 * To change this template use File | Settings | File Templates.
 */

if (PHP_SAPI !== 'cli') {
    die ("This is CLI only version!");
} else {
    $v = new VagexCheater();
	$v->set_log_file('log-win.txt');
    $v->set_userid('407476');
	$v->set_vagex_user('nullunx@gmail.com');
	$v->set_pw('null4vagexP.1');
	$v->set_flash('2');
	$v->set_speed('1609');
	$v->set_machine_id('S-1-5-21-2061459912-3953257075-1710956145-1096');//一定要自己改一下
	$v->set_version('2.1.6');
	$v->set_kup178('bbb');
	$v->set_go('go');
    //$v->set_proxy('127.0.0.1:15846', true);
    $v->run();
}


/**
 * Class Vagex Cheater with cli log output
 */
class VagexCheater {
    const VAGEX_URL_LOGIN = 'https://vagex.com/alogin.php';
    const VAGEX_URL_R = 'http://vagex.com/kupper209.php';

    private $log_file;
    private $http;
    private $sleep_time;
	private $length;
	private $pw;
	private $vagex_user;
	private $secid;
	private $sid_;
	private $suid;
	private $flash;
	private $speed;
	private $machine_id;
	private $version;
	private $url;
	private $kup178;
	private $go;
    private $data_default = array(
        'userid'        => '0',
        'ua'            => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0'
    );

    function __construct() {
        $this->http = new HttpReq();
        $this->http->setUserAgent($this->data_default['ua']); //默认ua
        $this->log("Vagex Cheater instance initialized");
    }

    function set_log_file($filename) {
        $this->log_file = $filename;
        $this->log("Set log file: " . $filename);
    }

    function set_userid($uid) {
        $this->data_default['userid'] = $uid;
        $this->log("Set user id: " . $uid);
    }
	
	function set_vagex_user($user) {
        $this->vagex_user = $user; 
    } 
	
    function set_pw($pws) {
        $this->pw = $pws; 
    } 
	
    function set_proxy($proxy, $is_sock5 = false) {
        $this->http->setProxy($proxy, $is_sock5);
        $this->log("Set proxy: " . ($is_sock5?'sock5://':'') . $proxy );
    }
	function set_flash($flash1) {
        $this->flash = $flash1;
    }
	function set_speed($speed1) {
        $this->speed = $speed1;
    }
	function set_machine_id($machine_id1) {
        $this->machine_id = $machine_id1;
    }
	function set_version($version1) {
        $this->version = $version1;
    }
	function set_kup178($kup1781) {
        $this->kup178 = $kup1781;
    }
	function set_go($go1) {
        $this->go = $go1;
    }
    function run() {
		$this->vagex_login_f();
        $this->log('Start to run main routine');
		$this->update_video_arr(); 
        while(true) {
            $this->log("A new loop of a video array start");
			if ($this->sid_ <=0){
				log("update video arr");
				$this->update_video_arr();
			}else{
                    $this->log('Let\'s sleep for ' . $this->sleep_time . ' seconds');
                    sleep($this->sleep_time);
                    $this->log('Wake up, report processed');
                    if($result = $this->report_processed()) {
                        $this->log('Earnt: ' . $result);
                    }
			}
        }
    }

    /**
	* login
    * 
    */
	function vagex_login_f() {
		$this->log('Start login');
		$this->http->setCookies__();
		$this->http->setMethod('POST');
        $this->http->setPostData(array(
            'userid' => $this->vagex_user,
            'passwd' => $this->pw
        ));
		
		while(!$this->http->fetch(self::VAGEX_URL_LOGIN)){
			$this->log("login Failed.  ".$this->http->response['body']);
		}
		$ls = $this->http->response['body'];
		$data = $this->get_xml_data($ls);
		
		
		/*
		<?xml version="1.0"?>
		<entries>
		<entry>
			<secid></secid><error></error>
		</entry>
		</entries>
		*/
		if ($data['entry']['secid'] == "" ){
			$this->log($ls);
			exit ;
		}
		$this->secid = $data['entry']['secid'];
		$this->log("secid :".$this->secid);
		$this->http->setPostData("");
	}
    /**
    * get video items
    * @return bool
    */		
    function update_video_arr() {
        $this->log('Requesting new Show Array.');
		$this->http->setPostData(array());
        $this->http->setMethod('POST');
        
		$PostData = array(
            'userid' => $this->encrypted($this->data_default['userid']),
			'url' => $this->encrypted(''),
			'urlusrid' => $this->encrypted(''),
            'version' => $this->encrypted($this->version),
			'ytuser' => $this->encrypted(''),
			'length' => $this->encrypted(''),
			'hwid' => $this->encrypted($this->machine_id),
			'dur' => $this->encrypted(''),
			'flash' => $this->encrypted($this->flash),
			'speed' => $this->encrypted($this->speed),
			'secid' => $this->encrypted($this->secid),
			'kup178' => $this->kup178,
			'go' => $this->go,
        );

		$PostFields = array();
//        foreach ($PostData as $k => $v) $PostFields[] = "$k=$v";
//        $PostDataStr = base64_encode(implode('&', $PostFields));
		$this->http->setPostData(array($PostData));
		
        $this->http->fetch(self::VAGEX_URL_R);
//		echo "\n\n\n\n\n\n\n\nbody:".$this->http->response['body']."\n\n\n\n\n\n\n";
		$jsonxml= json_encode($this->http->response['body']);
//		echo "\n\n\n\n\n\n\njsonxml:".$jsonxml."\n\n\n\n\n\n";
		$xml_start=strripos($jsonxml,"ipc=")."\n";
		$xml_end= strripos($jsonxml,"p>")."\n";
//		echo "\n\n\n\n\n\n\n\nsubstr:".substr($jsonxml,$xml_start,$xml_end-$xml_start+2)."\n\n\n\n\n\n\n\n";
		$xml_cut="\"<p id='kup' ".substr($jsonxml,$xml_start,$xml_end-$xml_start+2)."\"";
//		echo "\n\n\n\n\n\n\nxml:".$xml_cut."\n\n\n\n\n\n";
		$xml = new DOMDocument(); 
		$xmlstring=json_decode($xml_cut);
		$xml->loadXML($xmlstring);
		$node_lists = $xml->getElementsByTagName('p');
		$node  = $node_lists->item(0);
		
		/*<?xml version="1.0"?>
		<entries>
		<entry>
		<url>Si</url><length>30</length><credits>0</credits><error></error><sid>25</sid><wlikes>0</wlikes><wsubs>0</wsubs><ref>https://www.youtube.com/results?q=Si</ref>
		</entry>
		</entries>*/
		
		$this->sid_ = $node->getAttribute('ssid');
		$this->url = $node->getAttribute('suid');
		$this->length = (int)$node->getAttribute('slength')/30;
		$this->sleep_time = (int)$node->getAttribute('slength') + rand(5,10);
		if ($node->getAttribute('error')!=array()){
			$this->log('update_video_arr error : '.$node->getAttribute('error'));
		}		
		if ($node->getAttribute('ssid')<=0){
			$this->sid_ = "";
			$this->url = "";
			$this->sleep_time = 5;
			$this->log($this->http->response['body']);
			$this->log( ' sleep : '.'30');
			sleep(30);
			$this->update_video_arr();
		}else{
			$this->log('sid : '.$node->getAttribute('ssid') . ',' . $node->getAttribute('suid') . ' sleep : '.$this->sleep_time);
		}

        return true;
    }

    function report_processed() {
        $this->log('report_processed start');
        $this->http->setPostData(array());
		$PostData = array(
            'userid' => $this->encrypted($this->data_default['userid']),
			'url' => $this->encrypted($this->sid_),
			'urlusrid' => $this->encrypted($this->url),
            'version' => $this->encrypted($this->version),
			'ytuser' => $this->encrypted(''),
			'length' => $this->encrypted($this->length),
			'hwid' => $this->encrypted($this->machine_id),
			'dur' => $this->encrypted(''),
			'flash' => $this->encrypted($this->flash),
			'speed' => $this->encrypted($this->speed),
			'secid' => $this->encrypted($this->secid),
			'kup178' => $this->kup178,
			'go' => $this->go,
        );
//		echo "userid:".$this->data_default['userid']."\n";
//		echo "url:".$this->sid_."\n";
//		echo "urlusrid:".$this->url."\n";
//		echo "version:".$this->version."\n";
//		echo "ytuser:".""."\n";
//		echo "length:".$this->length."\n";
//		echo "hwid:".$this->machine_id."\n";
//		echo "dur:".''."\n";
//		echo "flash:".$this->flash."\n";
//		echo "speed:".$this->speed."\n";
//		echo "secid:".$this->secid."\n";
//		$PostFields = array();
 //       foreach ($PostData as $k => $v) $PostFields[] = "$k=$v";
 //       $PostDataStr = base64_encode(implode('&', $PostFields));
		$this->http->setPostData($PostData);
        $this->http->setMethod('POST');
       


        if ($response_body = $this->http->fetch(self::VAGEX_URL_R)) {
			$this->sid_ = -1;
			if (strpos($response_body,'logged')==true){
				$this->vagex_login_f();
			}
			$jsonxml= json_encode($this->http->response['body']);
			$xml_start=strripos($jsonxml,"ipc=")."\n";
			$xml_end= strripos($jsonxml,"p>")."\n";
			$xml_cut="\"<p id='kup' ".substr($jsonxml,$xml_start,$xml_end-$xml_start+2)."\"";
			$xml = new DOMDocument(); 
			$xmlstring=json_decode($xml_cut);
			$xml->loadXML($xmlstring);
			$node_lists = $xml->getElementsByTagName('p');
			$node  = $node_lists->item(0);
			
			if ($node->getAttribute('error')!=array()){
				$this->log('report_processed error : '.$node->getAttribute('error'));
			}
			if ($node->getAttribute('ssid')<=0){
				$this->sid_ = -1;
				$this->url = "";
				$this->sleep_time = 5;
				$this->log($xml->saveXML());
				$this->log( ' sleep : '.'30');
				sleep(30);
				$this->update_video_arr();
			}else{
				$this->sid_ = $node->getAttribute('ssid');
				$this->url = $node->getAttribute('suid');
				$this->length = (int)$node->getAttribute('slength')/30;
				$this->sleep_time = (int)$node->getAttribute('slength') + rand(5,10);
				$this->log('sid : '.$node->getAttribute('ssid') . ',' . $node->getAttribute('suid') . ' sleep : '.$this->sleep_time);	
			}
            return $node->getAttribute('totalc');
        } else {
			$this->sid_ = -1;
			$this->url = "";
			$this->sleep_time = 5;
			$this->update_video_arr();			
            $this->log('report_processed Failed');
            return false;
        }
    }

	 /**
     * get_xml_data
     * @param $xml
	 * @return array
     */
	function get_xml_data($xml) {
		$xml = simplexml_load_string($xml);
		return json_decode(json_encode($xml),TRUE);
	}

    /**
     * HTTP Get and return response body
     * @param $url
     * @return mixed
     */
    function simple_fetch($url) {
        $this->http->setMethod('GET');
        return $this->http->fetch($url);
    }

    function log($log_line) {
        $time_array = explode(" ", microtime());
        $time_array[0] = sprintf('%.6f', $time_array[0]);
        $time = date('Y/m/d H:i:s.', $time_array[1]) . substr($time_array[0], 2) ;

        if(!empty($this->log_file)) {
            file_put_contents($this->log_file, "[$time] $log_line" . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        echo "[$time] $log_line" . PHP_EOL;
    }
	function encrypted($data) {
		$key = "lkirwf897+22#bbtrm8814z5qq=498j5";
		$iv = "741952hheeyy66#cs!9hjv887mxx7@8y";
	
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $iv));
	}
        
	function decrypted($data) {
		$key = "lkirwf897+22#bbtrm8814z5qq=498j5";
		$iv = "741952hheeyy66#cs!9hjv887mxx7@8y";
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_CBC, $iv);
	}
}

//////////////  End Class VagexCheater  ///////////////////

/**
 * Http Request Class
 * 参考 @link https://apidoc.sinaapp.com/sae/SaeFetchurl.html
 *      @link http://josephscott.org/archives/2010/03/php-helpers-curl_http_request/
 */
class HttpReq
{
    public $response = array();
    private $cookies = array();
    private $headers = array();
    private $curl_opt = array();

    function __construct() {
        $this->curl_opt = array(
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => 0,
        );
        $this->setConnectionTimeout(5); //默认连接超时 5s
        $this->setTotalTimeout(15); //执行超时15s
        $this->setUserAgent('Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.1'); //默认ua

        //$this->setProxy('192.168.11.36:8880'); //fiddler debug
    }

    /**
     * 设置代理，如127.0.0.1:8888
     * @param $proxy
     * @param bool $is_socks5
     */
    public function setProxy($proxy, $is_socks5 = false) {
        $this->curl_opt[CURLOPT_PROXY] = $proxy;
        if ($is_socks5) {
            $this->curl_opt[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
        }
    }

    /**
     * 设置请求方法，如get post
     * @param string $method
     */
    public function setMethod($method = 'GET') {
        $this->curl_opt[CURLOPT_CUSTOMREQUEST] = $method;
        if ($method == 'POST') {
            $this->curl_opt[CURLOPT_POST] = true;
        } else if ( $method == 'HEAD' ) {
            $curl_opt[CURLOPT_NOBODY] = true;
        }
    }

    /**
     * 设置连接超时
     * @param $second
     */
    public function setConnectionTimeout($second) {
        $this->curl_opt[CURLOPT_CONNECTTIMEOUT] = $second;
    }

    /**
     * 设置执行超时
     * @param $second
     */
    public function setTotalTimeout($second) {
        $this->curl_opt[CURLOPT_TIMEOUT] = $second;
    }

    /**
     * 设置ua
     * @param $ua
     */
    public function setUserAgent($ua) {
        $this->curl_opt[CURLOPT_USERAGENT] = $ua;
    }

    /**
     * 批量设置cookie
     * @param $cookie_arr
     */
	public function setCookies__() {
        $this->cookies=array();//清空cookies
    }
	
    public function setCookies($cookie_arr) {
        if ($cookie_arr) {
            foreach($cookie_arr as $k => $v) {
                $this->setCookie($k, $v);
            }
        }
    }

    /**
     * 设置一条cookie
     * @param $cookie_name
     * @param $cookie_value
     */
    public function setCookie($cookie_name, $cookie_value) {
        $this->cookies[$cookie_name] = $cookie_value;
    }

    /**
     * 设置一条header
     * @param $header_name
     * @param $header_value
     */
    public function setHeader($header_name, $header_value) {
        $this->headers[$header_name] = $header_value;
    }
	public function setHeader_() {
        $this->headers =array();
    }
    /**
     * 设置post提交值，会覆盖前面的设置
     * @param $post_arr
     * @param $multipart 是否为二进制数据
     */
    public function setPostData($post_arr, $multipart = false) {
        if (empty($post_arr) && isset($this->curl_opt[CURLOPT_POSTFIELDS])) {
            unset($this->curl_opt[CURLOPT_POSTFIELDS]);
            return;
        }
        if (!$multipart) {
            foreach ($post_arr as $k => &$p) {
                $p = urlencode($p);
                $p = "$k=$p";
            }
            $this->curl_opt[CURLOPT_POSTFIELDS] = implode('&', $post_arr);
        } else {
            $this->curl_opt[CURLOPT_POSTFIELDS] = $post_arr;
        }
    }

    /**
     * 取已设置的post参数
     * @return array|string
     */
    public function getPostData() {

        $post_arr = $this->curl_opt[CURLOPT_POSTFIELDS];
        if (is_array($post_arr)) {
            foreach($post_arr as &$p) {
                $p = urldecode($p);
            }
            return $post_arr;
        } else if (is_string($post_arr)) {
            $post_arr = explode('&', $post_arr);
            $count = count($post_arr);
            for ($i = 0; $i < $count; $i++) {
                list($k, $v) = explode('=', $post_arr[$i], 2);
                unset($post_arr[$i]);
                $post_arr[$k] = $v;
            }
            return $post_arr;
        }
    }

    private function _prepare_custom_fields() {
        if (count($this->cookies) > 0) {    //cookies init
            $formatted = array();
            foreach($this->cookies as $k => $v) {
                $formatted[] = "$k=$v";
            }
            $this->curl_opt[CURLOPT_COOKIE] = implode( ';', $formatted );
        }

        if (count($this->headers) > 0) {    //headers init
            $formatted = array();
            foreach($this->headers as $k => $v) {
                $formatted[] = "$k: $v";
            }
            $this->curl_opt[CURLOPT_HTTPHEADER] = $formatted;
        }
    }
	private function _prepare_custom_fields_() {

		$this->curl_opt[CURLOPT_COOKIE] = "";
        if (count($this->headers) > 0) {    //headers init
            $formatted = array();
            foreach($this->headers as $k => $v) {
                $formatted[] = "$k: $v";
            }
            $this->curl_opt[CURLOPT_HTTPHEADER] = $formatted;
        }
    }
    /**
     * 抓取
     * @param $url
     * @return bool
     */
    public function fetch( $url ) {
		if (strpos($url,'vagex')==false){
			$this->_prepare_custom_fields_();
		}else{
			$this->_prepare_custom_fields();
		}
        
		
        $curl = curl_init( $url );
        curl_setopt_array( $curl, $this->curl_opt );

        $this->response['body'] = curl_exec( $curl );
        $this->response['err_no'] = curl_errno( $curl );
        $this->response['err_msg'] = curl_error( $curl );
        $this->response['info'] = curl_getinfo( $curl );
		
        curl_close( $curl );
		
        //cut body and header
        $this->response['headers'] = trim( substr( $this->response['body'], 0, $this->response['info']['header_size'] ) );
        $this->response['body'] = substr( $this->response['body'], $this->response['info']['header_size'] );

        if ($this->response['err_no'] == 0) {
            return $this->response['body'];
        } else {
            return false;
        }
    }

    /**
     * 取得返回的http头
     * @param $parse
     * @return mixed|string
     */
    public function getHeaders($parse = true) {
        $headers = array_pop( explode( "\r\n", $this->response['headers'], 2 ) );

        if (!$parse) {
            return $headers;
        }

        $headers = explode("\r\n", $headers);
        $headers_new = array();
        foreach ( $headers as $line ) {
            @list( $k, $v ) = explode( ':', $line, 2 );
            if ( empty( $v ) ) {
                continue;
            }

            if ( strtolower( $k ) == 'set-cookie' ) {
                if (array_key_exists($k, $headers_new)) {
                    array_push($headers_new[$k], trim( $v ));
                } else {
                    $headers_new[$k] = array(trim( $v ));
                }
            } else {
                $headers_new[$k] = trim( $v );
            }
        }
        return $headers_new;
    }

    public function getCookies($all = true)
    {
        $header = $this->response['headers'];
        $matchs = array();
        $cookies = array();
        $kvs = array();
        if (preg_match_all('/Set-Cookie:\s([^\r\n]+)/i', $header, $matchs)) {
            foreach ($matchs[1] as $match) {
                $cookie = array();
                $items = explode(";", $match);
                foreach ($items as $_) {
                    $item = explode("=", trim($_));
                    if (count($item) == 2) {
                        $cookie[$item[0]]= $item[1];
                    }
                }
                array_push($cookies, $cookie);
                $kvs = array_merge($kvs, $cookie);
            }
        }
        if ($all) {
            return $cookies;
        } else {
            unset($kvs['path']);
            unset($kvs['max-age']);
            return $kvs;
        }
    }
}
//////////////  End Class HttpReq  ///////////////////
