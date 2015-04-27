<?php
namespace Kaarisaa\Console\Commands;

use Symfony\Component\Console\Command\Command; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use Symfony\Component\Console\Output\StreamOutput;

use Mandrill;


class EmailSendCommand extends Command {
  
  protected function configure(){
      $this->setName("email:send")
           ->setDescription("Sends Emails via Mandrill")
           ->setHelp(<<<EOT
Refer docs - Email Send via Mandrill
EOT
)
          ->setDefinition(array(
            new InputOption('from_email','fe',InputOption::VALUE_OPTIONAL,'nprelief@kickstart.mv'),
            new InputOption('from_name', 'fn',InputOption::VALUE_OPTIONAL,'nprelief'),
          ));
  }

  protected function execute(InputInterface $input, OutputInterface $output){

    //loading DB driver 
    $db = $this->getApplication()->DB();

    //selecting all outgoing messages
    $messages = $db->fetchAll("select * from message_out");

    foreach($messages as $message){
      
      $subscribers = [];
      $recipients = [];
      // get subscribers for the persons
      $personSubs = $db->fetchAll("select user_id from subscriptions where sub_person in (".$message['persons'].")");
      
      foreach($personSubs as $pSub){
        array_push($subscribers,$pSub['user_id']);
      }

      // get subscribers to the location
      $locationSubs = $db->fetchAll("select user_id from subscriptions where sub_location in (".$message['locations'].")");

      foreach($locationSubs as $lSub){
        array_push($subscribers,$lSub['user_id']);
      }

      $ignore = implode($subscribers,",");

      $others = $db->fetchAll("select user_id from subscriptions where sub_location is null and sub_person is null and user_id NOT IN (".$ignore.")");

      foreach($others as $oSub){
        array_push($subscribers,$oSub['user_id']);
      }

      $finalList = array_unique($subscribers);

      foreach($finalList as $sub){
        $query = $db->executeQuery("select * from user where id = ? limit 1",[$sub]);
        
        $user = $query->fetch();

        if(empty($user['email'])){
          $output->writeln("No phone number, ignoring...");
        }
        else{
          $output->writeln("Sending Email to " . $user['username']. " - " . $user['email']);
          //$sms->sendMessage($message['message'],$user['phone']);
          //
          $recipients[] = array('email'=>$user['email'], 
                                'name'=>$user['fullname'],
                                'type'=>'to');
        }
      }

      //starting up Mandrill 
      $mandrill = new Mandrill('vJn9Xl7YhPsIK7HXezBU9Q'); //set to TEST API Key -> Change when switiching to production

      //sending email with all recipients
      try
      {
        $message = [
          'text'       => $message['message'],
          'subject'    => $message['title'],
          'from_email' => $input->getOption('from_email'),
          'from_name'  => $input->getOption('from_name'),
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

}
?>
