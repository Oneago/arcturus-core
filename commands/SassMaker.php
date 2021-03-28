<?php


namespace Oneago\Arcturus\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SassMaker extends Command
{
    protected static $defaultName = "sass:new";

    protected function configure()
    {
        $this
            ->setDescription("Create a sass file or component")
            ->addArgument("name", InputArgument::REQUIRED, "Name for sass file")
            ->addOption("component", "c", InputOption::VALUE_NONE, "sass component create")
            ->addOption("parent", "p", InputOption::VALUE_OPTIONAL, "sass parent to add import", "style.scss")
            ->setHelp("This command create a sass file in public_html/css");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $name .= str_contains($name, ".scss") ? "" : ".scss";
        $parent = $input->getOption("parent");
        $parent .= str_contains($parent, ".scss") ? "" : ".scss";
        $userName = get_current_user();
        $date = date("d/m/Y");

        if (!mkdir("public_html") && !is_dir("public_html")) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', "public_html"));
        }
        if (!mkdir("public_html/css") && !is_dir("public_html/css")) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', "public_html/css"));
        }
        if ($input->getOption("component")) {
            if (!mkdir("public_html/css/components") && !is_dir("public_html/css/components")) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', "public_html/css/components"));
            }
            $name = "_$name";
            $output->writeln("<info>Creating in public_html/css/components/$name</info>");
            $fp = fopen("public_html/css/components/$name", 'wb+');
            fwrite($fp, "/*" . PHP_EOL);
            fwrite($fp, " * $name" . PHP_EOL);
            fwrite($fp, " * Created by $userName" . PHP_EOL);
            fwrite($fp, " * $date" . PHP_EOL);
            fwrite($fp, " */" . PHP_EOL);
            fclose($fp);
            exec("git add public_html/css/components/$name");


            $name = str_replace("_", "", $name);
            $fp = fopen("public_html/css/$parent", 'ab+');
            fwrite($fp, PHP_EOL . "@import \"components/$name\";");
            fclose($fp);

        } else {
            $output->writeln("<info>Creating in public_html/css/$name</info>");
            $fp = fopen("public_html/css/$name", 'wb+');
            fwrite($fp, "/*" . PHP_EOL);
            fwrite($fp, " * $name" . PHP_EOL);
            fwrite($fp, " * Created by $userName" . PHP_EOL);
            fwrite($fp, " * $date" . PHP_EOL);
            fwrite($fp, " */" . PHP_EOL);
            fclose($fp);
            exec("git add public_html/css/$name");
        }

        $output->writeln("<info>{$input->getArgument('name')} created!</info>");
        return Command::SUCCESS;
    }
}