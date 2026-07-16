<?php
// Include this at the very top of any vendor-only page or endpoint.
// For pages, it redirects to the login screen.
// For JSON API endpoints, pass is_api=true so it returns a 401 JSON error instead.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_vendor_login($is_api = false) {
    if (!isset($_SESSION['vendor_id'])) {
        if ($is_api) {
            header("Content-Type: application/json");
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
        } else {
            header("Location: vendor_login.php");
        }
        exit;
    }
}
