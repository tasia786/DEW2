<?php

if (!isset($pageTitle))  $pageTitle  = '';
if (!isset($activePage)) $activePage = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php echo $pageTitle; ?> – Drug Explorer</title>

  
  <link rel="stylesheet" href="/css/base/variables.css">
  <link rel="stylesheet" href="/css/base/reset.css">

  <link rel="stylesheet" href="/css/layout/grid.css">
  <link rel="stylesheet" href="/css/components/components.css">
</head>
<body>

  <header class="app-header">
    <?php include __DIR__ . '/nav.php'; ?>
  </header>



