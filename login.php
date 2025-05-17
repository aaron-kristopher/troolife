<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "database.php";

$username = $password = "";
$usernameErr = $passwordErr = "";
$hasErrors = false;

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $usernameErr = "* Username is required";
        $hasErrors = true;
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "* Password is required";
        $hasErrors = true;
    } else {
        $password = $_POST["password"]; 
    }

    if (!$hasErrors) {
        // First check if the credentials are in the user table
        $userFound = false;
        $sql = "SELECT userID, adminID, username, email, first_name, last_name, password, profile_picture, birthday, is_active FROM user WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $userFound = true;
                    $stmt->bind_result($userID, $adminID, $db_username, $email, $first_name, $last_name, $hashed_password, $profile_picture, $birthday, $is_active);
                    $is_admin = 0; // Default to not admin
                    
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Check if user is active
                            if (!$is_active) {
                                $usernameErr = "* Account is inactive. Please contact an administrator.";
                                $hasErrors = true;
                            } else {
                                session_regenerate_id(true);
                                
                                $_SESSION["is_logged_in"] = true;
                                $_SESSION["userID"] = $userID;
                                // Check if user is an admin based on adminID
                                if ($adminID !== null) {
                                    $is_admin = 1; // User is an admin
                                }
                                
                                $_SESSION["current_user"] = [
                                    "username" => $db_username,
                                    "email" => $email,
                                    "first-name" => $first_name,
                                    "last-name" => $last_name,
                                    "profile-picture" => $profile_picture,
                                    "birthday" => $birthday,
                                    "is_admin" => (bool)$is_admin,
                                    "is_active" => (bool)$is_active
                                ];
                                
                                header("location: index.php");
                                exit;
                            }
                        } else {
                            $passwordErr = "* Invalid credentials";
                            $hasErrors = true;
                        }
                    }
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
        
        // If not found in user table or password didn't match, check admin table
        if (!$userFound || $hasErrors) {
            $hasErrors = false; // Reset errors to check admin table
            $sql = "SELECT adminID, username, email, first_name, last_name, password FROM admin WHERE username = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $param_username);
                $param_username = $username;
                
                if ($stmt->execute()) {
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 1) {
                        $stmt->bind_result($adminID, $db_username, $email, $first_name, $last_name, $hashed_password);
                        
                        if ($stmt->fetch()) {
                            if (password_verify($password, $hashed_password)) {
                                session_regenerate_id(true);
                                
                                $_SESSION["is_logged_in"] = true;
                                $_SESSION["adminID"] = $adminID;
                                $_SESSION["current_user"] = [
                                    "username" => $db_username,
                                    "email" => $email,
                                    "first-name" => $first_name,
                                    "last-name" => $last_name,
                                    "profile-picture" => null, // Admins may not have profile pictures
                                    "birthday" => null, // Admins may not have birthdays stored
                                    "is_admin" => true, // Admin is always an admin
                                    "is_active" => true // Admins are always active
                                ];
                                
                                header("location: index.php");
                                exit;
                            } else {
                                $passwordErr = "* Invalid credentials";
                                $hasErrors = true;
                            }
                        }
                    } else if (!$userFound) { // Only show error if not found in both tables
                        $usernameErr = "* Invalid credentials";
                        $hasErrors = true;
                    }
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                $stmt->close();
            }
        }
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
            <div class="col-lg-5 mb-5 mb-lg-0" style="z-index: 10">
                <h1 class="my-5 display-5 fw-bold ls-tight text-white">Elevate Yourself with <span class="position-relative"><span class="underline">TrooLife</span></span></h1>
                <p class="mb-4 opacity-70 text-white">
                    Our mission is to help you take charge of your health, wellness, and longevity.
                    With modern diets lacking key nutrients, our high-quality supplements provide essential
                    vitamins and minerals for a shitton of money.
                </p>
            </div>

            <div class="offset-md-1 col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                             <?php if ($hasErrors && !empty($usernameErr) && $usernameErr === "* Invalid credentials"): ?>
                                <div class="alert alert-danger mb-4" role="alert">
                                    Invalid username or password.
                                </div>
                             <?php elseif ($hasErrors && !empty($passwordErr) && $passwordErr === "* Invalid credentials"): ?>
                                 <div class="alert alert-danger mb-4" role="alert">
                                     Invalid username or password.
                                 </div>
                             <?php endif; ?>

                            <!-- Username input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required />
                                <span class="error" style="color: red; font-size: 14px;"><?php echo $usernameErr ?: ''; ?></span>
                            </div>

                            <!-- Password input -->
                            <div data-mdb-input-init class="form-outline mb-5">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required />
                                <span class="error" style="color: red; font-size: 14px;"><?php echo $passwordErr ?: ''; ?></span>
                            </div>

                            <!-- Submit button -->
                            <div class="d-grid">
                                <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-secondary mb-4">
                                    Log in
                                </button>
                            </div>

                            <!-- Register buttons -->
                            <div class="text-center">
                                <h6>No account? <a class="text-primary" href="register.php">Create one here.</a></h6>
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