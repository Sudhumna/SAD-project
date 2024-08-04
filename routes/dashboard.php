<?php
session_start();

// Adjust the path to connection.php based on your directory structure
include("../api/connection.php");

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo '<script>
            alert("You need to be logged in to view this page!");
            window.location = "../routes/login.html";
        </script>';
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user data including address and status
$query = "SELECT user.*, address.address 
          FROM user 
          LEFT JOIN address ON user.address_id = address.id 
          WHERE user.id = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $status = ($data['status'] == 1) ? '<b style="color: green">Voted</b>' : '<b style="color: red">Not Voted</b>';
} else {
    echo '<script>
            alert("User data not found!");
            window.location = "../routes/login.html";
        </script>';
    exit();
}

// Fetch groups if user is logged in
$query = "SELECT name, photo, votes, id FROM user WHERE role_id = 2";
$stmt = $connect->prepare($query);
$stmt->execute();
$groups_result = $stmt->get_result();

$groups = ($groups_result->num_rows > 0) ? $groups_result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Online Voting System - Dashboard</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
<style> 
footer {
    background-color: rgb(198, 245, 245);
    position: fixed;
    width: 100%;
    text-align: center;
    left: 0px;
    bottom: 0px;
}
</style>

<center>
    <div id="headerSection">
        <a href="../"><button id="back-button">Back</button></a>
        <a href="logout.php"><button id="logout-button">Logout</button></a>
        <h1>Online Voting System</h1>  
    </div>
</center>
<hr>

<div id="mainSection">
    <div id="profileSection">
        <center><img src="../uploads/<?php echo htmlspecialchars($data['photo']) ?>" height="100" width="100"></center><br>
        <b>Name : </b><?php echo htmlspecialchars($data['name']) ?><br><br>
        <b>Mobile : </b><?php echo htmlspecialchars($data['mobile']) ?><br><br>
        <b>Address : </b><?php echo htmlspecialchars($data['address'] ?? 'No address provided') ?><br><br>
        <b>Status : </b><?php echo $status ?>
    </div>
    <div id="groupSection">
        <?php
        if (count($groups) > 0) {
            foreach ($groups as $group) {
                ?>
                <div style="border-bottom: 1px solid #bdc3c7; margin-bottom: 10px">
                    <img style="float: right" src="../uploads/<?php echo htmlspecialchars($group['photo']) ?>" height="80" width="80">
                    <b>Group Name : </b><?php echo htmlspecialchars($group['name']) ?><br><br>
                    <b>Votes :</b> <?php echo htmlspecialchars($group['votes']) ?><br><br>
                    <form method="POST" action="../api/vote.php">
                        <input type="hidden" name="gvotes" value="<?php echo htmlspecialchars($group['votes']) ?>">
                        <input type="hidden" name="gid" value="<?php echo htmlspecialchars($group['id']) ?>">
                        <?php
                        if ($data['status'] == 1) {
                            ?>
                            <button disabled style="padding: 5px; font-size: 15px; background-color: #27ae60; color: white; border-radius: 5px;" type="button">Voted</button>
                            <?php
                        } else {
                            ?>
                            <button style="padding: 5px; font-size: 15px; background-color: #3498db; color: white; border-radius: 5px;" type="submit">Vote</button>
                            <?php
                        }
                        ?>
                    </form>
                </div>
                <?php
            }
        } else {
            ?>
            <div style="border-bottom: 1px solid #bdc3c7; margin-bottom: 10px">
                <b>No groups available right now.</b>    
            </div>
            <?php
        }
        ?>
    </div>
</div>
<footer>
    <p>Developed by Sudhumna Phuyal</p>
</footer>
</body>
</html>
