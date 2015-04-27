<?php
namespace Kaarisaa\Console;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Application;

class KaarisaaConsole extends Application {

  protected $config;

  public function boot(){

    $configFile = __DIR__.'/../config.yml';

    $config = Yaml::parse(file_get_contents($configFile));

    $this->config = $config;

    $files = scandir(__DIR__ . "/commands");

    foreach($files as $file){

      if(!is_dir($file)){ // add command if not a directory
        $commandName = "Kaarisaa\\Console\\Commands\\".basename(substr($file,0,strpos($file,".")));

        $this->add(new $commandName); // hook up the command to application
      }
    }
  }

  public function setConfig($config=[]){
    $this->config = $config;
  }

  public function getConfig($key=null){
    
    return $key == null ? $this->config : $this->config[$key];

  }
}
?>
