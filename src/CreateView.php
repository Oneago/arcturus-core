<?php


namespace Oneago\AdaConsole\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateView extends Command
{
    protected static $defaultName = "make:view";
    private string $viewName;

    protected function configure()
    {
        $this
            ->setDescription("Create a new view for this app")
            ->addArgument("view name", InputArgument::REQUIRED, "Name for use in view file and model file")
            ->setHelp("This command create a new view passing a name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Creating {$input->getArgument('view name')}</info>");
        $output->writeln("<info>Wait a moment please...</info>");
        $output->writeln("");

        $viewName = $this->viewName = $input->getArgument('view name') . ".twig";
        $output->writeln("<info>Creating {$viewName}</info>");
        $this->createFile($viewName, __DIR__ . "/../templates/example.twig", "views");
        $output->writeln("<info>{$viewName} Created!</info>");
        $output->writeln("");

        $controllerName = ucfirst($input->getArgument('view name')) . "Controller.php";
        $output->writeln("<info>Creating {$controllerName}</info>");
        $this->createFile($controllerName, __DIR__ . "/../templates/ExampleController.php", "controllers");
        $output->writeln("<info>{$controllerName} Created!</info>");
        $output->writeln("");

        $output->writeln("<info>{$input->getArgument('view name')} view has created!</info>");
        return Command::SUCCESS;
    }

    private function createFile(string $name, string $templatePath, string $savePath)
    {
        $fp = fopen("$savePath/$name", "w+");

        $fileContent = file_get_contents($templatePath);

        $contents = str_replace(
            [
                "example.twig",
                "ExampleController",
                " is a example class, you can delete or use as a model example for your app"
            ],
            [
                $this->viewName,
                str_replace(".php", "", $name),
                ""
            ], $fileContent
        );
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $savePath/$name");
    }
}