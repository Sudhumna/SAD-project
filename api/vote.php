<?php
session_start();
include("../api/connection.php"); // Ensure the path to connection.php is correct

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo '<script>
            alert("You need to be logged in to vote!");
            window.location = "../routes/login.html";
        </script>';
    exit();
}

$user_id = $_SESSION['id'];
$group_id = $_POST['gid'];

// Check if the user has already voted
$query = "SELECT * FROM votes WHERE user_id = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User has already voted
    echo '<script>
            alert("You have already voted!");
            window.location = "../routes/dashboard.php";
        </script>';
    exit();
}

// Increment the group's vote count
$query = "UPDATE user SET votes = votes + 1 WHERE id = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $group_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Record the vote in the votes table
    $query = "INSERT INTO votes (user_id, group_id) VALUES (?, ?)";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("ii", $user_id, $group_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Update user's voting status
        $query = "UPDATE user SET status = 1 WHERE id = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        echo '<script>
                alert("Vote recorded successfully!");
                window.location = "../routes/dashboard.php";
            </script>';
    } else {
        echo '<script>
                alert("Failed to record vote!");
                window.location = "../routes/dashboard.php";
            </script>';
    }
} else {
    echo '<script>
            alert("Failed to update group votes!");
            window.location = "../routes/dashboard.php";
        </script>';
}

$stmt->close();
$connect->close();
?>
