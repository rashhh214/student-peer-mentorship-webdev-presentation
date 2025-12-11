<?php
include '../db_connect.php';
include '../email_config.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Insert mentor record
  $sql = "INSERT INTO mentors (name, email, password) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $name, $email, $password);

  if ($stmt->execute()) {

    // Generate verification token
    $token = bin2hex(random_bytes(32));
    $conn->query("UPDATE mentors SET verify_token='$token' WHERE email='$email'");

    // Verification link
    $verify_link = "http://localhost/studentpeermentorship/mentor/verify.php?token=$token";

    /* ============================
       SEND EMAIL TO MENTOR
    ============================= */
    $email_body = "
    <div style='font-family:Arial;padding:20px;background:#f0f0f0;'>
      <div style='background:#fff;padding:20px;border-radius:8px;'>
        <h2 style='color:#2c7be5;'>Welcome, $name!</h2>
        <p>Thank you for registering as a <b>Mentor</b> in the Student Peer Mentorship System.</p>
        <p>Please verify your email to activate your account:</p>
        
        <a href='$verify_link'
           style='display:inline-block;background:#2c7be5;color:white;padding:12px 18px;
                  text-decoration:none;border-radius:6px;margin-top:10px;'>
          Verify My Account
        </a>

        <p style='margin-top:25px;font-size:12px;color:#555;'>
          If you did not create this account, please ignore this message.
        </p>
      </div>
    </div>
    ";

    sendMail($email, "Verify Your Mentor Account", $email_body);


    /* ============================
       SEND NOTIFICATION EMAIL TO ADMIN
    ============================= */

    $admin_email = "admin@gmail.com"; // <-- CHANGE THIS

    $admin_body = "
    <h3>New Mentor Registration</h3>
    <p>A new mentor has registered:</p>
    <ul>
      <li><b>Name:</b> $name</li>
      <li><b>Email:</b> $email</li>
    </ul>
    <p>Login to admin dashboard to review.</p>
    ";

    sendMail($admin_email, "New Mentor Registration Alert", $admin_body);


    /* ============================
       SHOW SUCCESS MESSAGE
    ============================= */

    $message = "Account created! Please check your email to verify your mentor account.";

  } else {
    $message = "Error: " . $conn->error;
  }
}
?>
