<?php
function redirect_to($location) {
    header("Location: " . $location);
    exit;
}

function check_login() {
    if(!isset($_SESSION['farmerid']) && !isset($_SESSION['customerid']) && !isset($_SESSION['adminid'])) {
        redirect_to("../index.php");
    }
}

function get_user_type() {
    if(isset($_SESSION['farmerid'])) {
        return 'farmer';
    } elseif(isset($_SESSION['customerid'])) {
        return 'customer';
    } elseif(isset($_SESSION['adminid'])) {
        return 'admin';
    }
    return '';
}

function get_user_id() {
    if(isset($_SESSION['farmerid'])) {
        return $_SESSION['farmerid'];
    } elseif(isset($_SESSION['customerid'])) {
        return $_SESSION['customerid'];
    } elseif(isset($_SESSION['adminid'])) {
        return $_SESSION['adminid'];
    }
    return '';
}

function format_timestamp($timestamp) {
    return date("M j, Y", strtotime($timestamp));
}

function format_number($number) {
    return number_format($number);
}

function sanitize_input($data) {
    global $connection;
    return mysqli_real_escape_string($connection, trim($data));
}

function generate_csrf_token() {
    if(!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function check_csrf_token($token) {
    if(!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

function check_file_type($file, $allowed_types) {
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    return in_array($file_extension, $allowed_types);
}

function generate_unique_filename($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return time() . '_' . uniqid() . '.' . $extension;
}

function create_directory_if_not_exists($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}
?>
