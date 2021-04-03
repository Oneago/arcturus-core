<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerBuild extends Command
{
    protected static $defaultName = "docker:pull";

    protected function configure()
    {
        $this
            ->setDescription("Pull docker-compose.yml containers")
            ->setHelp("This command pull docker-compose.yml containers from docker hub");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Pulling docker-compose.yml</info>");
        $output->writeln("");
        $output->writeln(shell_exec("docker-compose build"));
        $output->writeln("<info>Pulling ok</info>");
        return Command::SUCCESS;
    }
}