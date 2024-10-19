<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Initialize car variable
$car = null;

// Handle form submissions for adding/editing cars
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $make = $_POST['make'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $sql = "INSERT INTO cars (make, model, year) VALUES ('$make', '$model', '$year')";
        $conn->query($sql);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $make = $_POST['make'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $sql = "UPDATE cars SET make='$make', model='$model', year='$year' WHERE id=$id";
        $conn->query($sql);
    }
    header('Location: index.php');
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM cars WHERE id=$id");
}

// Fetch all cars
$result = $conn->query("SELECT * FROM cars");

// Check if we need to edit a specific car
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $car_result = $conn->query("SELECT * FROM cars WHERE id=$edit_id");
    
    if ($car_result->num_rows > 0) {
        $car = $car_result->fetch_assoc(); // Fetch car data for editing
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Car Management System</h1>

<a href="logout.php" class="logout-button">Logout</a>

<form method="post">
    <input type="hidden" name="id" value="<?php echo isset($car) ? $car['id'] : ''; ?>">
    <input type="text" name="make" placeholder="Make" required value="<?php echo isset($car) ? $car['make'] : ''; ?>">
    <input type="text" name="model" placeholder="Model" required value="<?php echo isset($car) ? $car['model'] : ''; ?>">
    <input type="number" name="year" placeholder="Year" required value="<?php echo isset($car) ? $car['year'] : ''; ?>">
    <button type="submit" name="<?php echo isset($car) ? 'edit' : 'add'; ?>">
        <?php echo isset($car) ? 'Update Car' : 'Add Car'; ?>
    </button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Make</th>
        <th>Model</th>
        <th>Year</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['make']; ?></td>
            <td><?php echo $row['model']; ?></td>
            <td><?php echo $row['year']; ?></td>
            <td>
                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a> |
                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php
$conn->close();
?>