<?php


namespace Oneago\Arcturus\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateView extends Command
{
    protected static $defaultName = "make:view";
    private string $viewName;

    protected function configure()
    {
        $this
            ->setDescription("Create a new view for this app")
            ->addArgument("view name", InputArgument::REQUIRED, "Name for use in view file and/or model file")
            ->addOption("no-controller", null, InputOption::VALUE_NONE, "If is set this option, controller isn't create")
            ->addOption("dir", "d", InputOption::VALUE_OPTIONAL, "Save view in a folder for pretty viewer")
            ->setHelp("This command create a new view passing a name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = $input->getOption("dir");

        $output->writeln("<info>Creating {$input->getArgument('view name')}</info>");
        $output->writeln("<info>Wait a moment please...</info>");
        $output->writeln("");

        $viewName = $this->viewName = ucfirst($dir ?? "") . ucfirst($input->getArgument('view name')) . ".twig";
        $output->writeln("<info>Creating {$viewName}</info>");
        $this->createFile($viewName, __DIR__ . "/../templates/example.twig", "views", $dir);
        $output->writeln("<info>{$viewName} Created!</info>");
        $output->writeln("");

        if (!$input->getOption('no-controller')) {
            $controllerName = ucfirst($dir ?? "") . ucfirst($input->getArgument('view name')) . "Controller.php";
            $output->writeln("<info>Creating {$controllerName}</info>");
            if ($dir !== null) {
                $cDir = ucfirst($dir);
                $this->createFile($controllerName, __DIR__ . "/../templates/ExampleController.php", "app/Http/Controllers", $cDir);
            } else {
                $this->createFile($controllerName, __DIR__ . "/../templates/ExampleController.php", "app/Http/Controllers", null);
            }
            $output->writeln("<info>{$controllerName} Created!</info>");
            $output->writeln("");
        }

        $output->writeln("<info>{$input->getArgument('view name')} view has created!</info>");
        return Command::SUCCESS;
    }

    /**
     * @param string $name
     * @param string $templatePath
     * @param string $savePath
     * @param string|null $newDirectory
     */
    private function createFile(string $name, string $templatePath, string $savePath, ?string $newDirectory): void
    {
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
                "example.twig",
                "ExampleController",
                "App\Http\Controllers",
                " is a example class, you can delete or use as a model example for your app"
            ],
            [
                $this->viewName,
                str_replace(".php", "", $name),
                "App\Http\Controllers" . ($newDirectory !== null ? "\\$newDirectory" : ""),
                ""
            ], $fileContent
        );
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $savePath/$name");
    }
}