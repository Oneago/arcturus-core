<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TypeScriptCompile extends Command
{
    protected static $defaultName = "tsc:compile";

    protected function configure(): void
    {
        $this
            ->setDescription("Compile TypeScript")
            ->setHelp("This command compile typescript folder in public_html/js");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Compiling typescript folder");
        $output->writeln(shell_exec("node_modules/.bin/tsc ./app/typescript/*.ts"));
        $output->writeln("<info>Compile success</info>");
        return Command::SUCCESS;
    }
}