<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunPhpServer extends Command
{
    protected static $defaultName = "run:server";

    protected function configure(): void
    {
        $this
            ->setDescription("Run server with php")
            ->addArgument("address", InputArgument::REQUIRED, "Address for run php host")
            ->setHelp("This command runs a php server");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Running in http://{$input->getArgument('address')}</info>");
        $output->writeln(shell_exec("cd public_html && php -S {$input->getArgument('address')}"));
        $output->writeln("<info>Launch in {$input->getArgument('address')}</info>");
        return Command::SUCCESS;
    }
}