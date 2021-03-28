<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerRun extends Command
{
    protected static $defaultName = "docker:run";

    protected function configure()
    {
        $this
            ->setDescription("Run docker-compose.yml")
            ->setHelp("This command runs a docker-compose.yml file and up containers");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Running docker-compose.yml</info>");
        $output->writeln("");
        $output->writeln("<info>Mount containers</info>");
        $output->writeln(shell_exec("docker-compose up -d"));
        $output->writeln("<info>Apache Server in http://localhost:8080</info>");
        $output->writeln("<info>PhpMyAdmin in http://localhost:8081</info>");
        $output->writeln("<info>MySQL Service in localhost:9906</info>");
        return Command::SUCCESS;
    }
}