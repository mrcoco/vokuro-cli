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
        /*$it = new \RecursiveDirectoryIterator($directory,\RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($directory);
        $output->writeln("Module ".$module." Deleted");*/

        //$vendor = $composer->getConfig()->get("vendor-dir");
        //$file = file_get_contents(realpath(__DIR__ . '/../src')."/view.txt");
        $output->writeln(realpath(__DIR__ . '/../src/view.txt'));
    }
}