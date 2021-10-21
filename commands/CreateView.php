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

    protected function configure(): void
    {
        $this
            ->setDescription("Create a new view for this app")
            ->addArgument("view name", InputArgument::REQUIRED, "Name for use in view file and/or model file")
            ->addOption("no-controller", null, InputOption::VALUE_NONE, "If is set this option, controller isn't create")
            ->addOption("dir", "d", InputOption::VALUE_OPTIONAL, "Save view in a folder for pretty viewer, you can use multiple folder using '/'")
            ->setHelp("This command create a new view passing a name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = $input->getOption("dir");
        $allDirs = explode("/", $dir);
        $dirFileNamePrefix = implode(array_map("ucfirst", $allDirs)); // First capitalized on dir path

        $output->writeln("Creating {$input->getArgument('view name')}");
        $output->writeln("Wait a moment please...");
        $output->writeln("");

        // Create twig file based on template
        $viewName = $this->viewName = $dirFileNamePrefix . ucfirst($input->getArgument('view name')) . ".twig";
        $output->writeln("Creating $viewName");
        $vDir = strtolower($dir); // strtolower all dir path vDir = viewDir abbreviation
        $this->createFile($viewName, __DIR__ . "/../templates/example.twig", "views", $vDir, $output);
        $output->writeln("<info>$viewName Created!</info>");
        $output->writeln("");

        if (!$input->getOption('no-controller')) { // bool, if is true not create controller
            // Create controller PHP file
            $controllerName = $dirFileNamePrefix . ucfirst($input->getArgument('view name')) . "Controller.php";
            $output->writeln("Creating $controllerName");
            if ($dir !== null) { // if need create dir
                $cDir = implode("/", array_map("ucfirst", explode("/", $dir))); // ucfirst on all folders controller cDir = controllerDir abbreviation
                $this->createFile($controllerName, __DIR__ . "/../templates/ExampleController.php", "app/Http/Controllers", $cDir, $output);
            } else {
                $this->createFile($controllerName, __DIR__ . "/../templates/ExampleController.php", "app/Http/Controllers", null, $output);
            }
            $output->writeln("<info>$controllerName Created!</info>");
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
     * @param OutputInterface $output
     */
    private function createFile(string $name, string $templatePath, string $savePath, ?string $newDirectory, OutputInterface $output): void
    {
        $controllerNamespace = str_replace("/", "\\", $savePath);
        if ($newDirectory !== null) {
            $savePath = "$savePath/$newDirectory";
            GeneralFunctions::makeFolder($savePath, $output, false);
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
                "App\Http\Controllers" . ($newDirectory !== null ? "\\$controllerNamespace" : ""),
                ""
            ], $fileContent
        );
        fwrite($fp, $contents);
        fclose($fp);
        exec("git add $savePath/$name");
    }
}