<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require_once '../includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mr. Compilex</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #wrapper {
            display: flex;
            flex: 1;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #343a40;
            color: white;
            transition: all 0.3s;
        }
        #sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: white;
            background-color: #495057;
        }
        #content-wrapper {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .card-counter {
            box-shadow: 2px 2px 10px #DADADA;
            margin: 5px;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
        }
        .card-counter:hover {
            box-shadow: 4px 4px 20px #DADADA;
            transform: scale(1.02);
        }
        .card-counter i {
            font-size: 5em;
            opacity: 0.2;
        }
        .card-counter .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }
        .card-counter .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.5;
            display: block;
            font-size: 18px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Mr. Compilex Admin</a>
        <div class="d-flex">
            <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div id="wrapper">
    <nav id="sidebar">
        <div class="list-group list-group-flush">
            <a href="index.php" class="nav-link list-group-item bg-transparent border-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="categories.php" class="nav-link list-group-item bg-transparent border-0"><i class="fas fa-tags me-2"></i>Categories</a>
            <a href="products.php" class="nav-link list-group-item bg-transparent border-0"><i class="fas fa-box me-2"></i>Products</a>
            <a href="orders.php" class="nav-link list-group-item bg-transparent border-0"><i class="fas fa-shopping-bag me-2"></i>Orders</a>
            <a href="users.php" class="nav-link list-group-item bg-transparent border-0"><i class="fas fa-users me-2"></i>Users</a>
        </div>
    </nav>
    <div id="content-wrapper">
