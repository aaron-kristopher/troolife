<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once "database.php";

$firstname = $lastname = $username = $email = $birthday = $gender = $password = "";
$profilePhotoPath = "";
$firstnameErr = $lastnameErr = $usernameErr = $emailErr = $birthdayErr = $genderErr = $profileErr = $passwordErr = "";

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$hasErrors = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Validate First Name ---
    if (empty($_POST["first-name"])) {
        $firstnameErr = "First name is required";
        $hasErrors = true;
    } else {
        $firstname = trim($_POST["first-name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $firstname)) {
            $firstnameErr = "Only letters and white space allowed";
            $hasErrors = true;
        }
    }

    // --- Validate Last Name ---
    if (empty($_POST["last-name"])) {
        $lastnameErr = "Last name is required";
        $hasErrors = true;
    } else {
        $lastname = trim($_POST["last-name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lastname)) {
            $lastnameErr = "Only letters and white space allowed";
            $hasErrors = true;
        }
    }

    // --- Validate username ---
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
        $hasErrors = true;
    } else {
        $username = trim($_POST["username"]);
        
        $sql = "SELECT userID FROM User WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $usernameErr = "Username already taken";
                $hasErrors = true;
            }
            $stmt->close();
        }
    }

    // --- Validate Email ---
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $hasErrors = true;
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            $hasErrors = true;
        }
    }

    // --- Validate Profile Photo ---
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == UPLOAD_ERR_OK) {
        $targetFile = $uploadDir . basename($_FILES["profile-picture"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $fileSize = $_FILES["profile-picture"]["size"];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        $check = getimagesize($_FILES["profile-picture"]["tmp_name"]);
        if ($check === false) {
            $profileErr = "File is not an image.";
            $hasErrors = true;
        }
        elseif ($fileSize > $maxFileSize) {
            $profileErr = "Sorry, your file is too large (Max 2MB).";
            $hasErrors = true;
        }
        elseif (!in_array($imageFileType, $allowedTypes)) {
            $profileErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $hasErrors = true;
        }
        else {
            $uniqueName = uniqid('profile_', true) . '.' . $imageFileType;
            $newTargetFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $newTargetFile)) {
                $profilePhotoPath = $newTargetFile; 
            } else {
                $profileErr = "Sorry, there was an error uploading your file.";
                $hasErrors = true;
            }
        }
    } elseif (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] != UPLOAD_ERR_NO_FILE) {
        $profileErr = "File upload error: " . $_FILES['profile-picture']['error'];
        $hasErrors = true;
    } else {
        $profileErr = "Profile photo is required.";
        $hasErrors = true;
    }

    // --- Validate Birthday ---
    if (empty($_POST["birthday"])) {
        $birthdayErr = "Birthday is required";
        $hasErrors = true;
    } else {
        $birthday = $_POST["birthday"];
         if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $birthday)) {
             $birthdayErr = "Invalid date format."; 
             $hasErrors = true;
         }
    }

    // --- Validate Gender ---
    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
        $hasErrors = true;
    } else {
        $gender = $_POST["gender"];
        if (!in_array($gender, ['male', 'female'])) {
             $genderErr = "Invalid gender selection";
             $hasErrors = true;
        }
    }

    // --- Validate Password ---
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
        $hasErrors = true;
    } else {
        $password = $_POST["password"]; 
        if (strlen($password) < 8) {
            $passwordErr = "Password must be at least 8 characters long";
            $hasErrors = true;
        }
    }

    if (!$hasErrors) {
        // Hash the password before storing
        $hashed_password = hashPassword($password);
        
        // Prepare an insert statement
        $sql = "INSERT INTO User (username, email, first_name, last_name, password, birthday, profile_picture, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssss", $param_username, $param_email, $param_firstname, $param_lastname, $param_password, $param_birthday, $param_profile);
            
            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_password = $hashed_password;
            $param_birthday = $birthday;
            $param_profile = $profilePhotoPath;
            
            if ($stmt->execute()) {
                $userID = $conn->insert_id;
                
                $_SESSION['is_logged_in'] = true;
                $_SESSION['userID'] = $userID;
                $_SESSION['current_user'] = [
                    'username' => $username,
                    'email' => $email,
                    'first-name' => $firstname,
                    'last-name' => $lastname,
                    'profile-picture' => $profilePhotoPath,
                    'birthday' => $birthday,
                    'gender' => $gender
                ];
                
                header("Location: register-success.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<?php require "./templates/header.php"?>

<!-- Section: Design Block -->
<section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5">
            <div class="offset-md-1 col-lg-10 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <?php if ($hasErrors && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                            <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
                                Please correct the errors below and try again.
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="needs-validation">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <label class="form-label" for="first-name">First Name</label>
                                        <input type="text" id="first-name" name="first-name" class="form-control" placeholder="Juan" value="<?php echo htmlspecialchars($firstname); ?>" required />
                                        <span style="color: red; font-size: 14px;" class="error"><?php echo $firstnameErr ? "* ".$firstnameErr : ""; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-outline">
                                        <label class="form-label" for="last-name">Last Name</label>
                                        <input type="text" id="last-name" name="last-name" class="form-control" placeholder="Dela Cruz" value="<?php echo htmlspecialchars($lastname); ?>" required />
                                        <span style="color: red; font-size: 14px;" class="error"><?php echo $lastnameErr ? "* ".$lastnameErr : ""; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="form-outline mb-4">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="TonyFowlerLuver69" value="<?php echo htmlspecialchars($username); ?>" required />
                                <span style="color: red; font-size: 14px;" class="error"><?php echo $usernameErr ? "* ".$usernameErr : ""; ?></span>
                            </div>

                            <!-- Email -->
                            <div class="form-outline mb-4">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="name@email.com" value="<?php echo htmlspecialchars($email); ?>" required />
                                <span style="color: red; font-size: 14px;" class="error"><?php echo $emailErr ? "* ".$emailErr : "";?></span>
                            </div>

                            <!-- Birthday -->
                            <div class="form-outline mb-4">
                                <label class="form-label" for="birthday">Birthday</label>
                                <input type="date" id="birthday" name="birthday" class="form-control" value="<?php echo htmlspecialchars($birthday); ?>" required />
                                <span style="color: red; font-size: 14px;" class="error"><?php echo $birthdayErr ? "* ".$birthdayErr : "";?></span>
                            </div>

                            <!-- Gender (Radio Buttons) -->
                            <div class="mb-4">
                                <label class="form-label d-block">Gender</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="male" name="gender" value="male" <?php echo ($gender == "male" || empty($gender)) ? "checked" : ""; ?>>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="female" name="gender" value="female" <?php echo ($gender == "female") ? "checked" : ""; ?>>
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                                <span style="color: red; font-size: 14px; display: block; margin-top: 5px;" class="error"><?php echo $genderErr ? "* ".$genderErr : ""; ?></span>
                            </div>

                            <!-- Profile Picture -->
                            <div class="mb-4">
                                <label for="profile-picture" class="form-label">Profile Picture</label>
                                <input class="form-control form-control-sm" type="file" id="profile-picture" name="profile-picture" required>
                                <span style="color: red; font-size: 14px;" class="error"><?php echo $profileErr ? "* ".$profileErr : ""; ?></span>
                            </div>

                            <!-- Password -->
                            <div class="form-outline mb-5">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required />
                                <span style="color: red; font-size: 14px;" class="error"><?php echo $passwordErr ? "* ".$passwordErr : ""; ?></span>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid pt-5">
                                <button type="submit" class="btn btn-secondary mb-5">Sign Up</button>
                            </div>

                            <!-- Register buttons -->
                            <div class="text-center">
                                <h6>Already have an account? <a class="text-primary" href="login.php">Log in.</a></h6>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section: Design Block -->

<?php require "./templates/footer.php" ?>