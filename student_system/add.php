<?php

require_once 'db_conn.php';

$full_name = '';
$email = '';
$course = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $course = trim(htmlspecialchars($_POST['course'] ?? ''));

    if (empty($full_name)) {
        $errors[] = 'Full Name is required.';
    }
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email format
        $errors[] = 'Invalid email format.';
    }
    if (empty($course)) {
        $errors[] = 'Course is required.';
    }

    if (empty($errors)) {
        try {
            // Updated INSERT query to use full_name, email, course
            // created_at will use CURRENT_TIMESTAMP in SQL, so no PHP parameter needed for it.
            $stmt = $conn->prepare("INSERT INTO students (full_name, email, course) VALUES (?, ?, ?)");

            if ($stmt === false) {
                throw new PDOException("Failed to prepare SQL statement. Check your SQL syntax or database connection.");
            }

            // Pass parameters in the correct order for full_name, email, course
            $stmt->execute([$full_name, $email, $course]);

            header('Location: index.php?status=added');
            exit();

        } catch (PDOException $e) {
            $errors[] = "Error adding student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Student System</a>
    </nav>

    <div class="container fade-in">
        <h2>Add New Student</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="add.php" method="POST">
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name:</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="course" class="form-label">Course:</label>
                <input type="text" id="course" name="course" class="form-control" value="<?php echo htmlspecialchars($course); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Add Student</button>
                <a href="index.php" class="btn btn-secondary" style="background-color: #6c757d; border-color: #6c757d;">Cancel</a>
            </div>
        </form>
    </div>

</body>
</html>
