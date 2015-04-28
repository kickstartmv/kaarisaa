<?php
namespace Kaarisaa\Console\Commands;

/**
 * Command to Send SMS in the outgoing queue
 * @author - Ahmed Ali <ajaaibu@gmail.com>
 * @date - 27-04-2015 04:32 AM
 */

use Symfony\Component\Console\Command\Command; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class SMSSendCommand extends Command {
  
  protected function configure(){
    $this->setName("sms:send")
         ->setDescription("Sends the SMS's in the outgoing queue")
         ->setDefinition(array(
            new InputArgument('limit',InputArgument::OPTIONAL,'Send Limit', 10)
         ))
         ->setHelp(<<<EOT
Sends the SMS's in the outgoing queue

Usage:

<info>console sms:send </info>

You can also set the number of sms to send per run
<info>console sms:send 10</info>

This command can be configured run in the background as a crontab
EOT
);
  }

  protected function execute(InputInterface $input, OutputInterface $output){
    $sender = $this->getApplication()->getConfig('sms_driver');

    $sms = new $sender($this->getApplication()->getConfig('sms_user'),$this->getApplication()->getConfig('sms_pass'));

    $db = $this->getApplication()->DB();
  
    $messages = $db->fetchAll("select * from message_out limit ?",[$input->getArgument('limit')]);
    
    if($messages->rowCount()){
      foreach($messages as $message){
        
        $subscribers = [];

        // get subscribers for the persons
        $personSubs = $db->fetchAll("select user_id from subscriptions where sub_person in (?)",[$message['persons']]);
        
        foreach($personSubs as $pSub){
          array_push($subscribers,$pSub['user_id']);
        }

        // get subscribers to the location
        $locationSubs = $db->fetchAll("select user_id from subscriptions where sub_location in (?)",[$message['locations']]);

        foreach($locationSubs as $lSub){
          array_push($subscribers,$lSub['user_id']);
        }

        $ignore = implode($subscribers,",");

        $others = $db->fetchAll("select user_id from subscriptions where sub_location is null and sub_person is null and user_id NOT IN (?)",[$ignore]);

        foreach($others as $oSub){
          array_push($subscribers,$oSub['user_id']);
        }

        $finalList = array_unique($subscribers);


        foreach($finalList as $sub){
          $query = $db->executeQuery("select * from user where id = ? limit 1",[$sub]);
          
          $user = $query->fetch();

          if(empty($user['phone'])){
            $output->writeln("No phone number, ignoring...");
          }
          else{
            $output->writeln("Sending to " . $user['username']. " - " . $user['phone']);
            $sms->sendMessage($message['message'],$user['phone']);
          }
        }

      }
    }
    else{
      $output->writeln("No messages to send");
    }

  }
}
?>
