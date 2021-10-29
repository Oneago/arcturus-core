<?php


namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SassCompiler extends Command
{
    protected static $defaultName = "sass:compile";

    protected function configure(): void
    {
        $this
            ->setDescription("Compile all sass files")
            ->setHelp("This command compile all sass files in to app/sass");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parentDir = "app/sass/";

        $output->writeln("Compiling sass files in to public_html/css");
        $output->writeln("");

        $scan = scandir($parentDir);
        foreach ($scan as $file) {
            if (!is_dir("$parentDir/$file") && $file !== "." && $file !== "..") {
                $sassMIMECheck = substr($file, -5) === ".scss";
                $isSassComponent = $file[0] === "_";
                if ($sassMIMECheck && !$isSassComponent) {
                    GeneralFunctions::makeFolder("public_html/css", $output, false);
                    $output->writeln("Compiling $file");

                    $cssName = str_replace('.scss', '.css', $file);
                    exec("npx sass --style=compressed app/sass/$file public_html/css/$cssName", $out, $code);
                    $output->writeln($out);

                    if ($code === 0) {
                        $output->writeln("<info>$file compile succcessful on public/css/$cssName</info>");
                        $output->writeln("");
                    } else {
                        $output->writeln("<error>Error compiling $file</error>");
                        return Command::FAILURE;
                    }
                }
            }
        }

        $output->writeln("<info>Sass compile finish successful</info>");
        return self::SUCCESS;
    }
}
