<?php
include("connection.php");

// Collect form data
$name = $_POST['name'];
$mobile = $_POST['mob'];
$pass = $_POST['pass'];
$cpass = $_POST['cpass'];
$add = $_POST['add'];
$image = $_FILES['image']['name'];
$tmp_name = $_FILES['image']['tmp_name'];
$role = $_POST['role'];

// Check password match
if ($cpass != $pass) {
    echo '<script>
            alert("Passwords do not match!");
            window.location = "../routes/register.html";
        </script>';
} else {
    // Move uploaded file
    if (move_uploaded_file($tmp_name, "../uploads/$image")) {
        // Prepare SQL statement
        $stmt = $connect->prepare("INSERT INTO user (name, mobile, password, address, photo, status, votes, role) VALUES (?, ?, ?, ?, ?, 0, 0, ?)");
        $stmt->bind_param("sssssi", $name, $mobile, $pass, $add, $image, $role);

        // Execute the statement
        if ($stmt->execute()) {
            echo '<script>
                    alert("Registration successful!");
                    window.location = "../";
                </script>';
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "File upload failed.";
    }
}

// Close the database connection
$connect->close();
?>
