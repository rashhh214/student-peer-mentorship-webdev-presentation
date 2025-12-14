<?php
session_start();
include __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['mentor_id'])) {
  header("Location: mentor_login.php");
  exit();
}

$mentor_id = $_SESSION['mentor_id'];
$mentor_name = $_SESSION['mentor_name'];

$tutorials = $conn->query("SELECT * FROM tutorials WHERE mentor_id = $mentor_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mentor Dashboard</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #0097e6, #00a8ff, #273c75);
    min-height: 100vh;
    margin: 0;
    padding: 20px;
    color: #2f3640;
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    color: #fff;
  }

  .header h2 {
    margin: 0;
  }

  .header a {
    color: white;
    text-decoration: none;
    margin-left: 15px;
    font-weight: 600;
    background: #0097e6;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
  }

  .header a:hover {
    background: #00a8ff;
  }

  .card {
    background: #ffffff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 1000px;
    margin: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  table th, table td {
    border: 1px solid #dcdde1;
    padding: 12px;
    text-align: left;
  }

  table th {
    background: #0097e6;
    color: white;
    font-weight: 600;
  }

  table tr:nth-child(even) {
    background: #f5f6fa;
  }

  .view-btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    color: white;
    background: #44bd32;
    transition: 0.3s;
    display: inline-block;
  }

  .view-btn:hover {
    background: #4cd137;
  }

  .no-tutorials {
    text-align: center;
    color: #718093;
    padding: 40px;
    font-size: 1.1rem;
  }

</style>
</head>
<body>

<div class="header">
  <h2>Welcome, <?php echo htmlspecialchars($mentor_name); ?>!</h2>
  <div>
    <a href="create_tutorial.php">Create New Tutorial</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="card">
  <h3>Your Tutorials</h3>
  <?php if ($tutorials->num_rows > 0): ?>
    <table>
      <tr>
        <th>Title</th>
        <th>Topic</th>
        <th>Schedule</th>
        <th>View Students</th>
      </tr>
      <?php while($row = $tutorials->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['title']); ?></td>
          <td><?php echo htmlspecialchars($row['topic']); ?></td>
          <td><?php echo htmlspecialchars($row['schedule']); ?></td>
          <td><a class="view-btn" href="view_students.php?tutorial_id=<?php echo $row['id']; ?>">View</a></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <div class="no-tutorials">
      You haven't created any tutorials yet. Click "Create New Tutorial" to get started!
    </div>
  <?php endif; ?>
</div>

</body>
</html>