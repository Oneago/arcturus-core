<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SassWatch extends Command
{
    protected static $defaultName = "sass:watch";

    protected function configure()
    {
        $this
            ->setDescription("auto compile a sass file on change")
            ->addArgument("name", InputArgument::REQUIRED, "Name for sass file")
            ->setHelp("This command autocompile a sass file in public_html/css");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $name .= str_contains($name, ".scss") ? "" : ".scss";

        $output->writeln("<info>Watching sass file in public_html/css/$name</info>");

        if (!is_file("public_html/css/$name")) {
            $output->writeln("<error>$name not is a valid file</error>");
            return self::FAILURE;
        }
        $cssName = str_replace('.scss', '.css', $name);
        exec("sass --style=compressed --watch public_html/css/$name public_html/css/$cssName &");
        return self::SUCCESS;
    }
}