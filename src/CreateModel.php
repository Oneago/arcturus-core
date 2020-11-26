<?php


namespace Oneago\AdaConsole\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModel extends Command
{
    protected static $defaultName = "make:model";
    private string $modelName;

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

        $this->modelName = ucfirst($input->getArgument('model name')) . "Model.php";
        $output->writeln("<info>Creating {$this->modelName}</info>");
        $this->createFile($this->modelName, "https://raw.githubusercontent.com/Oneago/oneago-php-template/master/models/ExampleModel.php", "models");
        $output->writeln("<info>{$this->modelName} Created!</info>");
        $output->writeln("");

        $output->writeln("<info>{$input->getArgument('model name')} model has created!</info>");
        return Command::SUCCESS;
    }

    private function createFile(string $name, string $url, string $path)
    {
        $fp = fopen("$path/$name", "w+");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        $contents = str_replace(
            [
                "ExampleModel",
            ],
            [
                str_replace(".php", "", $this->modelName),
            ], $data);
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $path/$name");
    }
}