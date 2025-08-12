<?php
include 'config.php';

$message = '';
$message_type = ''; // 'success' or 'error'

// Show success message only if redirected after successful insert
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Resume added successfully!";
    $message_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $profile = trim($_POST['profile']);
    $skills = trim($_POST['skills']);
    $education = trim($_POST['education']);
    $experience = trim($_POST['experience']);
    $projects = trim($_POST['projects']);

    // Basic validation
    if (empty($name) || empty($email)) {
        $message = "Name and Email are required.";
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = 'error';
    } elseif (!preg_match('/^[0-9+\-\s]{7,15}$/', $mobile)) {
        $message = "Invalid mobile number.";
        $message_type = 'error';
    } else {
        // Escape strings for DB
        $name = $conn->real_escape_string($name);
        $address = $conn->real_escape_string($address);
        $mobile = $conn->real_escape_string($mobile);
        $email = $conn->real_escape_string($email);
        $profile = $conn->real_escape_string($profile);
        $skills = $conn->real_escape_string($skills);
        $education = $conn->real_escape_string($education);
        $experience = $conn->real_escape_string($experience);
        $projects = $conn->real_escape_string($projects);

        // Handle photo upload
        $photo = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = 'uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $dest_path = $uploadFileDir . $newFileName;
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $photo = $conn->real_escape_string($dest_path);
                } else {
                    $message = "Error uploading photo.";
                    $message_type = 'error';
                }
            } else {
                $message = "Upload failed. Allowed types: " . implode(', ', $allowedfileExtensions);
                $message_type = 'error';
            }
        }

        // Insert if no error message
        if (!$message) {
            $sql = "INSERT INTO resumes 
                    (name, address, mobile, email, photo, profile, skills, education, experience, projects) 
                    VALUES 
                    ('$name', '$address', '$mobile', '$email', " . ($photo ? "'$photo'" : "NULL") . ", '$profile', '$skills', '$education', '$experience', '$projects')";
            if ($conn->query($sql)) {
                // Redirect to same page with success query param
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                exit();
            } else {
                $message = "Database error: " . $conn->error;
                $message_type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Your Resume</title>
<style>
  /* Reset & base */
  * {
    box-sizing: border-box;
  }
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f4f8;
    margin: 0;
    padding: 30px 15px;
    color: #333;
  }
  .container {
    max-width: 700px;
    background: #fff;
    margin: 0 auto;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgb(0 0 0 / 0.1);
  }
  h1 {
    text-align: center;
    color: #1e40af;
    margin-bottom: 30px;
    font-weight: 700;
  }
  label {
    display: block;
    font-weight: 600;
    margin-top: 18px;
    margin-bottom: 6px;
    color: #1e3a8a;
  }
  input[type="text"],
  input[type="email"],
  textarea,
  input[type="file"] {
    width: 100%;
    padding: 12px 15px;
    font-size: 15px;
    border: 1.8px solid #cbd5e1;
    border-radius: 6px;
    transition: border-color 0.3s ease;
    font-family: inherit;
    resize: vertical;
  }
  input[type="text"]:focus,
  input[type="email"]:focus,
  textarea:focus,
  input[type="file"]:focus {
    border-color: #2563eb;
    outline: none;
  }
  textarea {
    min-height: 80px;
  }
  button {
    margin-top: 30px;
    width: 100%;
    background-color: #2563eb;
    color: white;
    border: none;
    padding: 15px;
    font-size: 17px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.25s ease;
  }
  button:hover {
    background-color: #1e40af;
  }
</style>

<script>
  function validateForm(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const mobile = document.getElementById('mobile').value.trim();

    if (!name) {
      alert('Name is required');
      e.preventDefault();
      return false;
    }
    if (!email) {
      alert('Email is required');
      e.preventDefault();
      return false;
    }
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
      alert('Invalid email format');
      e.preventDefault();
      return false;
    }
    const mobilePattern = /^[0-9+\-\s]{7,15}$/;
    if (!mobilePattern.test(mobile)) {
      alert('Invalid mobile number');
      e.preventDefault();
      return false;
    }
    return true;
  }
</script>
</head>
<body>
  <div class="container">
    <h1>Add Your Resume</h1>

    <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm(event)" novalidate>
      <label for="photo">Profile Photo (jpg, png, gif):</label>
      <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.gif">

      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" placeholder="Your full name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

      <label for="address">Address</label>
      <input type="text" id="address" name="address" placeholder="Your address" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">

      <label for="mobile">Mobile Number</label>
      <input type="text" id="mobile" name="mobile" placeholder="Your mobile number" required value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">

      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" placeholder="example@mail.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

      <label for="profile">Profile</label>
      <textarea id="profile" name="profile" rows="3" placeholder="Write a brief profile summary" required><?= htmlspecialchars($_POST['profile'] ?? '') ?></textarea>

      <label for="skills">Skills</label>
      <textarea id="skills" name="skills" rows="4" placeholder="e.g. HTML, CSS, JavaScript, PHP" required><?= htmlspecialchars($_POST['skills'] ?? '') ?></textarea>

      <label for="education">Education</label>
      <textarea id="education" name="education" rows="4" placeholder="Your degrees, schools, years" required><?= htmlspecialchars($_POST['education'] ?? '') ?></textarea>

      <label for="experience">Experience</label>
      <textarea id="experience" name="experience" rows="4" placeholder="Internships, projects, jobs" required><?= htmlspecialchars($_POST['experience'] ?? '') ?></textarea>

      <label for="projects">Projects</label>
      <textarea id="projects" name="projects" rows="4" placeholder="Highlight your projects" required><?= htmlspecialchars($_POST['projects'] ?? '') ?></textarea>

      <button type="submit">Save Resume</button>
    </form>
  </div>

<?php if ($message): ?>
<script>
  <?php if ($message_type === 'success'): ?>
    if (confirm(<?= json_encode($message . "\n\nClick OK to view resumes.") ?>)) {
      window.location.href = 'view_resume.php';
    }
  <?php else: ?>
    alert(<?= json_encode($message) ?>);
  <?php endif; ?>
</script>
<?php endif; ?>

</body>
</html>
