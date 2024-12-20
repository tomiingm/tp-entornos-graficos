<?php
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function login($email, $password) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}