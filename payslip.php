<!DOCTYPE html>
<html>
<head>
    <title>Payroll Form</title>
</head>
<body>

<h2>Enter Employee Payroll Details</h2>
<form action="payslip.php" method="post">
    <label>Employee ID:</label>
    <input type="text" name="employee_id" required><br>

    <label>Basic Salary:</label>
    <input type="number" name="basic_salary" required><br>

    <label>Allowances:</label>
    <input type="number" name="allowances" required><br>

    <label>Deductions:</label>
    <input type="number" name="deductions" required><br>

    <input type="submit" value="Submit">
</form>

<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "workpausedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST["employee_id"];
    $basic_salary = $_POST["basic_salary"];
    $allowances = $_POST["allowances"];
    $deductions = $_POST["deductions"];

    // Insert into database
    $sql = "INSERT INTO payroll (employee_id, basic_salary, allowances, deductions) VALUES ('$employee_id', '$basic_salary', '$allowances', '$deductions')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Record added successfully!</p>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
}

// Fetch and display payroll records
$result = $conn->query("SELECT * FROM payroll");

echo "<h2>Payroll Details</h2>";
echo "<table border='1'><tr><th>Employee ID</th><th>Basic Salary</th><th>Allowances</th><th>Deductions</th><th>Net Salary</th></tr>";

while ($row = $result->fetch_assoc()) {
    $leave_result = $conn->query("SELECT COUNT(*) as leave_days FROM leaves WHERE employee_id='{$row["employee_id"]}' AND status='Approved'");
    $leave_data = $leave_result->fetch_assoc();
    $leave_deduction = ($row["basic_salary"] / 30) * ($leave_data["leave_days"] ?? 0);

    $total_deductions = $row["deductions"] + $leave_deduction;
    $net_salary = ($row["basic_salary"] + $row["allowances"]) - $total_deductions;

    echo "<tr>";
    echo "<td>".$row["employee_id"]."</td>";
    echo "<td>".$row["basic_salary"]." INR</td>";
    echo "<td>".$row["allowances"]." INR</td>";
    echo "<td>".$total_deductions." INR</td>";
    echo "<td>".$net_salary." INR</td>";
    echo "</tr>";
}

echo "</table>";

$conn->close();
?>

</body>
</html>