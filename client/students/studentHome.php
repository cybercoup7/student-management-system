<?php
// Authorization check
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home</title>
</head>
<body>
    <h1>Welcome to the Student Home Page</h1>
    <p>Hello, <?php echo $_SESSION['username']; ?>! Here you can access your student dashboard.</p>
    <ul>
        <li><a href="/index.php?script=viewProfile">View Profile</a></li>
        <li><a href="/index.php?script=viewCourses">View Courses</a></li>
    </ul>
</body>
</html>
