<?php


namespace Oneago\AdaConsole\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModel extends Command
{
    protected static $defaultName = "make:model";

    protected function configure()
    {
        $this
            ->setDescription("Create a new model for this app")
            ->addArgument("model name", InputArgument::REQUIRED, "Name for use in model")
            ->setHelp("This command create a new model passing a name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Creating {$input->getArgument('model name')}</info>");
        $output->writeln("<info>Wait a moment please...</info>");
        $output->writeln("");

        $modelName = ucfirst($input->getArgument('model name')) . "Model.php";
        $output->writeln("<info>Creating {$modelName}</info>");
        $this->createFile($modelName, __DIR__ . "/../templates/ExampleModel.php", "models");
        $output->writeln("<info>{$modelName} Created!</info>");
        $output->writeln("");

        $output->writeln("<info>{$input->getArgument('model name')} model has created!</info>");
        return Command::SUCCESS;
    }

    private function createFile(string $name, string $templatePath, string $savePath)
    {
        $fp = fopen("$savePath/$name", "w+");

        $fileContent = file_get_contents($templatePath);

        $contents = str_replace(
            [
                "ExampleModel",
            ],
            [
                str_replace(".php", "", $name),
            ], $fileContent
        );
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $savePath/$name");
    }
}