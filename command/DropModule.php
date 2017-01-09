<?php
/**
 * Created by PhpStorm.
 * User: dwiagus
 * Date: 09/01/17
 * Time: 11:18
 */

namespace App\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Phalcon\Db\Adapter\Pdo\Mysql as DbMysqlAdapter;
use Phalcon\Db\Adapter\Pdo\Postgresql as DbPgsqlAdapter;

class DropModule extends Command
{
    protected function configure()
    {
        $this
            ->setName('drop:module')
            ->setDescription('Remove Module')
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'Module name to remove'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module     = $input->getArgument('module');
        $module_dir = BASE_PATH.'/modules';
        $directory  = $module_dir.DIRECTORY_SEPARATOR.$module;
        $this->delDirectory($directory);
        $this->delConfig($module);
        $this->dropTable($module);
        $output->writeln("Module ".$module." Deleted");
    }

    /*
     *
     */
    public function dropTable($module)
    {
        $config = include APP_PATH."/config/config.php";

        if($config->database->adapter == 'PgSql'){
            $db = new DbPgsqlAdapter([
                'host'      => $config->database->host,
                'username'  => $config->database->username,
                'password'  => $config->database->password,
                'dbname'    => $config->database->dbname
            ]);
        }else{
            $db = new DbMysqlAdapter([
                'host'      => $config->database->host,
                'username'  => $config->database->username,
                'password'  => $config->database->password,
                'dbname'    => $config->database->dbname
            ]);
        }
        return $db->dropTable($module);
    }

    public function delConfig($module){
        $config = include APP_PATH."/config/modules.php";
        if(($key = array_search($module, $config)) !== false) {
            unset($config[$key]);
        }
        file_put_contents(APP_PATH."/config/modules.php", '<?php return [' ."'".implode("','",$config)."'".'];');
    }

    public function delDirectory($directory)
    {
        $it = new \RecursiveDirectoryIterator($directory,\RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($directory);
    }
}