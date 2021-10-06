<?php


namespace Oneago\Arcturus\Commands;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class RunRelease extends Command
{
    protected static $defaultName = "run:release";

    protected function configure(): void
    {
        $this
            ->setDescription("Create released file for production server")
            ->addArgument("name", InputArgument::OPTIONAL, "Name for zip file", "release")
            ->setHelp("This command create a zip file with files for production server");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Generating Release</info>");
        $this->recurse_copy(); // Copy project files
        $output->writeln(shell_exec("cd out && composer update --no-dev && composer install --no-dev")); // Download libraries

        $output->writeln("<info>Compressing files</info>");

        $zipDir = "release";
        $zipName = $input->getArgument('name');
        unlink("$zipDir/$zipName.zip"); // Delete old file
        if (!mkdir($zipDir) && !is_dir($zipDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $zipDir));
        }

        $zip = new ZipArchive;
        if ($zip->open("$zipDir/$zipName.zip", ZipArchive::CREATE) === TRUE) {
            $this->add_to_zip($zip);
            $zip->close();
        }

        $output->writeln("<info>Cleaning generated files</info>");
        $this->flush_folder();

        $pwd = getcwd();
        if ($this->isDebugMode()) {
            $output->writeln('<bg=RED;options=bold>Please consider put DEBUG_MODE = 0 in your .env file if this is a production release</>');
        }
        $output->writeln("<info>Release file in $pwd/$zipDir/$zipName.zip</info>");
        return self::SUCCESS;
    }

    private function recurse_copy(string $src = ".", string $dst = "out"): void
    {
        $dir = opendir($src);
        if (!mkdir($dst) && !is_dir($dst)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dst));
        }

        $excluded = ["Dockerfile", "docker-compose.yml", "composer.lock", "temp", "LICENSE", "ada", ".gitignore", "commands", ".git", "out", "vendor", ".idea", "postinit", ".DS_Store", "Dockerfile", "docker-compose.yml"];
        while (false !== ($file = readdir($dir))) {
            if (!in_array($file, $excluded, true) && ($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function add_to_zip(ZipArchive $zip): void
    {
        $src = "out";

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file

                // Remove out folder for zip write
                $file = str_replace("out/", "", $file);

                // Add current file to archive
                echo "add $file" . PHP_EOL;
                $zip->addFile($file);
            }
        }
    }

    private function flush_folder($src = "out"): void
    {
        if (is_dir($src)) {
            $objects = scandir($src);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== ".." && $object !== "released.zip") {
                    if (is_dir("$src/$object")) {
                        $this->flush_folder("$src/$object");
                    } else {
                        unlink("$src/$object");
                        echo "deleted $src/$object" . PHP_EOL;
                    }
                }
            }
            reset($objects);
            rmdir($src);
            echo "deleted $src" . PHP_EOL;
        }
    }

    private function isDebugMode(): bool
    {
        $dotenv = new DotEnvConfig();
        $dotenv->initConfigs();

        return $_ENV["DEBUG_MODE"];
    }
}