<?php
/**
 * Created by PhpStorm.
 * User: dwiagus
 * Date: 02/01/17
 * Time: 16:04
 */

namespace App\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateView extends  Command
{
    protected function configure()
    {
        $this
            ->setName('create:views')
            ->setDescription('Generate Model Class')
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'Module name to Generate'
            )
            ->addArgument(
                'controller',
                InputArgument::REQUIRED,
                'Controller name to Generate'
            )
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'Action name To Generate '
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module     = $input->getArgument('module');
        $controller = $input->getArgument('controller');
        $action     = $input->getArgument('action');
        $directory  = BASE_PATH.'/modules';

        $source     = realpath(__DIR__ . '/../src/view.txt');
        //============== create index view ================//
        $view = $directory."/".strtolower($module)."/views";
        if(! mkdir($view,0755, true)){
            $output->writeln("Failed to create Views Directory");
        }
        $file = file_get_contents($source);
        $file = str_replace("oke", ucfirst($action), $file);
        if (!file_exists($view."/".ucfirst($action).".volt")) {
            if($action == 'index'){
                $fm = fopen($view."/main.volt", "w");
                fwrite($fm, "{{ content() }}");
                fclose($fm);
            }
            $fh = fopen($view."/".strtolower($action). ".volt", "w");
            fwrite($fh, $file);
            fclose($fh);

            $className = ucfirst($action) . ".volt";

            $output->writeln("Created $className views in modules ".$module);
        } else {
            $output->writeln("Views already Exists!");
        }
    }
}