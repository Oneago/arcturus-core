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

    protected function configure(): void
    {
        $this
            ->setDescription("Create a sass file or component")
            ->addArgument("name", InputArgument::REQUIRED, "Name for sass file")
            ->addOption("component", "c", InputOption::VALUE_NONE, "sass component create")
            ->addOption("dir", "d", InputOption::VALUE_OPTIONAL, "will be create sass file in directory, no use --component or -c options because no will be create folder")
            ->addOption("parent", "p", InputOption::VALUE_OPTIONAL, "sass parent to add import", "style.scss")
            ->setHelp("This command create a sass file in public_html/css, you can modify folder using --dir -d or --component -c");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = $input->getOption("dir");

        $name = $input->getArgument('name');
        $name .= str_contains($name, ".scss") ? "" : ".scss";
        $parent = $input->getOption("parent");
        $parent .= str_contains($parent, ".scss") ? "" : ".scss";

        if (!file_exists("app/sass") && !mkdir("app/sass") && !is_dir("app/sass")) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', "app/sass"));
        }
        if ($input->getOption("component")) {
            $this->createSass($output, "_$name", "components", $parent);
        } else if ($dir) {
            $name = ucfirst($name);
            $this->createSass($output, "_$dir$name", $dir, $parent);
        } else {
            $this->createSass($output, $name);
        }

        $output->writeln("<info>{$input->getArgument('name')} created!</info>");
        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @param string $name file name
     * @param string|null $folder folder to create
     * @param string|null $parent if isset folder require parent, parent is a sass file to add new file
     */
    private function createSass(OutputInterface $output, string $name, string $folder = null, string $parent = null): void
    {
        $path = "app/sass"; // $path change if $folder is set
        $userName = get_current_user();
        $date = date("d/m/Y H:i:s");

        if ($folder !== null) {
            if (!file_exists("app/sass/$folder") && !mkdir("app/sass/$folder") && !is_dir("app/sass/$folder")) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', "app/sass/$folder"));
            }
            $path .= "/$folder";
        }
        $output->writeln("Creating $path/$name");
        $fp = fopen("$path/$name", 'wb+');
        fwrite($fp, "/*" . PHP_EOL);
        fwrite($fp, " * $name" . PHP_EOL);
        fwrite($fp, " * Created by $userName" . PHP_EOL);
        fwrite($fp, " * $date" . PHP_EOL);
        fwrite($fp, " */" . PHP_EOL);
        fclose($fp);
        exec("git add $path/$name");

        // Add to parent
        if ($folder !== null) {
            $name = str_replace("_", "", $name);
            $fp = fopen("app/sass/$parent", 'ab+');
            fwrite($fp, PHP_EOL . "@import \"$folder/$name\";");
            fclose($fp);
        }
    }
}