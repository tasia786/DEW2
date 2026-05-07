<?php

function initSchema(PDO $db): void
{
    $tables = [
        'seizures', 'emergencies', 'campaigns_projects', 'prevention_activities', 
        'crimes_general', 'crimes_sex', 'crimes_law', 'crimes_sentences', 'criminal_groups', 
        'users'
    ];

    foreach ($tables as $table) {
        $db->exec("DROP TABLE IF EXISTS {$table}");
    }

    $db->exec("CREATE TABLE seizures (
        id             INTEGER PRIMARY KEY AUTOINCREMENT,
        year           INTEGER NOT NULL,
        drug_type      TEXT    NOT NULL,
        seizure_type   TEXT    NOT NULL,
        value          REAL 
    )");

    $db->exec("CREATE TABLE emergencies (
        id              INTEGER PRIMARY KEY AUTOINCREMENT,
        year            INTEGER NOT NULL,
        criterion_value TEXT    NOT NULL,
        drug            TEXT    NOT NULL,
        value           REAL
    )");

    $db->exec("CREATE TABLE campaigns_projects (
        id                  INTEGER PRIMARY KEY AUTOINCREMENT,
        year                INTEGER NOT NULL,
        type                TEXT    NOT NULL,
        name                TEXT    NOT NULL,
        beneficiaries_count INTEGER
    )");

    $db->exec("CREATE TABLE prevention_activities (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        year        INTEGER NOT NULL,
        environment TEXT    NOT NULL,
        beneficiary TEXT    NOT NULL,
        value       REAL
    )");

    $db->exec("CREATE TABLE crimes_general (
        id       INTEGER PRIMARY KEY AUTOINCREMENT,
        year     INTEGER NOT NULL,
        category TEXT    NOT NULL,
        value    INTEGER 
    )");

    $db->exec("CREATE TABLE crimes_sex (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        year         INTEGER NOT NULL,
        sex          TEXT    NOT NULL,
        age_category TEXT    NOT NULL,
        value        INTEGER 
    )");

    $db->exec("CREATE TABLE crimes_law (
        id      INTEGER PRIMARY KEY AUTOINCREMENT,
        year    INTEGER NOT NULL,
        article TEXT    NOT NULL,
        value   INTEGER 
    )");

    $db->exec("CREATE TABLE crimes_sentences (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        year          INTEGER NOT NULL,
        sentence_type TEXT    NOT NULL,
        law           TEXT    NOT NULL,
        value         INTEGER 
    )");

    $db->exec("CREATE TABLE criminal_groups (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        year       INTEGER NOT NULL,
        field_name TEXT,
        value      INTEGER
    )");

    $db->exec("CREATE TABLE users (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT    NOT NULL UNIQUE,
    password TEXT    NOT NULL  
    );");
}