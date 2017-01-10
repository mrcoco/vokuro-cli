<?php
/**
 * Created by PhpStorm.
 * User: dwiagus
 * Date: 29/12/16
 * Time: 20:57
 */

namespace App\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class GenerateModule extends Command
{
    /**
     * Console command config and argument
     */
    protected function configure()
    {
        $this
            ->setName('create:module')
            ->setDescription('Generate Module')
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'Module name to Generate'
            )
            ->addArgument(
                'table',
                InputArgument::REQUIRED,
                'DataBase Table name to Generate'
            )
            ->addArgument(
                'column',
                InputArgument::IS_ARRAY,
                'column name (column:type:value) '
            )
        ;
    }

    /**
     * Excecute console command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $names  = $input->getArgument('module');
        $module = BASE_PATH.'/modules';

        if (is_dir($module) && !is_writable($module)) {
            $output->writeln('The "%s" directory is not writable');
            return;
        }
        if(is_dir($module)){
            if(mkdir($module."/".strtolower($names),0755, true)){
                $this->createModuleClass($module,$names,$output);
                $this->createTable($input,$output);
                $this->createModel($input,$output);
                $this->createView($input,$output);
                $this->createController($input,$output);
                $this->createRoute($input,$output);
                $this->createConfig($names);
                $this->createAsset();
            }else{
                $output->writeln("Failed create Module");
            }
        }else{
            $output->writeln("Directory Module not Exsist");
        }
    }


    /**
     * Generate Module config
     * @param $module
     * @param $names
     * @param OutputInterface $output
     * @return mixed
     */
    public function createModuleClass($module, $names, OutputInterface $output)
    {
        $source = realpath(__DIR__ . '/../src/module.txt');
        $file = file_get_contents($source);
        $file = str_replace("!module", ucfirst($names), $file);
        $file = str_replace("!date",date('d/m/Y'),$file);
        $file = str_replace("!time",date('HH:mm:ss'),$file);
        if (!file_exists($module."/".$names."/Modules.php")) {
            $fh = fopen($module."/".$names."/Modules.php", "w");
            fwrite($fh, $file);
            fclose($fh);

            $className = ucfirst($names) . ".php";

            return $output->writeln("Created config $className in modules");
        } else {
            return $output->writeln("Class modules already Exists!");
        }
    }

    /**
     * Generate Table Database
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function createTable(InputInterface $input, OutputInterface $output)
    {
        $databse = $this->getApplication()->find('create:table');
        $databse_arguments = array(
            'command'   => 'create:table',
            'table'     => $input->getArgument('table'),
            'column'    => $input->getArgument('column')
        );
        $input_db   = new ArrayInput($databse_arguments);
        $return_db  = $databse->run($input_db,$output);
        return $output->writeln($return_db);
    }

    /**
     * Generate Model
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function createModel(InputInterface $input, OutputInterface $output)
    {
        $model = $this->getApplication()->find('create:model');
        $model_arguments = array(
            'command'   => 'create:model',
            'module'    => $input->getArgument('module'),
            'table'     => $input->getArgument('table'),
            'column'    => $input->getArgument('column')
        );

        $input_model = new ArrayInput($model_arguments);
        $return_model= $model->run($input_model,$output);
        $output->writeln($return_model);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function createView(InputInterface $input, OutputInterface $output)
    {
        $view = $this->getApplication()->find('create:view');
        $view_argument = array(
            'command'   => 'create:view',
            'module'    => $input->getArgument('module'),
            'controller' => $input->getArgument('table'),
            'action'     => 'index',
        );
        $input_view = new ArrayInput($view_argument);
        $return_view = $view->run($input_view,$output);
        return $output->writeln($return_view);
    }
    /**
     * Generate Controller
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function createController(InputInterface $input, OutputInterface $output)
    {
        $controller = $this->getApplication()->find('create:controller');
        $controller_arguments = array(
            'command'   => 'create:model',
            'module'    => $input->getArgument('module'),
            'table'     => $input->getArgument('table'),
            'column'    => $input->getArgument('column')
        );
        $input_controller   = new ArrayInput($controller_arguments);
        $return_controller  = $controller->run($input_controller,$output);
        return $output->writeln($return_controller);
    }

    /**
     * Generate Route
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function createRoute(InputInterface $input, OutputInterface $output)
    {
        $router = $this->getApplication()->find('create:router');
        $router_arguments = array(
            'command'       => 'create:router',
            'module'        => $input->getArgument('module'),
            'controller'    => $input->getArgument('table'),
            'action'        => 'index'
        );
        $input_router = new ArrayInput($router_arguments);
        $return_router = $router->run($input_router,$output);
        return $output->writeln($return_router);
    }

    /**
     * Add on module config array
     * @param $names
     */
    public function createConfig($names)
    {
        $config = include APP_PATH."/config/modules.php";
        array_push($config,$names);
        file_put_contents(APP_PATH."/config/modules.php", '<?php return [' ."'".implode("','",$config)."'".'];');
    }

    /**
     * Create jquery bootgrid Asset file
     */
    public function createAsset()
    {
        $css    = BASE_PATH."/public/css/";
        $js     = BASE_PATH."/public/js/";
        $source = realpath(__DIR__ . '/../assets/');
        $css_file   = file_get_contents($source."/jquery.bootgrid.css");
        $js_file    = file_get_contents($source."/jquery.bootgrid.js");
        $fajs_file  = file_get_contents($source."/jquery.bootgrid.fa.js");
        $fc = fopen($css."jquery.bootgrid.css", "w");
        fwrite($fc, $css_file);
        fclose($fc);
        $fj = fopen($js."jquery.bootgrid.js", "w");
        fwrite($fj, $js_file);
        fclose($fj);
        $fa = fopen($js."jquery.bootgrid.fa.js", "w");
        fwrite($fa, $fajs_file);
        fclose($fa);
    }
}