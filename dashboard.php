<?php

    /*require_once 'session.php';
    session_start();
    if($_SESSION['access']&32) {
        header('location: operations.php');
    } else if($_SESSION['access']&4) {
        header('location: accounting_dashboard.php');
    } else if($_SESSION['access']&16) {
        header('location: operations_dashboard.php');
    } else if($_SESSION['access']&16) {
        header('location: operations_dashboard.php');
    }
    
    else {
        die("You are not welcome here.");
    }

?>
