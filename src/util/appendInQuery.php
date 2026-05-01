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
