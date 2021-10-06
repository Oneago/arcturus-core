<?php


namespace Oneago\Arcturus\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateApi extends Command
{
    protected static $defaultName = "make:api";

    protected function configure(): void
    {
        $this
            ->setDescription("Create a new api for this app")
            ->addArgument("api name", InputArgument::REQUIRED, "Name for use in api file")
            ->addOption("dir", "d", InputOption::VALUE_OPTIONAL, "Save view in a folder for pretty viewer")
            ->setHelp("This command create a new api passing a name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = ucfirst($input->getOption("dir"));

        $output->writeln("<info>Creating {$input->getArgument('api name')}</info>");
        $output->writeln("<info>Wait a moment please...</info>");
        $output->writeln("");

        $apiName = ($dir ?? "") . ucfirst($input->getArgument('api name')) . "Api.php";
        $output->writeln("<info>Creating $apiName</info>");

        if ($dir !== null) {
            $this->createFile($apiName, __DIR__ . "/../templates/ExampleApi.php", $dir);
        } else {
            $this->createFile($apiName, __DIR__ . "/../templates/ExampleApi.php", null);
        }


        $output->writeln("<info>$apiName Created!</info>");
        $output->writeln("");

        $output->writeln("<info>{$input->getArgument('api name')} file has created!</info>");
        return Command::SUCCESS;
    }

    /**
     * @param string $name
     * @param string $templatePath
     * @param string|null $newDirectory
     */
    private function createFile(string $name, string $templatePath, ?string $newDirectory): void
    {
        $savePath = "app/Http/Apis";
        if ($newDirectory !== null) {
            if (!mkdir("$savePath/$newDirectory") && !is_dir("$savePath/$newDirectory")) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', "$savePath/$newDirectory"));
            }
            $savePath = "$savePath/$newDirectory";
        }
        $fp = fopen("$savePath/$name", 'wb+');

        $fileContent = file_get_contents($templatePath);

        $contents = str_replace(
            [
                "ExampleApi",
                "App\Http\Apis",
                " is a example class, you can delete or use as a model example for your app"
            ],
            [
                str_replace(".php", "", $name),
                "App\Http\Apis" . ($newDirectory !== null ? "\\$newDirectory" : ''),
                ""
            ], $fileContent
        );
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $savePath/$name");
    }
}