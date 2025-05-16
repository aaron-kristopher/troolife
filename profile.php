<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "database.php";

if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

$errorMessage = '';
$successMessage = '';

$currentUser = $_SESSION["current_user"];

$editMode = false;
if (isset($_GET['mode']) && $_GET['mode'] === 'edit') {
    $editMode = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $firstname = trim($_POST["first-name"]);
    $lastname = trim($_POST["last-name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $birthday = $_POST["birthday"];
    
    $hasErrors = false;
    
    if (empty($firstname) || !preg_match("/^[a-zA-Z-' ]*$/", $firstname)) {
        $errorMessage = "First name is required and can only contain letters";
        $hasErrors = true;
    }
    
    if (empty($lastname) || !preg_match("/^[a-zA-Z-' ]*$/", $lastname)) {
        $errorMessage = "Last name is required and can only contain letters";
        $hasErrors = true;
    }
    
    if (empty($username)) {
        $errorMessage = "Username is required";
        $hasErrors = true;
    } else {
        $sql = "SELECT userID FROM User WHERE username = ? AND userID != ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $username, $_SESSION["userID"]);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $errorMessage = "Username already taken";
                $hasErrors = true;
            }
            $stmt->close();
        }
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Valid email is required";
        $hasErrors = true;
    }
    
    if (empty($birthday) || !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $birthday)) {
        $errorMessage = "Valid date is required";
        $hasErrors = true;
    }
    
    $profilePhotoPath = $currentUser["profile-picture"];
    
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        
        $targetFile = $uploadDir . basename($_FILES["profile-picture"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $fileSize = $_FILES["profile-picture"]["size"];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        $check = getimagesize($_FILES["profile-picture"]["tmp_name"]);
        if ($check === false) {
            $errorMessage = "File is not an image.";
            $hasErrors = true;
        }
        elseif ($fileSize > $maxFileSize) {
            $errorMessage = "Sorry, your file is too large (Max 2MB).";
            $hasErrors = true;
        }
        elseif (!in_array($imageFileType, $allowedTypes)) {
            $errorMessage = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $hasErrors = true;
        }
        else {
            $uniqueName = uniqid('profile_', true) . '.' . $imageFileType;
            $newTargetFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $newTargetFile)) {
                if (!empty($profilePhotoPath) && file_exists($profilePhotoPath) && strpos($profilePhotoPath, 'images/icons/account_circle.svg') === false) {
                    @unlink($profilePhotoPath);
                }
                $profilePhotoPath = $newTargetFile;
            } else {
                $errorMessage = "Sorry, there was an error uploading your file.";
                $hasErrors = true;
            }
        }
    }
    
    $newPassword = $_POST["new-password"];
    $confirmPassword = $_POST["confirm-password"];
    $passwordChanged = false;
    
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 8) {
            $errorMessage = "Password must be at least 8 characters long";
            $hasErrors = true;
        } elseif ($newPassword !== $confirmPassword) {
            $errorMessage = "Passwords do not match";
            $hasErrors = true;
        } else {
            $passwordChanged = true;
        }
    }
    
    if (!$hasErrors) {
        $userId = $_SESSION["userID"];
        
        if ($passwordChanged) {
            $sql = "UPDATE User SET first_name = ?, last_name = ?, username = ?, email = ?, birthday = ?, profile_picture = ?, password = ? WHERE userID = ?";
            $stmt = $conn->prepare($sql);
            $hashedPassword = hashPassword($newPassword);
            $stmt->bind_param("sssssssi", $firstname, $lastname, $username, $email, $birthday, $profilePhotoPath, $hashedPassword, $userId);
        } else {
            $sql = "UPDATE User SET first_name = ?, last_name = ?, username = ?, email = ?, birthday = ?, profile_picture = ? WHERE userID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $firstname, $lastname, $username, $email, $birthday, $profilePhotoPath, $userId);
        }
        
        if ($stmt->execute()) {
            $_SESSION["current_user"]["first-name"] = $firstname;
            $_SESSION["current_user"]["last-name"] = $lastname;
            $_SESSION["current_user"]["username"] = $username;
            $_SESSION["current_user"]["email"] = $email;
            $_SESSION["current_user"]["birthday"] = $birthday;
            $_SESSION["current_user"]["profile-picture"] = $profilePhotoPath;
            
            $successMessage = "Profile updated successfully!";
            $currentUser = $_SESSION["current_user"]; 
            
            header("Location: profile.php");
            exit;
        } else {
            $errorMessage = "Error updating profile: " . $conn->error;
        }
        
        $stmt->close();
    }
}

require "./templates/header.php";
?>

<section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5">
            <div class="offset-1 col-lg-10 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <div class="text-center mb-4">
                            <h2>Profile</h2>
                            <p class="lead">Wan to chenj yo propayl, <?php echo htmlspecialchars($currentUser["first-name"] . " " . $currentUser["last-name"]); ?>?</p>
                            <hr>
                        </div>
                        
                        <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($errorMessage); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($successMessage); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($editMode): ?>
                        <!-- EDIT MODE -->
                        <div class="position-relative">
                            <a href="profile.php" class="position-absolute top-0 end-0 btn btn-light rounded-circle" style="margin-top: -10px; margin-right: -10px; background-color: #007377; color: white;">
                                <i class="fas fa-times"></i>
                            </a>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-4 text-center mb-4">
                                        <?php if (!empty($currentUser["profile-picture"]) && file_exists($currentUser["profile-picture"])): ?>
                                            <img src="<?php echo htmlspecialchars($currentUser["profile-picture"]); ?>" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 200px; height: 200px;">
                                                <span class="text-muted">No Profile Photo</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mb-3">
                                            <label for="profile-picture" class="form-label">Update Profile Picture</label>
                                            <input class="form-control form-control-sm" type="file" id="profile-picture" name="profile-picture">
                                            <small class="text-muted">Max size: 2MB. Formats: JPG, JPEG, PNG, GIF</small>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <h4>Watashi wa</h4>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="first-name" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="first-name" name="first-name" value="<?php echo htmlspecialchars($currentUser["first-name"]); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="last-name" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="last-name" name="last-name" value="<?php echo htmlspecialchars($currentUser["last-name"]); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($currentUser["username"]); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($currentUser["email"]); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="birthday" class="form-label">Birthday</label>
                                            <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($currentUser["birthday"]); ?>" required>
                                        </div>
                                        
                                        <h4 class="mt-4">Change Password</h4>
                                        <div class="mb-3">
                                            <label for="new-password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="new-password" name="new-password">
                                            <small class="text-muted">Leave blank to keep current password</small>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label for="confirm-password" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm-password" name="confirm-password">
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                                        </div>
                                        <br>
                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                        <?php else: ?>
                        <!-- VIEW MODE -->
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <?php if (!empty($currentUser["profile-picture"]) && file_exists($currentUser["profile-picture"])): ?>
                                    <img src="<?php echo htmlspecialchars($currentUser["profile-picture"]); ?>" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 200px; height: 200px;">
                                        <span class="text-muted">No Profile Photo</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4>Profile Information</h4>
                                    <a href="profile.php?mode=edit" class="btn btn-primary">Edit Profile</a>
                                </div>
                                
                                <table class="table">
                                    <tr>
                                        <th style="width: 30%;">First Name:</th>
                                        <td><?php echo htmlspecialchars($currentUser["first-name"]); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Last Name:</th>
                                        <td><?php echo htmlspecialchars($currentUser["last-name"]); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Username:</th>
                                        <td><?php echo htmlspecialchars($currentUser["username"]); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?php echo htmlspecialchars($currentUser["email"]); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Birthday:</th>
                                        <td><?php echo htmlspecialchars($currentUser["birthday"]); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require "./templates/footer.php" ?>