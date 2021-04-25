<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateApi extends Command
{
    protected static $defaultName = "make:api";

    protected function configure()
    {
        $this
            ->setDescription("Create a new api for this app")
            ->addArgument("api name", InputArgument::REQUIRED, "Name for use in api file")
            ->setHelp("This command create a new api passing a name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Creating {$input->getArgument('api name')}</info>");
        $output->writeln("<info>Wait a moment please...</info>");
        $output->writeln("");

        $apiName = ucfirst($input->getArgument('api name')) . "Api.php";
        $output->writeln("<info>Creating $apiName</info>");
        $this->createFile($apiName, __DIR__ . "/../templates/ExampleApi.php");

        $output->writeln("<info>$apiName Created!</info>");
        $output->writeln("");

        $output->writeln("<info>{$input->getArgument('api name')} file has created!</info>");
        return Command::SUCCESS;
    }

    /**
     * @param string $name
     * @param string $templatePath
     */
    private function createFile(string $name, string $templatePath): void
    {
        $savePath = "app/Http/Apis";
        $fp = fopen("$savePath/$name", 'wb+');

        $fileContent = file_get_contents($templatePath);

        $contents = str_replace(
            [
                "ExampleApi",
                "App\Http\Controllers",
                " is a example class, you can delete or use as a model example for your app"
            ],
            [
                str_replace(".php", "", $name),
                "App\Http\Apis",
                ""
            ], $fileContent
        );
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $savePath/$name");
    }
}