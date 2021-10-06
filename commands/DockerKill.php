<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerKill extends Command
{
    protected static $defaultName = "docker:kill";

    protected function configure(): void
    {
        $this
            ->setDescription("Kill docker-compose.yml")
            ->setHelp("This command kill a docker-compose.yml containers");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Killing docker-compose.yml</info>");
        $output->writeln("");
        exec("docker-compose kill", $out, $code);
        $output->writeln($out);
        if ($code === 0) {
            $output->writeln($out);
            return Command::SUCCESS;
        }

        $output->writeln("<error>Error killing docker</error>");
        return Command::FAILURE;
    }
}