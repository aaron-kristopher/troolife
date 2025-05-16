<?php
require "./templates/header.php";
require_once "database.php";

$errorMessage = null;

if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] !== true) {
    $errorMessage = "You need to be logged in to view this page.";
}
?>

<section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5">
            <div class="offset-1 col-lg-10 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <?php if (isset($_SESSION["current_user"]) && $_SESSION['is_logged_in']):?>
                            <div class="text-center mb-4">
                                <h2>Registration Successful!</h2>
                                <p class="lead">Welcome to TrooLife, <?php echo htmlspecialchars($_SESSION["current_user"]["first-name"] . " " . $_SESSION["current_user"]["last-name"]); ?>!</p>
                                <hr>
                            </div>

                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <?php if (!empty($_SESSION["current_user"]["profile-picture"]) && file_exists($_SESSION["current_user"]["profile-picture"])): ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION["current_user"]["profile-picture"]); ?>" alt="Profile Photo" class="img-fluid rounded-circle" style="max-width: 200px; max-height: 200px;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 200px; height: 200px;">
                                            <span class="text-muted">No Profile Photo</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-8">
                                    <h4>Your Account Information</h4>
                                    <table class="table">
                                        <tr>
                                            <th>Username:</th>
                                            <td><?php echo htmlspecialchars($_SESSION["current_user"]["username"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Full Name:</th>
                                            <td><?php echo htmlspecialchars($_SESSION["current_user"]["first-name"] . " " . $_SESSION["current_user"]["last-name"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><?php echo htmlspecialchars($_SESSION["current_user"]["email"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Birthday:</th>
                                            <td><?php echo htmlspecialchars($_SESSION["current_user"]["birthday"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Gender:</th>
                                            <td><?php echo htmlspecialchars(ucfirst($_SESSION["current_user"]["gender"])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <p>Your account has been created successfully. You are now logged in.</p>
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="index.php" class="btn btn-primary me-md-2">Go to Homepage</a>
                                </div>
                            </div>

                        <?php elseif (isset($errorMessage)): ?>
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Registration Error</h4>
                                <p><?php echo htmlspecialchars($errorMessage); ?></p>
                                <hr>
                                <p class="mb-0">
                                    <a href="register.php" class="btn btn-primary">Back to Registration</a>
                                </p>
                            </div>
                        <?php else: ?>
                             <div class="alert alert-warning" role="alert">
                                An unexpected state occurred. Please try logging in or registering again.
                             </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require "./templates/footer.php" ?>