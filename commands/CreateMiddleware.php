<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMiddleware extends Command
{
    protected static $defaultName = "make:middleware";

    protected function configure(): void
    {
        $this
            ->setDescription("Create a new middleware for this app")
            ->addArgument("middleware name", InputArgument::REQUIRED, "Name for use in middleware")
            ->setHelp("This command create a new middleware passing a name");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Creating {$input->getArgument('middleware name')}</info>");
        $output->writeln("<info>Wait a moment please...</info>");
        $output->writeln("");

        $middlewareName = ucfirst($input->getArgument('middleware name')) . "Middleware.php";
        $output->writeln("<info>Creating $middlewareName</info>");
        $this->createFile($middlewareName, __DIR__ . "/../templates/ExampleMiddleware.php");
        $output->writeln("<info>$middlewareName Created!</info>");
        $output->writeln("");

        $output->writeln("<info>{$input->getArgument('middleware name')} middleware has created!</info>");
        return Command::SUCCESS;
    }

    /**
     * @param string $name
     * @param string $templatePath
     */
    private function createFile(string $name, string $templatePath): void
    {
        $fp = fopen("app/Http/Middlewares/$name", 'wb+');

        $fileContent = file_get_contents($templatePath);

        $contents = str_replace(
            [
                "ExampleMiddleware",
            ],
            [
                str_replace(".php", "", $name),
            ], $fileContent
        );
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add app/Http/Middlewares/$name");
    }
}