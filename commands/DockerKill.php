<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerKill extends Command
{
    protected static $defaultName = "docker:kill";

    protected function configure()
    {
        $this
            ->setDescription("Kill docker-compose.yml")
            ->setHelp("This command kill a docker-compose.yml containers");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Killing docker-compose.yml</info>");
        $output->writeln("");
        $output->writeln(shell_exec("docker-compose kill"));
        return Command::SUCCESS;
    }
}