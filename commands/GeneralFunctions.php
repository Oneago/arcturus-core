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
     */
    public static function makeFolder(string $name, OutputInterface $output): void
    {
        if (!file_exists($name)) {
            $output->writeln(sprintf("[%s] Creating $name folder...", self::getHour()));
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