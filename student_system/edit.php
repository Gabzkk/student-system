<?php

require_once 'db_conn.php';

$id = null;
$errors = [];

$student_data = [
    'full_name' => '',
    'email' => '',
    'course' => '',
    'created_at' => ''
];

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT id, full_name, email, course, created_at FROM students WHERE id = ? LIMIT 1");

        if ($stmt === false) {
            throw new PDOException("Failed to prepare SELECT statement. Check your SQL syntax or database connection.");
        }

        $stmt->execute([$id]);
        $fetched_student = $stmt->fetch();

        if (!$fetched_student) {
            header('Location: index.php?status=error&msg=Student not found');
            exit();
        } else {
            $student_data = $fetched_student;
        }

    } catch (PDOException $e) {
        $errors[] = "Error fetching student data: " . $e->getMessage();
    }
} elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?status=error&msg=No student ID provided');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim(htmlspecialchars($_POST['id'] ?? ''));
    $full_name = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $course = trim(htmlspecialchars($_POST['course'] ?? ''));

    if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT)) {
        $errors[] = 'Invalid student ID.';
    }

    if (empty($full_name)) {
        $errors[] = 'Full Name is required.';
    }
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (empty(trim($course))) {
        $errors[] = 'Course is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE students SET full_name = ?, email = ?, course = ? WHERE id = ?");

            if ($stmt === false) {
                throw new PDOException("Failed to prepare UPDATE statement. Check your SQL syntax or database connection.");
            }

            $stmt->execute([$full_name, $email, $course, $id]);

            header('Location: index.php?status=updated');
            exit();

        } catch (PDOException $e) {
            $errors[] = "Error updating student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Student System</a>
    </nav>

    <div class="container fade-in">
        <h2>Edit Student</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($id): ?>
            <form action="edit.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student_data['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student_data['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="course" class="form-label">Course:</label>
                    <input type="text" id="course" name="course" class="form-control" value="<?php echo htmlspecialchars($student_data['course']); ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Student</button>
                    <a href="index.php" class="btn btn-secondary" style="background-color: #6c757d; border-color: #6c757d;">Cancel</a>
                </div>
                <?php if (!empty($student_data['created_at'])): ?>
                <div class="form-group">
                    <label class="form-label">Created At:</label>
                    <p class="form-control-static"><?php echo htmlspecialchars($student_data['created_at']); ?></p>
                </div>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <div class="alert alert-info">Please select a student to edit from the <a href="index.php">student list</a>.</div>
        <?php endif; ?>
    </div>

</body>
</html>
