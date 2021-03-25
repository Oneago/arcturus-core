<?php


namespace Oneago\AdaConsole\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TypeScriptCompile extends Command
{
    protected static $defaultName = "tsc:compile";

    protected function configure()
    {
        $this
            ->setDescription("Compile TypeScript")
            ->setHelp("This command compile typescript folder in public_html/js");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Compiling typescript folder</info>");
        $output->writeln(shell_exec("node_modules/.bin/tsc ./public_html/js/*.ts && git add ./public_html/js"));
        $output->writeln("<info>Compile success</info>");
        return Command::SUCCESS;
    }
}