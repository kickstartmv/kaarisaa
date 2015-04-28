<?php
namespace Kaarisaa\Senders;
/**
 * Message Sender Interface
 * @author - Ahmed Ali <ajaaibu@gmail.com>
 * @date - 28-04-2015 21:22 PM
 */

abstract class MessageSender{

   abstract public function sendMessage($message,$number);

}
?>
