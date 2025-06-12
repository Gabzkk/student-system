<?php

require_once 'db_conn.php';

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    try {
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE id = ? LIMIT 1");
        if ($check_stmt === false) {
            throw new PDOException("Failed to prepare check statement.");
        }
        $check_stmt->execute([$id]);
        $student_exists_before_delete = $check_stmt->fetch();

        if (!$student_exists_before_delete) {
            header('Location: index.php?status=error&msg=' . urlencode('Student not found.'));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");

        if ($stmt === false) {
            throw new PDOException("Failed to prepare DELETE statement. Check your SQL syntax or database connection.");
        }

        $delete_success = $stmt->execute([$id]);

        if ($delete_success) {
            $verify_stmt = $conn->prepare("SELECT id FROM students WHERE id = ? LIMIT 1");
            if ($verify_stmt === false) {
                throw new PDOException("Failed to prepare verification statement.");
            }
            $verify_stmt->execute([$id]);
            $student_exists_after_delete = $verify_stmt->fetch();

            if (!$student_exists_after_delete) {
                header('Location: index.php?status=deleted');
                exit();
            } else {
                header('Location: index.php?status=error&msg=' . urlencode('Failed to delete student.'));
                exit();
            }
        } else {
            header('Location: index.php?status=error&msg=' . urlencode('Deletion operation failed to execute.'));
            exit();
        }

    } catch (PDOException $e) {
        header('Location: index.php?status=error&msg=' . urlencode('Database error: ' . $e->getMessage()));
        exit();
    }
} else {
    header('Location: index.php?status=error&msg=' . urlencode('Invalid or no student ID provided for deletion.'));
    exit();
}
?>
