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

class SMSOutCommand extends Command {
  
  protected function configure(){
    $this->setName("sms:send")
         ->setDescription("Sends the SMS's in the outgoing queue")
         ->setDefinition(array(
            new InputOption('limit','l',InputOption::VALUE_OPTIONAL,'Send Limit', 10)
         ))
         ->setHelp(<<<EOT
Sends the SMS's in the outgoing queue

Usage:

<info>console sms:send </info>

You can also set the number of sms to send per run
<info>console sms:send l 10</info>

This command can be configured run in the background as a crontab
EOT
);
  }

  protected function execute(InputInterface $input, OutputInterface $output){
    $output->writeln("Hello world, " . $input->getOption('name'));
  }
}
?>
