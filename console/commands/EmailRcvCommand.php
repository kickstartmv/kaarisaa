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

$mandrill = new Mandrill('vJn9Xl7YhPsIK7HXezBU9Q'); //set to TEST API Key -> Change when switiching to production

class EmailRcvCommand extends Command {
  
  protected function configure(){
      $this->setName("email:rcv")
           ->setDescription("Receives Emails via Mandrill")
           ->setHelp(<<<EOT
Refer docs - Email Send via Mandrill
EOT
)
          ->setDefinition(array(
            new InputOption('msg'),
            new InputOption('ts'),
          ));
  }

  protected function execute(InputInterface $input, OutputInterface $output){

    $msg = $input->getOption('msg');

    $data[0] = "###########################################\n";
    $data[1] = "Time: ".$input->getOption('ts')."\n";
    $data[2] = "Msg: ".$msg['text']."\n";
    $data[4] = "###########################################\n";

    file_put_contents('email_rcv.log', $data, FILE_APPEND | LOCK_EX);


  }

}
?>
