<?php

namespace Oneago\Arcturus\Commands;

use DateTime;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class GeneralFunctions
{
    /**
     * @param string $name
     * @param OutputInterface $output
     * @param bool $showHour
     */
    public static function makeFolder(string $name, OutputInterface $output, bool $showHour = true): void
    {
        if (!file_exists($name)) {
            if ($showHour) {
                $output->writeln(sprintf("[%s] Creating $name folder...", self::getHour()));
            } else {
                $output->writeln("Creating $name folder...");
            }
            if (!mkdir($name) && !is_dir($name)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $name));
            }
        }
    }

    /**
     * @return string
     */
    public static function getHour(): string
    {
        return (new DateTime())->format('Y-m-d H:i:s');
    }
}