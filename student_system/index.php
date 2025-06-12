<?php

require_once 'db_conn.php';

$message = '';
$message_type = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'added') {
        $message = 'Student added successfully!';
        $message_type = 'success';
    } elseif ($_GET['status'] == 'updated') {
        $message = 'Student updated successfully!';
        $message_type = 'success';
    } elseif ($_GET['status'] == 'deleted') {
        $message = 'Student deleted successfully!';
        $message_type = 'success';
    } elseif ($_GET['status'] == 'error') {
        $message = 'An error occurred during the operation.';
        $message_type = 'danger';
    }
}

try {
    // Prepare the SQL statement to select student data
    // Ensure 'full_name', 'email', 'course', and 'created_at' match your database table columns exactly.
    $stmt = $conn->prepare("SELECT id, full_name, email, course, created_at FROM students ORDER BY full_name ASC");

    // Explicitly check if the prepare statement failed.
    // With PDO::ATTR_ERRMODE set to EXCEPTION, prepare() usually throws on error,
    // but this check adds robustness.
    if ($stmt === false) {
        throw new PDOException("SQL statement preparation failed. Check your query syntax or column names.");
    }

    $stmt->execute(); // Execute the prepared statement

    $students = $stmt->fetchAll(); // Fetch all results

} catch (PDOException $e) {
    // If any PDO exception occurs (e.g., connection issues, invalid query, unknown column),
    // this block will catch it and set an informative error message.
    $message = "Database Error: " . $e->getMessage();
    $message_type = 'danger';
    $students = []; // Ensure $students is empty on error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Student System</a>
    </nav>

    <div class="container fade-in">
        <h2>Student List</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div style="text-align: right; margin-bottom: 20px;">
            <a href="add.php" class="btn btn-success">Add New Student</a>
        </div>

        <?php if (count($students) > 0): ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                            <td><?php echo htmlspecialchars($student['created_at']); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No students found. Click "Add New Student" to add one.</div>
        <?php endif; ?>
    </div>

</body>
</html>
