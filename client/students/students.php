<?php
use SYS\CONTROLLER\StudentController;

$student  = new StudentController
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
</head>
<body>
Students

<form method="post" action="index.php?controller=Student&action=updateStudentField">
    <h1>Update</h1>

    <label for="newValue">
        New First Name
    </label>
    <input type="text" name="newValue">
    <label for="field">
        Field
    </label>
    <input type="text" name="field">
    <label for="user_id">
        User ID
    </label>
    <input type="number" name="user_id">
    <input type="submit" name="submit">
</form>
</body>
</html>