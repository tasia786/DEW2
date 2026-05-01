<?php

interface RepositoryInterface {
    public function selectWithFilter (array $values, array $dbColumnNames);
}