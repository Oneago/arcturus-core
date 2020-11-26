<?php


namespace Oneago\AdaConsole\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMiddleware extends Command
{
    protected static $defaultName = "make:middleware";
    private string $middlewareName;

    protected function configure()
    {
        $this
            ->setDescription("Create a new middleware for this app")
            ->addArgument("middleware name", InputArgument::REQUIRED, "Name for use in middleware")
            ->setHelp("This command create a new middleware passing a name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Creating {$input->getArgument('middleware name')}</info>");
        $output->writeln("<info>Wait a moment please...</info>");
        $output->writeln("");

        $this->middlewareName = ucfirst($input->getArgument('middleware name')) . "Middleware.php";
        $output->writeln("<info>Creating {$this->middlewareName}</info>");
        $this->createFile($this->middlewareName, __DIR__ . "/../templates/ExampleMiddleware.php", "middlewares");
        $output->writeln("<info>{$this->middlewareName} Created!</info>");
        $output->writeln("");

        $output->writeln("<info>{$input->getArgument('middleware name')} middleware has created!</info>");
        return Command::SUCCESS;
    }

    private function createFile(string $name, string $templatePath, string $savePath)
    {
        $fp = fopen("$savePath/$name", "w+");

        $fileContent = file_get_contents($templatePath);

        $contents = str_replace(
            [
                "ExampleMiddleware",
            ],
            [
                str_replace(".php", "", $this->middlewareName),
            ], $fileContent);
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $savePath/$name");
    }
}