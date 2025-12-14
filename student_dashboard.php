<?php
session_start();
include '../db_connect.php';
include '../email_config.php';

if (!isset($_SESSION['student_id'])) {
  header("Location: student_login.php");
  exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

// Get student email
$student_result = $conn->query("SELECT email FROM students WHERE id=$student_id");
$student_data = $student_result->fetch_assoc();
$student_email = $student_data['email'];

$message = '';

if (isset($_GET['join'])) {
  $tutorial_id = intval($_GET['join']);
  
  // Check if already signed up
  $check = $conn->query("SELECT * FROM tutorial_signups WHERE student_id=$student_id AND tutorial_id=$tutorial_id");
  
  if ($check->num_rows == 0) {
    // Insert signup
    $conn->query("INSERT INTO tutorial_signups (tutorial_id, student_id, status) VALUES ($tutorial_id, $student_id, 'pending')");
    
    // Get tutorial and mentor details
    $tutorial_info = $conn->query("
      SELECT t.*, m.name AS mentor_name, m.email AS mentor_email 
      FROM tutorials t 
      JOIN mentors m ON t.mentor_id = m.id 
      WHERE t.id = $tutorial_id
    ")->fetch_assoc();
    
    // Send email to student
    $student_email_body = "
    <div style='font-family:Arial;padding:20px;background:#f0f0f0;'>
      <div style='background:#fff;padding:20px;border-radius:8px;'>
        <h2 style='color:#0097e6;'>Tutorial Registration Confirmed! 📚</h2>
        <p>Hello <b>$student_name</b>,</p>
        <p>You have successfully registered for the following tutorial:</p>
        <div style='background:#f5f6fa;padding:15px;border-radius:6px;margin:15px 0;'>
          <p><b>Tutorial:</b> {$tutorial_info['title']}</p>
          <p><b>Topic:</b> {$tutorial_info['topic']}</p>
          <p><b>Schedule:</b> {$tutorial_info['schedule']}</p>
          <p><b>Duration:</b> {$tutorial_info['duration']}</p>
          <p><b>Mentor:</b> {$tutorial_info['mentor_name']}</p>
        </div>
        <p><b>Status:</b> Pending approval from mentor</p>
        <p>You will receive another email once the mentor approves your registration.</p>
        <p style='margin-top:25px;font-size:12px;color:#555;'>
          Student Peer Mentorship System
        </p>
      </div>
    </div>
    ";
    
    sendMail($student_email, "Tutorial Registration Received", $student_email_body);
    
    // Send email to mentor
    $mentor_email_body = "
    <div style='font-family:Arial;padding:20px;background:#f0f0f0;'>
      <div style='background:#fff;padding:20px;border-radius:8px;'>
        <h2 style='color:#0097e6;'>New Student Registration 🎓</h2>
        <p>Hello <b>{$tutorial_info['mentor_name']}</b>,</p>
        <p>A new student has registered for your tutorial:</p>
        <div style='background:#f5f6fa;padding:15px;border-radius:6px;margin:15px 0;'>
          <p><b>Student Name:</b> $student_name</p>
          <p><b>Student Email:</b> $student_email</p>
          <p><b>Tutorial:</b> {$tutorial_info['title']}</p>
          <p><b>Topic:</b> {$tutorial_info['topic']}</p>
        </div>
        <p>Please log in to your dashboard to approve or reject this registration.</p>
        <a href='http://localhost/studentpeermentorship/mentor/view_students.php?tutorial_id=$tutorial_id'
           style='display:inline-block;background:#0097e6;color:white;padding:12px 18px;
                  text-decoration:none;border-radius:6px;margin-top:10px;'>
          View Registration
        </a>
        <p style='margin-top:25px;font-size:12px;color:#555;'>
          Student Peer Mentorship System
        </p>
      </div>
    </div>
    ";
    
    sendMail($tutorial_info['mentor_email'], "New Student Registration", $mentor_email_body);
    
    $message = "✅ Successfully registered! Check your email for confirmation.";
  } else {
    $message = "⚠️ You are already registered for this tutorial.";
  }
  
} elseif (isset($_GET['withdraw'])) {
  $tutorial_id = intval($_GET['withdraw']);
  $conn->query("DELETE FROM tutorial_signups WHERE student_id=$student_id AND tutorial_id=$tutorial_id");
  $message = "❌ Successfully withdrawn from the tutorial.";
}

$tutorials = $conn->query("SELECT t.*, m.name AS mentor_name FROM tutorials t JOIN mentors m ON t.mentor_id = m.id WHERE t.is_public=1");
$my_signups = $conn->query("SELECT tutorial_id FROM tutorial_signups WHERE student_id=$student_id");
$signed_ids = [];
while($row = $my_signups->fetch_assoc()) {
  $signed_ids[] = $row['tutorial_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
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

  .btn-logout {
    background: #e84118;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
  }

  .btn-logout:hover {
    background: #c23616;
  }

  .card {
    background: #ffffff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 1200px;
    margin: auto;
  }

  .message {
    background: #dfe4ea;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
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

  .action-btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    color: white;
    transition: 0.3s;
    display: inline-block;
  }

  .join-btn {
    background: #44bd32;
  }

  .join-btn:hover {
    background: #4cd137;
  }

  .withdraw-btn {
    background: #e84118;
  }

  .withdraw-btn:hover {
    background: #c23616;
  }
</style>
</head>
<body>

<div class="header">
  <h2>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h2>
  <a class="btn-logout" href="logout.php">Logout</a>
</div>

<div class="card">
  <?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
  <?php endif; ?>

  <h3>Available Tutorials</h3>
  <table>
    <tr>
      <th>Title</th>
      <th>Topic</th>
      <th>Mentor</th>
      <th>Schedule</th>
      <th>Duration</th>
      <th>Action</th>
    </tr>
    <?php while($row = $tutorials->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['title']); ?></td>
      <td><?php echo htmlspecialchars($row['topic']); ?></td>
      <td><?php echo htmlspecialchars($row['mentor_name']); ?></td>
      <td><?php echo htmlspecialchars($row['schedule']); ?></td>
      <td><?php echo htmlspecialchars($row['duration']); ?></td>
      <td>
        <?php if (in_array($row['id'], $signed_ids)): ?>
          <a class="action-btn withdraw-btn" href="?withdraw=<?php echo $row['id']; ?>" 
             onclick="return confirm('Are you sure you want to withdraw?')">Withdraw</a>
        <?php else: ?>
          <a class="action-btn join-btn" href="?join=<?php echo $row['id']; ?>">Join</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>