<?php
// api/auth/get_user.php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user'])) {
    echo json_encode(['logged' => true, 'user' => $_SESSION['user']]);
} else {
    echo json_encode(['logged' => false]);
}
