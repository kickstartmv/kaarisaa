<?php
/**
 * SMS Receiving Hook for Dhiraagu
 *
 * @author - Ahmed Ali <ajaaibu@gmail.com>
 * @date - 28-04-2015 09:27 PM
 */

require __DIR__.'/../../vendor/autoload.php';

use Kaarisaa\Senders\Dhiraagu;
use Kaarisaa\Console\KaarisaaConsole;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

$app = new KaarisaaConsole;
$app->boot();

$command = $app->get('message:receive');

$xml = simplexml_load_string(file_get_contents('php://input'));

$sms = Dhiraagu::byXML($xml);

$input = new ArrayInput([
  'command' => 'message:receive',
  'message' => $sms->getMessage(),
  'sender' => $sms->getNumber(),
  'timestamp' => date('Y-m-d H:i:s',time()),
  'channel' => 'Dhiraagu'
]);

$output = new ConsoleOutput();
$command->run($input,$output);
?>
