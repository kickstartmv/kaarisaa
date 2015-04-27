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

$mandrill = new Mandrill('mFtdrqUWlD1aT0uEagwDMw'); //set to TEST API Key -> Change when switiching to production

class EmailSendCommand extends Command {
  
  protected function configure(){
      $this->setName("email:send")
           ->setDescription("Sends Emails via Mandrill")
           ->setHelp(<<<EOT
Refer docs - Email Send via Mandrill
EOT
)
          ->setDefinition(array(
            new InputOption('timestamp'),
            new InputOption('event'),
            new InputOption('text'),
            new InputOption('from_email'),
            new InputOption('from_name'),
            new InputOption('subject')
          ));
  }

  protected function execute(InputInterface $input, OutputInterface $output){
    $output->writeln("Hello world, " . $input->getOption('myArg2'));
    var_dump($input->getOption('myArg1'));
  }

}
?>
