<?php
include 'config.php';

// Fetch only the latest resume
$sql = "SELECT * FROM resumes ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>View Latest Resume</title>
<link rel="stylesheet" href="style.css" />
<style>
  body { font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; }
  .resume {
    background: white;
    max-width: 700px;
    margin: 40px auto;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  .header {
    display: flex;
    align-items: center;
    gap: 25px;
    margin-bottom: 30px;
  }
  .header img {
    border-radius: 50%;
    width: 140px;
    height: 140px;
    object-fit: cover;
    border: 4px solid #2563eb;
  }
  .header h2 {
    margin: 0;
    font-size: 32px;
    color: #1e40af;
  }
  .contact-info {
    margin-top: 8px;
    font-size: 15px;
    color: #555;
    line-height: 1.4;
  }
  h3 {
    color: #2563eb;
    border-bottom: 3px solid #2563eb;
    padding-bottom: 5px;
    margin-bottom: 15px;
    margin-top: 35px;
  }
  p, ul {
    font-size: 16px;
    color: #333;
    line-height: 1.6;
  }
  ul { padding-left: 22px; }
</style>
</head>
<body>

<h1 style="text-align:center; color:#2563eb;">Latest Resume Submitted</h1>
<p style="text-align:center; margin-top: 20px;">
  <a href="download_resume.php" style="background:#2563eb; color:white; padding:10px 20px; border-radius:5px; text-decoration:none;">Download as PDF</a>
</p>

<?php if ($result && $result->num_rows > 0): ?>
  <?php $row = $result->fetch_assoc(); ?>
    <div class="resume">
      <div class="header">
        <?php if ($row['photo'] && file_exists($row['photo'])): ?>
          <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Profile photo of <?= htmlspecialchars($row['name']) ?>" />
        <?php else: ?>
          <img src="default-profile.png" alt="Default profile photo" />
        <?php endif; ?>
        <div>
          <h2><?= htmlspecialchars($row['name']) ?></h2>
          <div class="contact-info">
            <div>📍 <?= htmlspecialchars($row['address']) ?></div>
            <div>📞 <?= htmlspecialchars($row['mobile']) ?></div>
            <div>✉️ <?= htmlspecialchars($row['email']) ?></div>
          </div>
        </div>
      </div>

      <h3>Profile</h3>
      <p><?= nl2br(htmlspecialchars($row['profile'])) ?></p>

      <h3>Skills</h3>
      <p><?= nl2br(htmlspecialchars($row['skills'])) ?></p>

      <h3>Education</h3>
      <p><?= nl2br(htmlspecialchars($row['education'])) ?></p>

      <h3>Experience</h3>
      <p><?= nl2br(htmlspecialchars($row['experience'])) ?></p>

      <h3>Projects</h3>
      <p><?= nl2br(htmlspecialchars($row['projects'])) ?></p>
    </div>
<?php else: ?>
  <p style="text-align:center;">No resumes found.</p>
<?php endif; ?>

</body>
</html>
