<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SassWatch extends Command
{
    protected static $defaultName = "sass:watch";

    protected function configure(): void
    {
        $this
            ->setDescription("auto compile a sass file on change")
            ->addArgument("name", InputArgument::REQUIRED, "Name for sass file")
            ->setHelp("This command autocompile a sass file in app/sass");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $name .= str_contains($name, ".scss") ? "" : ".scss";

        $output->writeln("Watching sass file in app/sass/$name");

        if (!is_file("app/sass/$name")) {
            $output->writeln("<error>$name not is a valid file</error>");
            return self::FAILURE;
        }
        $cssName = str_replace('.scss', '.css', $name);
        exec("npm exec sass --style=compressed --watch app/sass/$name public_html/css/$cssName &");
        return self::SUCCESS;
    }
}
