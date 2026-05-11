<?php

interface RepositoryInterface {
    public function selectWithFilter (array $values, array $dbColumnNames);
    public function insert(object $model): bool;
    public function selectDistinct (string $columnName) : ?array;
}