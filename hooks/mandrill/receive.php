<?php


require_once __DIR__ .'/../../vendor/autoload.php';

use Kaarisaa\Console\KaarisaaConsole;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

use Symfony\Component\Console\Input\ArrayInput;

//use Mandrill;

$mandrill = new Mandrill('mFtdrqUWlD1aT0uEagwDMw');


$email = array();

if(!empty($_POST)){

  $posted_data = json_decode($_POST['mandrill_events'],true);

  $app = new KaarisaaConsole();
  $app->boot();
  $command = $app->get('email:rcv');
  
  foreach($posted_data as $email_data){
    $input = new ArrayInput(array(
                          'command' => 'email:rcv',
                          '--ts'    => $email_data['ts'],
                          '--msg'   => $email_data['msg']
                          )
                        );


    $output = new ConsoleOutput();
  }

  $command->run($input,$output);

}
else
{

  $email_json = file_get_contents('messag.json', FILE_USE_INCLUDE_PATH);

  $email_data = json_decode($email_json);
  

  $app = new KaarisaaConsole();
  $app->boot();
  $command = $app->get('email:rcv');



  $input = new ArrayInput(array(
                          'command' => 'email:rcv',
                          '--from_email'=> $email_data->mail_from,
                          '--msg'       => $email_data->raw_message
                          )
                        );

  $output = new ConsoleOutput();

  //$command->run($input,$output);


}



?>
