<?php
session_start();

// Database connection
$user = 'root';
$pass = '';
$dbName = 'manitoudb';

$conn = new mysqli('localhost:3306', $user, $pass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['CustomerID'])) {
    header("Location: login.php");
    exit();
}

// Check if logout button is clicked and timestamp matches
if (isset($_POST['logout']) && $_POST['timestamp'] == $_SESSION['logout_timestamp']) {
    // Perform logout actions
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Set logout timestamp in session
$_SESSION['logout_timestamp'] = time();

// Retrieve user information from the database
$customerId = $_SESSION['CustomerID'];
$sql = "SELECT * FROM customer WHERE CustomerID = $customerId"; // Query to retrieve user information
$result = mysqli_query($conn, $sql);

if ($result) {
    // Fetch user information as an associative array
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle error if user information retrieval fails
    echo "Error: " . mysqli_error($conn);
}

// Retrieve orders for the logged-in customer
$ordersSql = "SELECT o.*, a.Address, a.Country, a.City, a.PostalCode 
              FROM `order` o 
              JOIN address a ON o.AddressID = a.AddressID 
              WHERE a.CustomerID = $customerId";
$ordersResult = mysqli_query($conn, $ordersSql);

if (!$ordersResult) {
    // Handle error if order retrieval fails
    echo "Error: " . mysqli_error($conn);
}
?>

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
    <link rel="icon" href="./Images/LOGOTAB.png" type="image/x-icon">
    <!--Connect to the css sheet-->
    <link rel="stylesheet" href="Order.css" media="all" />
    <!--Title of webpage-->
    <title>Orders</title>

  </head>
  <body>

    <!--Navigation-->
    <nav class="navbar navbar-expand-md navbar-light" id="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="Main.php" class="navbar-brand">
                <span>
                    <img src="./Images/ManLOGO.png" alt="logo" id="Logo">
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
                    <a href="Main.php #aboutus" class="nav-link titles">About us</a>
                </li>
                <li class="nav-item">
                    <a href="Main.php #faq" class="nav-link titles">FAQ</a>
                </li>
                <li class="nav-item">
                    <a href="main.php #footertable" class="nav-link titles">Contact us</a>
                </li>
                <li class="nav-item">
                <a href="profile.php" class="nav-link titles"> <i class="bi bi-person-fill"></i> Profile</a>
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

    <!--Section 1-->
    <!--Navigation for the profile-->
    <div class="container-fluid section1">
        <div class="container insideProf">
            <div class="row paddings">
            </div>
            <div class="row profilePic">
                <img src="./Images/profileLOGORed.PNG" alt="profile">
            </div>
            <div class="row">
                <div class="col">
                    <p><?php echo $user['Name'] . ' ' . $user['surname']; ?></p>
                </div>
                <div class="col profsettings">
                    <a href="profile.php">Profile</a>
                </div>
                <div class="col profsettings">
                    <a href="myAddress.php">MyAddress</a>
                </div>
                <div class="col profsettings">
                    <a href="MyOrders.php">MyOrders</a>
                </div>
                <div class="col">
                    <!-- Logout form -->
                    <form method="post">
                        <input type="hidden" name="logout" value="true">
                        <button type="submit" class="btn btn-danger">Log out</button>
                        <!-- Include timestamp to make each request unique -->
                        <input type="hidden" name="timestamp" value="<?= time() ?>">
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="divider"></div>
            </div>

            <!--Order area-->
            <div class="row profilesHeader1">
                <h3><u>Orders</u></h3>
            </div>
            <!--A while loop to get all the orders that the user has-->
            <?php if ($ordersResult->num_rows > 0) { 
                while ($row = $ordersResult->fetch_assoc()) {?>
                <!--Divider-->
                <div class="divider"></div>
            <div class="row">
                <!--Gets the order ID-->
                <p><b><u>Order #<?php echo $row['OrderID']; ?></u></b></p>
            </div>
            <div class="row">
                <div class="col-4 text-start">
                    <!--Quantity of items in the order-->
                    <p>Quantity: <?php echo $row['Quantity']; ?></p>
                </div>
                <div class="col-4 text-center">
                    <!--The total of the order-->
                    <p>Total: R <?php echo number_format($row['Total'], 2); ?></p>
                </div>
                <div class="col-4 text-end">
                    <!--The date when the order took place-->
                    <p>Date Ordered: <?php echo $row['DateOrdered']; ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col text-end">
                    <!--The status of the order-->
                    <p>Order Status: <?php echo $row['Status']; ?></p>
                </div>
            </div>
            <?php } }else{ ?>
                <p>No orders yet.</p>
                <?php } ?>
            <div class="row paddings"></div>
        </div>
    </div>

    <!--Footer-->
    <div class="container-fluid section4">
        <div class="container">
            <div class="row address">
                <table id="footertable">
                    <tr>
                        <td>
                            500 Terry Francine Street <br>
                            San Francisco, CA 94158 <br>
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