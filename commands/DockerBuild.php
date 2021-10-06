<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerBuild extends Command
{
    protected static $defaultName = "docker:build";

    protected function configure(): void
    {
        $this
            ->setDescription("Pull docker-compose.yml containers")
            ->setHelp("This command pull docker-compose.yml containers from docker hub");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Pulling docker-compose.yml</info>");
        $output->writeln("");
        exec("docker-compose build", $out, $code);
        $output->writeln($out);
        if ($code === 0) {
            $output->writeln("<info>Building ok</info>");
            return Command::SUCCESS;
        }

        $output->writeln("<error>Error running docker</error>");
        return Command::FAILURE;
    }
}