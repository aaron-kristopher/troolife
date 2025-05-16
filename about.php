<?php require "./templates/header.php" ?>

<html>
<body>
    <!--ABOUT US-->
    <section class="container-fluid  position-relative" id="about-us">
    
        <div class="row h-100 align-items-center text-center overflow-hidden">
            <div class="col-11 col-md-8 col-lg-6 col-xl-7 col-xxl-5 mx-auto">
                <h1 class="about-content">About us</h1>
                <p class="mb-3 mt-3 align-items-center text-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Donec semper sagittis lectus id facilisis. 
                Aliquam diam lacus, pretium a fringilla in, rhoncus in augue. 
                </p>
                <img src="images/about-us-illustration.png" class="background-clips">
            </div>
        </div>
        
    </section>

    <!--MEMBER PROFILES-->
    <section class="container-fluid" id="member-profile">

        <div class="p-md-5 p-0 text-center">
            <div class="pt-5 py-md-5 row">
                <h2 class="text-white">Meet our team</h2>

            </div>
            <div class="py-5 px-xl-5 mx-xl-5 d-flex align-items-stretch member-card row">
                <div
                    class="d-flex flex-column justify-content-between wrapper member-card col-sm-4 col-md-12 card-item px-4 px-lg-5">
                    <img class="mb-4 pb-1 pe-1 card-img-1 rounded-circle" src="images/member-1.png" alt=" ">
                    <h6 class="text-black mb-4 mb-lg-5">April Hymn Dela Cruz</h6>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                    Donec semper sagittis lectus id facilisis. </p>
                </div>
                <div
                    class="d-flex flex-column justify-content-between wrapper member-card col-sm-4 col-md-12 card-item px-4 px-lg-5">
                    <img class="mb-4 pb-1 pe-1 card-img-1 rounded-circle" src="images/member-2.png" alt=" ">
                    <h6 class="text-black mb-4 mb-md-5">Aaron Kristopher Lim</h6>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                    Donec semper sagittis lectus id facilisis. </p>
                </div>
                <div
                    class="d-flex flex-column justify-content-between wrapper member-card col-sm-4 col-md-12 card-item px-4 px-lg-5">
                    <img class="mb-4 pb-1 pe-1 card-img-1 rounded-circle" src="images/member-3.png" alt=" ">
                    <h6 class="text-black mb-4 mb-md-5">Matthew Mascuñana</h6>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                    Donec semper sagittis lectus id facilisis. </p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
<?php require "./templates/footer.php" ?>
