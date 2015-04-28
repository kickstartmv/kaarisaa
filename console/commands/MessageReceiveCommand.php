<?php
namespace Kaarisaa\Console\Commands;

use Symfony\Component\Console\Command\Command; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class MessageReceiveCommand extends Command {
  
  protected function configure(){
      $this->setName("message:receive")
           ->setDescription("Receives Messages via Channels")
           ->setHelp(<<<EOT
Refer docs - Email Send via Mandrill
EOT
)
          ->setDefinition(array(
            new InputArgument('message',InputArgument::REQUIRED,'Message body'),
            new InputArgument('sender',InputArgument::REQUIRED,'Sender ID'),
            new InputArgument('timestamp',InputArgument::REQUIRED,'Received Timestamp'),
            new InputArgument('channel',InputArgument::REQUIRED,'Message Received channel'),
          ));
  }

  protected function execute(InputInterface $input, OutputInterface $output){

    $message = $input->getArgument('message');
    $timestamp = $input->getArgument('timestamp');
    $sender = $input->getArgument('sender');
    $channel = $input->getArgument('channel');

    $db = $this->getApplication()->DB();

    $result = $db->executeQuery("select * from message_channel where name = ? and direction = ? limit 1",[$channel,'Incoming']);
    
    if($result->rowCount() == 0){
      $output->writeln("Channel not registered");
      return;
    }

    $channel = $result->fetch();
    
    if($channel['status'] != 'Enabled'){
      $output->writeln("Channel not enabled");
      return;
    }

    $inbox = 1;

    $query = $db->executeQuery("insert into message_in (channel,inbox,sender,message,received_at,created_at) values(?,?,?,?,?,NOW())",[$channel['id'],$inbox,$sender,$message,$timestamp]);
    //file_put_contents('email_rcv.log', $data, FILE_APPEND | LOCK_EX);
    $output->writeln("Message added to database");

  }

}
?>
