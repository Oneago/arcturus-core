<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerPull extends Command
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
        exec("docker-compose pull", $out, $code);
        $output->writeln($out);
        if ($code === 0) {
            $output->writeln($out);
            $output->writeln("<info>Pulling ok</info>");
            return Command::SUCCESS;
        }

        $output->writeln("<error>Error pulling docker</error>");
        return Command::FAILURE;
    }
}