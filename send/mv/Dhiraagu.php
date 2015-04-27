<?php
namespace Kaarisaa\Senders;
/**
 * SMS Sender class for Dhiraagu
 * @author - Ahmed Ali <ajaaibu@gmail.com>
 * @date - 27-04-2015 05:36 AM
 */
class Dhiraagu {

	private $number;
	private $keyword;
	private $shortcode;
	private $message;
	private $content;
	private $return = false;

	private $username;
	private $password;

  /**
   * Intializes the class with the given credentials
   */
	public function __construct($username,$password){
		$this->username = $username;
		$this->password = $password;
	}

  /**
   * Instantiate class from XML
   * @returns Dhiraagu instance
   */
	public static function byXML($xml){

		$instance = new self();
		$instance->readFromXML($xml);

		return $instance;
	}

  /**
   * Instantiate class by parameters
   * @returns Dhiraagu instance
   */
	public static function byParams($number,$message,$auth=false){

		$instance = new self();

		if($auth){
			$instance->setUsername($auth[0]);
			$instance->setPassword($auth[1]);
		}
		$instance->setNumber(str_replace('-','',$number));
		$instance->setMessage(strtolower($message));

		$instance->chopMessage();

		return $instance;
	}

  /**
   * Reads and intialize object from XML
   */
	private function readFromXML($xml){

		$this->number = str_replace('-','',$xml->TELEMESSAGE_CONTENT->MESSAGE->USER_FROM->CIML->DEVICE_INFORMATION->DEVICE_VALUE);

		$message = $xml->TELEMESSAGE_CONTENT->MESSAGE->MESSAGE_CONTENT;

		if(property_exists($xml->TELEMESSAGE_CONTENT->MESSAGE, 'MESSAGE_CONTENT')){
			if(count($xml->TELEMESSAGE_CONTENT->MESSAGE->MESSAGE_CONTENT->TEXT_MESSAGE) > 0){
				$this->message = strtolower($this->concatMessages($xml->TELEMESSAGE_CONTENT->MESSAGE->MESSAGE_CONTENT->TEXT_MESSAGE));
			}
			else{
				$this->message = strtolower($xml->TELEMESSAGE_CONTENT->MESSAGE->MESSAGE_CONTENT->TEXT_MESSAGE->TEXT);
			}
		}
		else{
			$this->message = "";
		}

		$this->chopMessage();
	}

	public function concatMessages($messages){
		$msg = "";

		foreach($messages as $m){
			$msg .= $m->TEXT;
		}

		return $msg;
	}

  /**
   * Send Reply to a Message
   */
	public function Reply($message){
		if(!$this->getReturn()){
			$this->sendMessage($message);
		}
		else{
			echo $message;
		}
	}

  /**
   * Send a Message to a number
   */
	public function sendMessage($message,$number=false){

		if(!empty($number))
			$this->number = $number;

		$parts = $this->splitMessage($message);
    
    $xml = $this->prepareXML('messages' => $parts);

		$this->curlPost($xml);
	}

	public function normalize($string){
	    $table = array(
	        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
	        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
	        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
	        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
	        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
	        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
	        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü' => 'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
	        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
	    );
	    
	    return strtr($string, $table);
	}


  /**
   * Push the SMS Push request to the service provider API
   */
	public function curlPost($xml){
		$ch = curl_init("http://bulkmessage.com.mv/partners/xmlMessage.jsp");
		curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,0);
		curl_setopt($ch,CURLOPT_REFERER,'http://www.haveeru.com.mv');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		$ch_result = curl_exec($ch);
		curl_close($ch);

		return $ch_result;
	}


  /**
   * Split the message into multiple message bodies if the length exceeds 160 chars
   */
	public function splitMessage($message){

		
		// $message = html_entity_decode($message);
		$message = $this->normalize($message);
		
		$message = htmlentities($message);
		$limit = 160;
		$length = strlen($message);

		if($length > $limit){
			// $message = str_replace("<br />",'&#xA;&#xD;',nl2br($message));
			// $length = strlen($message);
		}

		$partLength = ceil($length/$limit);

		$parts = array();

		for($i = 0;$i<$partLength;$i++){
			$start = $i == 0 ? 0 : ($i * $limit);

			if($partLength > 0){
				$parts[] = substr($message, $start,160);
			}
		}

		return $parts;
	}

	// setters and getters
	public function getNumber(){
		return $this->number;
	}

	public function setNumber($number){
		$this->number = $number;
	}

	public function getKeyword(){
		return $this->keyword;
	}

	public function setMessage($message){
		$this->message = $message;
	}

	public function getMessage(){
		return $this->message;
	}

	public function getContent(){
		return $this->content;
	}

	public function setUsername($username){
		$this->username = $username;
	}

	public function getUsername(){
		return $this->username;
	}

	public function setPassword($password){
		$this->password = $password;
	}

	public function getPassword(){
		return $this->password;
	}

	public function enableReturn(){
		$this->return = true;
	}

	public function getReturn(){
		return $this->return;
	}

	public function chopMessage(){
		// get the trail position of keyword
		$keywordTrailingPosition = strpos($this->message,' ') ? strpos($this->message,' ') : strlen($this->message);

		// slice keyword from message || set keyword object
		$keyword = substr($this->message,0,$keywordTrailingPosition);
		$this->keyword = $keyword;

		// slice content from message || set content object
		$content = substr($this->message,$keywordTrailingPosition);
		$this->content = trim($content);
	}

  /**
   * Populates the XML required for Dhiraagu SMS Push Request
   * @returns string xml
   */
  public function prepareXML(){
  
  $xml = <<<END
<?xml version="1.0" encoding="UTF-8" ?>
<TELEMESSAGE>
  <TELEMESSAGE_CONTENT>
    <MESSAGE>
      <MESSAGE_INFORMATION>
        <SUBJECT/>
      </MESSAGE_INFORMATION>
      <USER_FROM>
        <CIML>
          <NAML>
            <LOGIN_DETAILS>
              <USER_NAME>{$this->getUsername()}</USER_NAME>
              <PASSWORD>{$this->getPassword()}</PASSWORD>
            </LOGIN_DETAILS>
          </NAML>
        </CIML>
      </USER_FROM>
      <MESSAGE_CONTENT>
END;
    foreach($messages as $i => $msg){
$xml .= <<<END
        <TEXT_MESSAGE>
          <MESSAGE_INDEX>{$i}</MESSAGE_INDEX>
          <TEXT>{$msg}</TEXT>
        </TEXT_MESSAGE>
END;
    }
$xml .= <<<END
      </MESSAGE_CONTENT>
      <USER_TO>
        <CIML>
          <DEVICE_INFORMATION>
            <DEVICE_TYPE DEVICE_TYPE="SMS"/>
            <DEVICE_VALUE>{$this->getNumber()}</DEVICE_VALUE>
          </DEVICE_INFORMATION>
        </CIML>
      </USER_TO>
    </MESSAGE>
  </TELEMESSAGE_CONTENT>
  <VERSION>1.6</VERSION>
</TELEMESSAGE>
END;

  return $xml;
  }
}
?>
