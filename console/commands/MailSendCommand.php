<?php


namespace Kaarisaa\Console\Commands;

require_once __DIR__ . '/../../hooks/mandrill/Send.php';

use Symfony\Component\Console\Command\Command; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use Symfony\Component\Console\Output\StreamOutput;


//$mailer = new \Kaarisaa\Drivers\Send();

//use Mandrill;


class MailSendCommand extends Command {
  
  protected function configure(){
      $this->setName("mail:send")
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
          $output->writeln("No email provided, ignoring...");
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


      $token = $this->getApplication()->getConfig('email_api_token');

      //loading email driver
      $email = new \Kaarisaa\Drivers\Send();   //$this->getApplication()->getConfig('email_driver');
      $output->writeln('Init. email driver with token: '.'<info>'.$token.'</info>');
      
      $email->setMessage($message['message']);
      $output->writeln('<info>Outbound email message body set</info>');

      $email->setSubject($message['title']);
      $output->writeln('<info>Outbound email message subject set</info>');

      $email->setFromEmail('nprelief@kickstart.mv');
      $output->writeln('Outbound email From Address set to: <info>'.$email->from_email.'</info>');

      $email->setFromName('nprelief');
      $output->writeln('Outbound email From Name set to: <info>'.$email->from_name.'</info>');

      $email->sendEmail($recipients,$token);
      $output->writeln('Prepped and handed over to mail driver');
      
    }
  }

}
?>
