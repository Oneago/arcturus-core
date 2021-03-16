<?php


namespace Oneago\AdaConsole\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SassMaker extends Command
{
    protected static $defaultName = "sass:new";

    protected function configure()
    {
        $this
            ->setDescription("Create a sass file or component")
            ->addArgument("name", InputArgument::REQUIRED, "Name for sass file")
            ->addOption("component", "c", InputOption::VALUE_NONE, "sass component create")
            ->addOption("parent", "p", InputOption::VALUE_OPTIONAL, "sass parent to add import", "style.scss")
            ->setHelp("This command create a sass file in www/css");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $name .= str_contains($name, ".scss") ? "" : ".scss";
        $parent = $input->getOption("parent");
        $parent .= str_contains($parent, ".scss") ? "" : ".scss";
        $userName = get_current_user();
        $date = date("d/m/Y");

        @mkdir("www");
        @mkdir("www/css");
        if ($input->getOption("component")) {
            @mkdir("www/css/components");
            $name = "_$name";
            $output->writeln("<info>Creating in www/css/components/$name</info>");
            $fp = fopen("www/css/components/$name", "w+");
            fwrite($fp, "/*" . PHP_EOL);
            fwrite($fp, " * $name" . PHP_EOL);
            fwrite($fp, " * Created by $userName" . PHP_EOL);
            fwrite($fp, " * $date" . PHP_EOL);
            fwrite($fp, " */" . PHP_EOL);
            fclose($fp);
            exec("git add www/css/components/$name");


            $name = str_replace("_", "", $name);
            $fp = fopen("www/css/$parent", "a+");
            fwrite($fp, PHP_EOL . "@import \"components/$name\";");
            fclose($fp);

        } else {
            $output->writeln("<info>Creating in www/css/$name</info>");
            $fp = fopen("www/css/$name", "w+");
            fwrite($fp, "/*" . PHP_EOL);
            fwrite($fp, " * $name" . PHP_EOL);
            fwrite($fp, " * Created by $userName" . PHP_EOL);
            fwrite($fp, " * $date" . PHP_EOL);
            fwrite($fp, " */" . PHP_EOL);
            fclose($fp);
            exec("git add www/css/$name");
        }

        $output->writeln("<info>{$input->getArgument('name')} created!</info>");
        return Command::SUCCESS;
    }
}