<?php


namespace Oneago\AdaConsole\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SassCompiler extends Command
{
    protected static $defaultName = "sass:compile";

    protected function configure()
    {
        $this
            ->setDescription("Compile a sass file")
            ->addArgument("name", InputArgument::REQUIRED, "Name for sass file")
            ->setHelp("This command compile a sass file in www/css");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $name .= str_contains($name, ".scss") ? "" : ".scss";

        $output->writeln("<info>Compiling sass file in www/css/$name</info>");

        if (!is_file("www/css/$name")) {
            $output->writeln("<error>$name not is a valid file</error>");
            return self::FAILURE;
        }
        $cssName = str_replace('.scss', '.css', $name);
        exec("sass --style=compressed www/css/$name www/css/$cssName");
        exec("git add www/css/$cssName");
        exec("git add www/css/$cssName.map");
        return self::SUCCESS;
    }
}