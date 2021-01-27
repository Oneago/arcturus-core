<?php


namespace App\Models;


use BadMethodCallException;

class ExampleModel extends Connection
{

    public function get(int $id): ?object
    {
        // TODO: Implement get() method.
        throw new BadMethodCallException("Get not enable");
    }

    public function create(object $model): bool
    {
        // TODO: Implement create() method.
        throw new BadMethodCallException("Create not enable");
    }

    public function list(string $search = null): ?array
    {
        // TODO: Implement list() method.
        throw new BadMethodCallException("List not enable");
    }

    public function update(object $model): bool
    {
        // TODO: Implement update() method.
        throw new BadMethodCallException("Update not enable");
    }
}