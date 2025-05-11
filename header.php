<?php
session_start();
require_once("../admin/inc/config.php");
if ($_SESSION['key'] != "Voterskey") {
    echo "<script> location.assign('../admin/inc/logout.php'); </script>";
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voters-panel Online Voting System</title>

    <!-- ✅ Bootstrap CSS (CDN for guaranteed working) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- ✅ Your custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <!-- ✅ Header Section -->
    <div class="container-fluid">
        <div class="row bg-dark text-white align-items-center py-3">
            
            <div class="col-md-11">
                <h2 class="m-0">
                    Online Voting System - 
                    <small >Welcome <?php echo $_SESSION['username']; ?>!</small>
                </h2>
            </div>
        </div>

    

   

