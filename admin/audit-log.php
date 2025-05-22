<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../db.php';

function log_audit($user_id, $role_type, $action_type, $action_description, $table_affected = null, $record_id = null) {
    global $conn;

    if (!$user_id || !$role_type) return;

    $stmt = $conn->prepare("
        INSERT INTO audit_logs (user_id, role_type, action_type, action_description, table_affected, record_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssi", $user_id, $role_type, $action_type, $action_description, $table_affected, $record_id);
    $stmt->execute();
    $stmt->close();
}
?>
