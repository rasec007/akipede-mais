<?php
// api/auth/check_session.php
session_start();

function checkAuth() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
}

function getLoggedUser() {
    return $_SESSION['user'] ?? null;
}
