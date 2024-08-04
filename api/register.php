<?php
include("connection.php");

$name = $_POST['name'];
$mobile = $_POST['mob'];
$pass = $_POST['pass'];
$cpass = $_POST['cpass'];
$address = isset($_POST['address']) ? $_POST['address'] : '';
$image = $_FILES['image']['name'];
$tmp_name = $_FILES['image']['tmp_name'];
$role_id = $_POST['role'];

if ($cpass != $pass) {
    echo '<script>
            alert("Passwords do not match!");
            window.location = "../routes/register.html";
        </script>';
} else {
    if (move_uploaded_file($tmp_name, "../uploads/$image")) {
        // Check if the role_id exists in the role table
        $role_check_stmt = $connect->prepare("SELECT id FROM role WHERE id = ?");
        $role_check_stmt->bind_param("i", $role_id);
        $role_check_stmt->execute();
        $role_check_stmt->store_result();

        if ($role_check_stmt->num_rows > 0) {
            $stmt = $connect->prepare("INSERT INTO address (address) VALUES (?)");
            $stmt->bind_param("s", $address);

            if ($stmt->execute()) {
                $address_id = $stmt->insert_id;

                $stmt = $connect->prepare("INSERT INTO user (name, mobile, password, address_id, photo, status, votes, role_id) VALUES (?, ?, ?, ?, ?, 0, 0, ?)");
                $stmt->bind_param("sssssi", $name, $mobile, $pass, $address_id, $image, $role_id);

                if ($stmt->execute()) {
                    echo '<script>
                            alert("Registration successful!");
                            window.location = "../";
                        </script>';
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Error inserting address: " . $stmt->error;
            }
        } else {
            echo '<script>
                    alert("Invalid role selected!");
                    window.location = "../routes/register.html";
                </script>';
        }

        $role_check_stmt->close();
    } else {
        echo "File upload failed.";
    }
}

$connect->close();
?>
