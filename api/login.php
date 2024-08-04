<?php
session_start();
include("connection.php");

$mobile = $_POST['mob'];
$password = $_POST['pass'];
$role_id = $_POST['role'];

$query = "SELECT * FROM user WHERE mobile = ? AND password = ? AND role_id = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("ssi", $mobile, $password, $role_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    $_SESSION['id'] = $user['id'];
    $_SESSION['status'] = $user['status'];
    $_SESSION['data'] = $user;

    $getGroups = $connect->prepare("SELECT name, photo, votes, id FROM user WHERE role_id = 2");
    $getGroups->execute();
    $groups_result = $getGroups->get_result();

    if ($groups_result->num_rows > 0) {
        $groups = $groups_result->fetch_all(MYSQLI_ASSOC);
        $_SESSION['groups'] = $groups;
    }

    $getGroups->close();

    echo '<script>
            alert("Login successful!");
            window.location = "../routes/dashboard.php";
        </script>';
} else {
    echo '<script>
            alert("Invalid mobile number, password, or role!");
            window.location = "../routes/login.html";
        </script>';
}

$stmt->close();
$connect->close();
?>
