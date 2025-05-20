<?php
// includes/audit-logger.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../db.php'; // Make sure this path is correct relative to wherever you're including this

function log_audit($user_id, $action_type, $action_description, $table_affected = null, $record_id = null) {
    global $conn;

    if (!$user_id) {
        return; // ignore unauthenticated logs
    }

    $stmt = $conn->prepare("
        INSERT INTO audit_logs (user_id, action_type, action_description, table_affected, record_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssi", $user_id, $action_type, $action_description, $table_affected, $record_id);
    $stmt->execute();
    $stmt->close();
}
?>
