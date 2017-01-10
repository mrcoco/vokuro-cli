<?php
/**
 * Created by PhpStorm.
 * User: dwiagus
 * Date: 10/01/17
 * Time: 14:12
 */

namespace App\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Phalcon\Db\Adapter\Pdo\Mysql as DbMysqlAdapter;
use Phalcon\Db\Adapter\Pdo\Postgresql as DbPgsqlAdapter;
use Phalcon\Db\Column;
use Phalcon\Db\Index;

class InstallDatabase extends Command
{

}