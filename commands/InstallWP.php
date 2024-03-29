<?php

namespace Oneago\Arcturus\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallWP extends Command
{
    protected static $defaultName = "install:wordpress";

    protected function configure(): void
    {
        $this
            ->setDescription("Install Wordpress Core")
            ->setHelp("Install latest version for wordpress to arcturus")
            ->addOption("dir", "d", InputOption::VALUE_OPTIONAL, "Name of directory to install on public_html", "blog")
            ->addOption("locale", "l", InputOption::VALUE_OPTIONAL, "Language and country to install in format <language_country>, language=ISO 639-1 country=ISO 3166-1", "en_US");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf("<info>[%s] Installing WordPress on Arcturus Project</info>", GeneralFunctions::getHour()));

        $pharDir = 'out';
        GeneralFunctions::makeFolder($pharDir, $output);

        if (!file_exists("$pharDir/wp-cli.phar")) {
            $output->writeln(sprintf("<comment>[%s] Downloading WP-CLI...</comment>", GeneralFunctions::getHour()));
            $content = file_get_contents('https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar');
            $output->writeln(sprintf("<comment>[%s] Storing WP-CLI...</comment>", GeneralFunctions::getHour()));
            file_put_contents("$pharDir/wp-cli.phar", $content);
            $output->writeln(sprintf("<comment>[%s] Enabling execution to WP-CLI...</comment>", GeneralFunctions::getHour()));
            chmod("$pharDir/wp-cli.phar", 0775);
        } else {
            $output->writeln(sprintf("<comment>[%s] WP-CLI already downloaded</comment>", GeneralFunctions::getHour()));
        }
        $output->writeln('');

        $installationDir = "public_html/" . $input->getOption('dir');
        $contentPath = "public_html/wp-content";
        $themesPath = "$contentPath/themes";
        $pluginsPath = "$contentPath/plugins";

        GeneralFunctions::makeFolder($installationDir, $output);
        GeneralFunctions::makeFolder($contentPath, $output);
        GeneralFunctions::makeFolder($themesPath, $output);
        GeneralFunctions::makeFolder($pluginsPath, $output);
        $output->writeln('');

        $this->gitignoreControl($installationDir, $output);
        $output->writeln('');
        $output->writeln("<comment>Downloading latest version of WordPress</comment>");
        $output->writeln('');

        $locale = $input->getOption("locale");
        $wp = "php $pharDir/wp-cli.phar";
        $cmd = "$wp core download --path=$installationDir --locale=$locale";


        exec($cmd, $out, $code);
        $output->writeln($out);

        if ($code === 0) {
            $output->writeln(sprintf("<info>[%s] WordPress download OK</info>", GeneralFunctions::getHour()));

            $destinyPlugin = "$contentPath/plugins";
            $destinyThemes = "$contentPath/themes";
            if (!file_exists($destinyPlugin)) {
                rename("$installationDir/wp-content/plugins", $destinyPlugin);
                unlink("$destinyPlugin/index.php");
            }
            if (!file_exists($destinyThemes)) {
                rename("$installationDir/wp-content/themes", $destinyThemes);
                unlink("$destinyThemes/index.php");
            }

            shell_exec("git add $contentPath");

            file_put_contents("$installationDir/wp-config.php", "<?php require_once __DIR__ . '/../../config/wp-config.php';");
            $this->wpConfigGenerate($installationDir, $output);

            $output->writeln("<comment>Check public_html/wp-content/themes folder before commit!!</comment>");
            $output->writeln(sprintf("<info>[%s] WordPress installation OK</info>", GeneralFunctions::getHour()));
            return Command::SUCCESS;
        }

        $output->writeln(sprintf("<error>[%s] WordPress installation ERROR</error>", GeneralFunctions::getHour()));
        return Command::FAILURE;
    }

    /**
     * @param string $path
     * @param OutputInterface $output
     */
    private function gitignoreControl(string $path, OutputInterface $output): void
    {
        $line = "\n###Ignore Wordpress installation (autogenerated by ada console)\n/$path\n";

        $content = file_get_contents(".gitignore");
        if (!strpos($content, $line)) {
            file_put_contents(".gitignore", $content . $line);
            $output->writeln(sprintf("<comment>[%s] WordPress ignored on .gitignore</comment>", GeneralFunctions::getHour()));
        } else {
            $output->writeln(sprintf("<comment>[%s] WordPress already ignored for git</comment>", GeneralFunctions::getHour()));
        }
    }

    /**
     * @param string $path
     * @param OutputInterface $output
     */
    private function wpConfigGenerate(string $path, OutputInterface $output): void
    {
        if (!file_exists("config/wp-config.php")) {
            $output->writeln(sprintf("<comment>[%s] Generating wp-config.php on config folder</comment>", GeneralFunctions::getHour()));

            $keys = file_get_contents("https://api.wordpress.org/secret-key/1.1/salt/");
            $fileContent = file_get_contents(__DIR__ . "/../templates/wp-config-sample.php");

            $fileContent = str_replace(
                [
                    "define('AUTH_KEY', '');\ndefine('SECURE_AUTH_KEY', '');\ndefine('LOGGED_IN_KEY', '');\ndefine('NONCE_KEY', '');\ndefine('AUTH_SALT', '');\ndefine('SECURE_AUTH_SALT', '');\ndefine('LOGGED_IN_SALT', '');\ndefine('NONCE_SALT', '');",
                    "<<instPath>>"
                ],
                [
                    $keys,
                    $path
                ], $fileContent
            );

            file_put_contents("config/wp-config.php", $fileContent);
            shell_exec("git add config/wp-config.php");
        } else {
            $output->writeln(sprintf("<comment>[%s] wp-config.php already exist</comment>", GeneralFunctions::getHour()));
        }
    }
}
