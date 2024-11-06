<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!--An icon-->
    <link rel="icon" href="LOGOTAB.png" type="image/x-icon">
    <!--Connect to the css sheet-->
    <link rel="stylesheet" href="format.css" media="all" />
    <!--Title of page-->
    <title>Manitou website</title>

  </head>
  <body>
    
    <!--Navigation-->
    <nav class="navbar navbar-expand-md navbar-light" id="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="Main.php" class="navbar-brand">
                <span>
                    <!--Logo-->
                    <img src="./images/ManLOGO.png" alt="logo" id="Logo">
                </span>
            </a>
        </div>
        <!--toggle button for mobile navigation-->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" 
        aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!--Navigation links for mobile-->
        <div class="collapse navbar-collapse justify-content-end" id="main-nav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="Main.php" class="nav-link titles">Home</a>
                </li>
                <!--Dropdown menu for shop-->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Shop
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                        <li><a class="dropdown-item" href="ShopMachines.php">Machines</a></li>
                        <li><a class="dropdown-item" href="ShopParts.php">Parts</a></li>
                        <li><a class="dropdown-item" href="merch.php">Branded merch</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#aboutus" class="nav-link titles">About us</a>
                </li>
                <li class="nav-item">
                    <a href="#faq" class="nav-link titles">FAQ</a>
                </li>
                <li class="nav-item">
                    <a href="#footertable" class="nav-link titles">Contact us</a>
                </li>
                <!--When the user is not logged in it should take the user to the log in page-->
                <!--When the user is logged in it should take them to the profile page-->
                <li class="nav-item">
                    <?php
                        session_start();
                        if(isset($_SESSION['CustomerID'])) {
                            echo '<a href="profile.php" class="nav-link titles"> <i class="bi bi-person-fill"></i> Profile</a>';
                        } else {
                            echo '<a href="login.php" class="nav-link titles"> <i class="bi bi-person-fill"></i> Log In</a>';
                        }
                    ?>
                </li>
                <li class="nav-item">
                <a href="cart.php" class="nav-link titles"> <i class="bi bi-basket2-fill"></i> </i> Cart</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    
<!--Divider-->
<div class="divider"></div>

<!--Section 1: choice section-->
<div class="countainer-fluid section1">
    <div class="choicecontainer">
        <div class="row shopheading">
          <!--The heading of the front page-->
            <div class="col-lg-12">
                <h1 class="display-1 text-center">
                Manitou's Online Shop</h1>
            </div>
        </div>
        <!--Adds different choices that the user would want to shop at-->
        <div class="row choicepics">
            <div class="col-10 offset-1 col-sm-8 offset-sm-2 col-md-4 offset-md-0 col-lg-4">
                   <a href="ShopMachines.php"><img src="./Images/MachineStart.jpg" alt="parts" class="img-fluid"  style="width: auto; height: auto;"></a>
            </div>
            <div class="col-10 offset-1 col-sm-8 offset-sm-2 col-md-4 offset-md-0 col-lg-4">
                <a href="ShopParts.php"><img src="./Images/PartsStart.jpg" alt="parts" class="img-fluid" style="width: auto; height: auto;"></a>
            </div>
            <div class="col-10 offset-1 col-sm-8 offset-sm-2 col-md-4 offset-md-0 col-lg-4">
                <a href="merch.php"><img src="./Images/MerchStart.jpg" alt="parts" class="img-fluid" style="width: auto; height: auto;"></a>
            </div>
        </div>
    </div>
</div>

<!--Section 2: About section-->
<div class="container-fluid section2">
    <div class="container">
        <div class="row">
          <!--About us information-->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6" id="aboutus">
                <h1><u>About Us</u></h1> <br>
                <p>At Manitou Online Store, we are committed to providing <br> our customers with the best farm equipment and supplies. <br>
                    We offer a diverse range of products, including machines, <br> machine parts, and merchandise such as clothing <br> and toys.
                    By joining our community, you'll have access to <br> exclusive deals, promotions, and expert advice to help <br> you grow your farm.</p> <br>
                <a href="Register.php"><button type="button" class="btn btn-danger">Register Now</button></a>
            </div>
            <!--Picture of the machines-->
            <div class="col-12 col-md-6 col-lg-6" id="picture">
                <img src="./Images/machines.jpg" alt="machines" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!--Section 3: FAQ-->
<div class="container-fluid section3">
    <div class="row faqheader" id="faq">
        <h1>Frequently asked questions</h1>
    </div>
</div>

<!--Accordion for FAQ-->
<div class="col-md-6 offset-md-3">
<div class="accordion accordion-flush row" id="accordionFlushFAQ">
  <div class="accordion-item">
    <h2 class="accordion-header" id="flush-headingOne">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
        What does Manitou sell?
      </button>
    </h2>
    <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushFAQ">
      <div class="accordion-body">
        Manitou sells many different types of machines, such as telehandlers. We also provide parts for these machines for repairs or extra addons. <br> 
        Manitou is now also selling branded merchandise, such as toys and clothing related to our brand.
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="flush-headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
        How do I purchase a machine?
      </button>
    </h2>
    <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushFAQ">
      <div class="accordion-body">
        To purchase a machine that you are interested in, you must add it to cart and checkout. The machine will automatically be R0,00. <br> 
        Once you are checked out we will send you a quote for that machine. Once you recieved the quote, 
        contact the email address stating that you are interested to move on with the purchase.
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="flush-headingThree">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
        How do I return my order?
      </button>
    </h2>
    <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushFAQ">
      <div class="accordion-body">
        To return the ordered items, please contact us at <b>info.centresa@manitou-group.com</b> or call <b>+27 10 449 4800</b>. <br> 
        If it is related to parts and it is after working hours please call <b>+27 76 022 4535</b>.
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="flush-headingFour">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseThree">
        Could I collect my items?
      </button>
    </h2>
    <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFlushFAQ">
      <div class="accordion-body">
        It is possible to collect rather than getting it delivered. Once you are at checkout, select shipping option <b>'Collection'</b>. <br> 
        When you are ready to collect, collect it at <b>Proton Industrial Park, Proton Street, Chloorkop, Ext 65 , 1619</b>.
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="flush-headingFive">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseThree">
        How long does it take to get a refund?
      </button>
    </h2>
    <div id="flush-collapseFive" class="accordion-collapse collapse" aria-labelledby="flush-headingFive" data-bs-parent="#accordionFlushFAQ">
      <div class="accordion-body">
        It typically takes three to five business days to recieve the refund.
      </div>
    </div>
  </div>
</div>
</div>

<!--Section 4: the footer-->
<!--Table used for footer information-->
<div class="container-fluid section4">
    <div class="container">
        <div class="row address">
            <table id="footertable">
                <tr>
                    <td>
                      <!--Address-->
                        500 Terry Francine Street <br>
                        San Francisco, CA 94158 <br>
                        <!--Email-->
                        info@my-domain.com
                    </td>
                    <th class="middleTable">
                        Operating Hours
                    </th>
                    <th>
                        Delivery Hours
                    </th>
                </tr>
                <tr>
                    <td>
                      <!--Contact details-->
                        Tel: 123-456-7890 <br>
                        Fax: 123-456-7890
                    </td>
                    <td class="middleTable">
                        Mon - Fri: 8am - 4.30pm <br>
                        ​Saturday: Closed <br>
                        ​Sunday: Closed
                    </td>
                    <td>
                        Mon - Fri: 8am - 4.30pm​​ <br>
                        Saturday: Closed​ <br>
                        Sunday: Closed
                    </td>
                </tr>
            </table>
        </div>
        <div class="row socialsheader">
            <h4>Follow us on social media:</h4>
        </div>
        <div class="row socials">
          <!--Socials-->
            <table id="socialstable">
                <tr>
                    <td>
                        <a href="https://www.facebook.com/ManitouMEA/"><img src="./Images/facebook.PNG" alt="facebook"></a>
                    </td>
                    <td>
                        <a href="https://www.instagram.com/manitou_worldwide/"><img src="./Images/insta.PNG" alt="instagram"></a>
                    </td>
                    <td>    
                        <a href="https://www.linkedin.com/company/manitou-mea/posts/?feedView=all"><img src="./Images/Linkedin.PNG" alt="LinkedIn"></a>
                    </td>
                    <td>
                        <a href="https://www.youtube.com/@ManitouMEA"><img src="./Images/YouTube.PNG" alt="YouTube"></a>
                    </td>
                </tr>
            </table>
            
        </div>
        <div class="row Manitou2024">
            <p>@ 2024 Manitou</p>
        </div>
    </div>
</div>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>