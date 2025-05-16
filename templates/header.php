<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_db"])) { 
    $_SESSION["user_db"] = [];
}
if (!isset($_SESSION["is_logged_in"])) { 
    $_SESSION["is_logged_in"] = false;
}
if (!isset($_SESSION["current_user"])) { 
    $_SESSION["current_user"] = null;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TrooLife</title>
    <link rel="icon" href="https://external-content.duckduckgo.com/ip3/www.troolife.com.ico">

    <!--BOOTSTRAP-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!--GOOGLE FONTS-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!--GOOGLE ICONS-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=chevron_right" />
    <!--FONT AWESOME ICONS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <!--START NAVBAR -->
    <nav class="autohide navbar justify-content-center navbar-expand-lg fixed-top navbar-dark nav-sticky shadow-lg" id="navbar">
        <div class="container-fluid px-3 px-xxl-5 py-2">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                aria-controls=" navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <img src="images/icons/menu.svg">
            </button>

            <a class="navbar-brand m-0" href="index.php">
                <img src="images/logo.svg" alt="Troolife">
            </a>

            <ul class="text-end nav flex-nowrap order-lg-2">
                <li class="mx-1 pe-2 nav-item">
                    <a href="#">
                        <img class="mb-1" src="images/icons/shopping_cart.svg" alt="cart">
                        <span id="cart-counter">0</span>
                    </a>
                </li>
                <li class="px-0 px-xxl-2 mx-1 nav-item d-none d-lg-inline" id="line">
                    <img class="mb-1" src="images/icons/line.png" alt="|">
                </li>
                <li class="d-none d-lg-inline px-0 px-xxl-2 mx-1 nav-item" id="profile">
                    <div class="dropdown">
                        <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#">
                            <!-- ($_SESSION["is_logged_in"] && isset($_SESSION['current_user']['profile-picture']) && !empty($_SESSION['current_user']['profile-picture']) && file_exists($_SESSION['current_user']['profile-picture'])): ?>
                                <img class="mb-1 pe-1 rounded-circle" src="echo htmlspecialchars($_SESSION['current_user']['profile-picture']); ?>" alt="profile" style="width: 24px; height: 24px; object-fit: cover;"> -->
                            <img class="mb-1 pe-1" src="images/icons/account_circle.svg" alt="profile">
                            
                            <span class="d-none d-lg-inline nav-item-text">
                                My Office
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($_SESSION["is_logged_in"]): ?>
                                <li><a class="dropdown-item" href="./profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="./about.php">About Us</a></li>
                                <li><a class="dropdown-item" href="./logout.php">Log out</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="./about.php">About Us</a></li>
                                <li><a class="dropdown-item" href="./login.php">Log in</a></li>
                                <li><a class="dropdown-item" href="./register.php">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="ps-0 ps-xl-2 ms-0 ms-sm-1 nav-item" id="language">
                    <a href="#">
                        <img class="mb-1 pe-1" src="images/icons/language.svg" alt="language">
                        <span class="nav-item-text">English</span>
                        <img class="pb-1 ps-1" src="images/icons/dropdown.svg" alt="dropdown">
                    </a>
                </li>
            </ul>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mx-auto" id="navbar-navlist">
                    <li class="nav-item px-0 px-xxl-2">
                        <a class="nav-link pe-0 pe-xl-2" href="index.php#mission">Mission</a>
                    </li>
                    <li class="nav-item px-0 px-xxl-2">
                        <a class="nav-link pe-0 pe-xl-2" href="index.php#lifeline">LifeLine</a>
                    </li>
                    <li class="nav-item px-0 px-xxl-2">
                        <a class="nav-link pe-0 pe-xl-2" href="index.php#nutrition">Nutrition</a>
                    </li>
                    <li class="nav-item px-0 px-xxl-2">
                        <a class="nav-link pe-0 pe-xl-2" href="index.php#referral">Free by Referral </a>
                    </li>
                    <li class="nav-item px-0 px-xxl-2">
                        <a class="nav-link pe-0 pe-xl-2" href="index.php#relationship">Public Relations</a>
                    </li>
                    <li class="nav-item px-0 px-xxl-2">
                        <a class="nav-link pe-0 pe-xl-2" href="index.php#company">Company</a>
                    </li>
                    <li class="nav-item px-0 px-xxl-2">
                        <a class="nav-link pe-0 pe-xl-2" href="index.php#management">Management</a>
                    </li>

                    <li class="d-inline d-lg-none px-0 nav-item" id="profile">
                        <a class="nav-link pe-0 pe-xl-2" href="#">My Office</a>
                    </li>
                    
                    <?php if ($_SESSION["is_logged_in"]): ?>
                    <li class="d-inline d-lg-none px-0 nav-item">
                        <a class="nav-link pe-0 pe-xl-2" href="profile.php">My Profile</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!--START OF MEMBER BLOCK-->

    <?php if ($_SESSION["is_logged_in"] && isset($_SESSION["current_user"])): 
        $currentUser = $_SESSION["current_user"];
       
        $profilePicPath = 'images/icons/account_circle.svg'; 
        if (!empty($currentUser['profile-picture']) && file_exists($currentUser['profile-picture'])) {
            $profilePicPath = htmlspecialchars($currentUser['profile-picture']);
        }
        $displayName = htmlspecialchars( ($currentUser['first-name'] ?? '') . ' ' . ($currentUser['last-name'] ?? 'User'));
    ?>

        <section class="container-fluid px-sm-5 d-flex align-items-center justify-content-between" id="member-block">
            <div class="d-flex align-items-center">
                <img src="<?php echo $profilePicPath; ?>" height="55" width="55" alt="profile" class="rounded-circle" style="object-fit: cover;">
                <p class="mb-0 ps-3" id="member-name"><?php echo $displayName; ?></p>
            </div>
            <div class="d-flex align-items-center">
                <img src="images/icons/contact.svg" alt="contact">
                <img class="ps-3" src="images/icons/messages.svg" alt="messages">
            </div>
        </section>
    <?php endif; ?>