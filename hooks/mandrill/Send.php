<?php
namespace Kaarisaa\Drivers;

use Mandrill;
/**
 * Email Send Class for Mandrill
 * @author  Ahmed Rishwan
 * @date    28 April 2015
 */

class Send{

  private $token;
  private $subject;
  private $message;
  public $from_email;
  public $from_name;

  public function __construct()
  {
  }

  //sets and gets
  public function setMessage($message)
  {
    $this->message = $message;
  }

  public function setSubject($subject)
  {
    $this->subject = $subject;
  }

  public function setFromEmail($from_email)
  {
    $this->from_email = $from_email;
  }

  public function setFromName($from_name)
  {
    $this->from_name = $from_name;
  }


  /***/
  public function sendEmail($recipients,$token)
  {
      $this->token = $token;
      
      $mandrill = new Mandrill($this->token); //set to TEST API Key -> Change when switiching to production

      //sending email with all recipients
      try
      {
        $message = [
          'text'       => $this->message,
          'subject'    => $this->subject,
          'from_email' => $this->from_email,
          'from_name'  => $this->from_name,
          'to'         => $recipients,

        ];

        $async = false;
        $ip_pool = 'Main Pool';
        $result = $mandrill->messages->send($message, $async, $ip_pool);
        print_r($result);

      }
      catch(Mandrill_Error $e)
      {
        $output->writeln("Mandrill Error occured: ".get_class($e).'-'.$e->getMessage());

        throw $e;
      }
  
  }

}

?>
