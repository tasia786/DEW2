<?php
//apends in query WHERE ... IN (...) + 
//return in $params the values to be placed in the query in order

function appendInQuery(array $values, array $dbColumnNames, string $name, array &$params)
{
    $query = "SELECT * FROM {$name} WHERE 1=1";
    $length = count($dbColumnNames);
    for ($i = 0; $i < $length; $i++) {
        $valueArray = explode(',', $values[$i]);
        $placeholders = implode(',', array_fill(0, count($valueArray), '?'));
        $query .= " AND $dbColumnNames[$i] IN ($placeholders)";
        $params = array_merge($params, $valueArray);
    }
    return $query;
}

function appendInQuery2(array $values, array $dbColumnNames, string $name, array &$params, ?int $pageNmb): string
{
    $query = "SELECT * FROM {$name} WHERE 1=1";
    $length = count($dbColumnNames);

    for ($i = 0; $i < $length; $i++) {
        $valueArray = is_array($values[$i]) ? $values[$i] : [$values[$i]]; // single value or array
        $placeholders = implode(',', array_fill(0, count($valueArray), '?'));
        $query .= " AND {$dbColumnNames[$i]} IN ($placeholders)";
        $params = array_merge($params, $valueArray);
    }

    if ($pageNmb !== null) {
        $pageSize = 10;
        $offset = ($pageNmb - 1) * $pageSize;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $pageSize;
        $params[] = $offset;
    }
    return $query;
}
