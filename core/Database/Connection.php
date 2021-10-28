<?php /** @noinspection PhpMissingFieldTypeInspection */


namespace Oneago\Arcturus\Core\Database;

use Exception;
use PDO;

abstract class Connection
{
    protected string $errorDetails;

    /**
     * @param array|null $PDOOptions
     * @return PDO
     */
    protected static function init(array $PDOOptions = null): PDO
    {
        $options = $PDOOptions ?? [
                PDO::ATTR_EMULATE_PREPARES => false, // turn off emulation mode for "real" prepared statements
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
            ];

        try {
            return new PDO("mysql:host={$_ENV["DB_HOST"]};port={$_ENV["DB_PORT"]};dbname={$_ENV["DB_NAME"]};charset=utf8", $_ENV["DB_USER"], $_ENV["DB_PASS"], $options);
        } catch (Exception $e) {
            die("Error connecting database - {$e->getMessage()}");
        }
    }

    /**
     * @return string|null return error string. If error not exist return null
     */
    public function getErrorDetails(): ?string
    {
        return $this->errorDetails;
    }

    abstract public function get(int $id): ?object;

    abstract public function list(string $search = null): ?array;

    abstract public function create(object $object): bool;

    abstract public function update(object $object): bool;

    abstract public function delete(int $id): bool;
}
