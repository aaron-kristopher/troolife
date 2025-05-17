<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] !== true || !isset($_SESSION["current_user"]) || !isset($_SESSION["current_user"]["is_admin"]) || $_SESSION["current_user"]["is_admin"] !== true) {
    header("Location: login.php");
    exit;
}

$errorMessage = '';
$successMessage = '';

// Handle password update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_password"])) {
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];
    
    $hasErrors = false;
    
    // Validate inputs
    if (empty($currentPassword)) {
        $errorMessage = "Current password is required";
        $hasErrors = true;
    }
    
    if (empty($newPassword)) {
        $errorMessage = "New password is required";
        $hasErrors = true;
    } elseif (strlen($newPassword) < 8) {
        $errorMessage = "Password must be at least 8 characters long";
        $hasErrors = true;
    }
    
    if ($newPassword !== $confirmPassword) {
        $errorMessage = "Passwords do not match";
        $hasErrors = true;
    }
    
    if (!$hasErrors) {
        // Verify current password
        $userId = $_SESSION["userID"];
        $sql = "SELECT password FROM User WHERE userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();
        
        if (!verifyPassword($currentPassword, $hashedPassword)) {
            $errorMessage = "Current password is incorrect";
        } else {
            // Update password
            $newHashedPassword = hashPassword($newPassword);
            $sql = "UPDATE User SET password = ? WHERE userID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newHashedPassword, $userId);
            
            if ($stmt->execute()) {
                $successMessage = "Password updated successfully!";
            } else {
                $errorMessage = "Error updating password: " . $conn->error;
            }
            $stmt->close();
        }
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
                            <h2>Admin Dashboard</h2>
                            <p class="lead">Manage users and update your admin password</p>
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

                        <!-- Tabs for different admin functions -->
                        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search-tab-pane" type="button" role="tab" aria-controls="search-tab-pane" aria-selected="true">Search Users</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false">Update Password</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="adminTabsContent">
                            <!-- Search Users Tab -->
                            <div class="tab-pane fade show active" id="search-tab-pane" role="tabpanel" aria-labelledby="search-tab" tabindex="0">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" id="searchInput" class="form-control" placeholder="Search by username, email, or name...">
                                            <button class="btn btn-primary" type="button" id="searchButton">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="searchResults" class="mt-4">
                                    <!-- Search results will be loaded here -->
                                    <div class="text-center text-muted">
                                        <p>Enter a search term to find users</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Update Password Tab -->
                            <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="form-text">Password must be at least 8 characters long.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for search functionality with debounce -->
<script>
// Debounce function to limit how often a function can be called
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchButton = document.getElementById('searchButton');
    
    // Debounced search function - will only execute 500ms after the user stops typing
    const debouncedSearch = debounce(function(query) {
        if (query.length < 2) {
            searchResults.innerHTML = '<div class="text-center text-muted"><p>Enter at least 2 characters to search</p></div>';
            return;
        }
        
        searchUsers(query);
    }, 500);
    
    // Event listener for input changes
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        debouncedSearch(query);
    });
    
    // Event listener for search button
    searchButton.addEventListener('click', function() {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            searchResults.innerHTML = '<div class="text-center text-muted"><p>Enter at least 2 characters to search</p></div>';
            return;
        }
        searchUsers(query);
    });
    
    // Function to perform the search
    function searchUsers(query) {
        searchResults.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        fetch('search_users.php?query=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    searchResults.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                } else if (data.users.length === 0) {
                    searchResults.innerHTML = '<div class="text-center text-muted"><p>No users found</p></div>';
                } else {
                    let html = '<div class="table-responsive"><table class="table table-hover">';
                    html += '<thead><tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Status</th></tr></thead>';
                    html += '<tbody>';
                    
                    data.users.forEach(user => {
                        const status = user.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                        html += `<tr>
                            <td>${user.userID}</td>
                            <td>${user.username}</td>
                            <td>${user.first_name} ${user.last_name}</td>
                            <td>${user.email}</td>
                            <td>${status}</td>
                        </tr>`;
                    });
                    
                    html += '</tbody></table></div>';
                    searchResults.innerHTML = html;
                }
            })
            .catch(error => {
                searchResults.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            });
    }
});
</script>

<?php require "./templates/footer.php" ?>
