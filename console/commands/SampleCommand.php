<?php
namespace Kaarisaa\Console\Commands;

use Symfony\Component\Console\Command\Command; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class SampleCommand extends Command {
  
  protected function configure(){
    $this->setName("hello:world")
         ->setDescription("Displays a hello world message")
         ->setHelp(<<<EOT
Displays a customized hello world message with the parsed name

Usage:

<info>console hello:world --name="Rishy"</info>
EOT
)
         ->setDefinition(array(
            new InputOption('name','m',InputOption::VALUE_OPTIONAL,'Your name', "John Doe")
         ));
  }

  protected function execute(InputInterface $input, OutputInterface $output){
    $output->writeln("Hello world, " . $input->getOption('name'));
  }
}
?>
