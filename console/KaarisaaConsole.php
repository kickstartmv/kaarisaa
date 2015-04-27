<?php
namespace Kaarisaa\Console;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Application;

class KaarisaaConsole extends Application {

  protected $config;

  protected $conn;

  public function boot(){

    $configFile = __DIR__.'/../config.yml';

    $config = Yaml::parse(file_get_contents($configFile));

    $this->config = $config;

    // setup doctrine config

    $dConfig = new \Doctrine\DBAL\Configuration();

    $connectionParams = [
      'dbname' => $this->getConfig('db_name'),
      'user' => $this->getConfig('db_user'),
      'password' => $this->getConfig('db_pass'),
      'host' => $this->getConfig('db_server'),
      'driver' => $this->getConfig('db_driver')
    ];

    // establish doctrine db connection
    $this->conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $dConfig);

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

  public function DB(){
    return $this->conn;
  }
}
?>
