<?php
session_start();

// Database connection
$user = 'root';
$pass = '';
$dbName = 'manitoudb';

$conn = new mysqli('localhost:3306', $user, $pass, $dbName);

// Check connection
if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding items to cart
if(isset($_POST['ProductID']) && isset($_POST['CustomerID'])) {
    $productID = $_POST['ProductID'];
    $customerID = $_POST['CustomerID'];

// Prepare and execute SQL statement to insert into the cart items table
$sql = "INSERT INTO `cart items` (ProductID, CustomerID) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $productID, $customerID);
$stmt->execute();
$stmt->close();
}

// Handle removing items from cart if action is "remove"
if(isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['product_id']) && isset($_SESSION['CustomerID'])) {
    $removeProductID = $_GET['product_id'];
    $customerID = $_SESSION['CustomerID'];

    // Prepare and execute SQL statement to remove item from cart
    $sql = "DELETE FROM `cart items` WHERE ProductID = ? AND CustomerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $removeProductID, $customerID);
    $stmt->execute();
    $stmt->close();
}

// Handle updating quantity
if (isset($_POST['quantityChange']) && isset($_SESSION['CustomerID']) && isset($_POST['ProductID'])) {
    $quantity = $_POST['quantityChange'];
    $customerID = $_SESSION['CustomerID'];
    $productID = $_POST['ProductID'];

    // Prepare and execute SQL statement to update quantity in the cart items table
    $sql = "UPDATE `cart items` SET Quantity = ? WHERE ProductID = ? AND CustomerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $productID, $customerID);
    $stmt->execute();
    $stmt->close();

    // Redirect back to cart.php after updating quantity
    header("Location: cart.php");
    exit();
}

// Initialize total price and total quantity
$totalPrice = 0;
$totalQuantity = 0;

// Fetch cart items for the logged-in customer with Visibility set to 'Yes'
if(isset($_SESSION['CustomerID'])) {
    $customerID = $_SESSION['CustomerID'];

    $sql = "SELECT c.ProductID, c.Quantity, p.Name, p.Price, p.ImageName 
            FROM `cart items` AS c 
            JOIN products AS p ON c.ProductID = p.ProductID 
            WHERE c.CustomerID = ? AND c.Visibility = 'Yes'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Iterate through each item in the cart
    while($row = $result->fetch_assoc()) {
        $subtotal = $row['Quantity'] * $row['Price'];
        $totalPrice += $subtotal;
        $totalQuantity += $row['Quantity'];
    }

    $stmt->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!--An icon-->
    <link rel="icon" href="./Images/LOGOTAB.png" type="image/x-icon">
    <!--Connect to the css sheet-->
    <link rel="stylesheet" href="format.css" media="all"/>
    <script src="carts.js"></script>
    <!--Title of page-->
    <title>Cart</title>

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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav"
                aria-controls="main-nav"
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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
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
                    <?php
                    if (isset($_SESSION['CustomerID'])) {
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

<!--Cart area-->
<div class="containerfluid cartArea">
    <div class="container conCart">
        <div class="row justify-center">
            <h3 class="cartName"><b><u>Cart</u></b></h3>
        </div>
        <!--Cart items-->
        <!--Divider-->
        <?php 
            // Fetch cart items for the logged-in customer with Visibility set to 'Yes'
            if(isset($_SESSION['CustomerID'])) {
                $customerID = $_SESSION['CustomerID'];
    
                $sql = "SELECT c.ProductID, c.Quantity, p.Name, p.Price, p.ImageName 
                        FROM `cart items` AS c 
                        JOIN products AS p ON c.ProductID = p.ProductID 
                        WHERE c.CustomerID = ? AND c.Visibility = 'Yes'";
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $customerID);
                $stmt->execute();
                $result = $stmt->get_result();
                // While loop to get all the cart items
                while($row = $result->fetch_assoc()) {
        ?>
        <div class="divider"></div>
        <div class="row cartTop">
            <div class="col-10 offset-1 col-md-8 offset-md-1">
                <!--Products name-->
                <h4><b><?php echo $row['Name'] ?></b></h4>
            </div>
            <div class="col ms-auto text-end">
                <!--Button to remove item from cart-->
                <a href="?action=remove&product_id=<?php echo $row['ProductID']; ?>" class="remove-link"><button type="button" class="btn-close" aria-label="Close"></button></a>
            </div>
        </div>
        <div class="row cartInfo align-items-center">
            <div class="col-auto">
                <!--Adds products image-->
                <img src="./Images/<?php echo $row['ImageName'] ?>" alt="Product picture" class="cartPic">
            </div>
            <div class="col-auto d-flex align-middle">
                <p class="me-2 quantity-label">Quanitity: <?php echo $row['Quantity'] ?></p>
                <!--Change quantity of item-->
                <!--Button trigger modal-->
                <button type="button" class="btn btn-outline-danger btn-modal" data-bs-toggle="modal" data-bs-target="#Modal<?php echo $row['ProductID']; ?>">
                    Change Quantity
                </button>

                <!-- Modal -->
                <div class="modal fade" id="Modal<?php echo $row['ProductID']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <form action="cart.php" method="POST">
                                    <label for="quantityChange" class="form-label">Quantity:</label>
                                    <select name="quantityChange" id="quantityChange">
                                        <option selected value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                    <!--Pass the ProductID to identify the item-->
                                    <input type="hidden" name="ProductID" value="<?php echo $row['ProductID']; ?>">
                                    <button type="submit" class="btn btn-danger">Save changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col ms-auto text-end">
                <!--Price of the item timed by the quantity amount-->
                <p><b> R <?php echo number_format($row['Price'] * $row['Quantity'], 2) ?></b></p>
            </div>
        </div>
        <?php 
            }
    
            $stmt->close();
        }
        
        $conn->close(); 
        ?>
        <!--Divider-->
        <div class="divider"></div>
        <!--Cart summary area-->
        <!--Divider-->
        <div class="divider"></div>
        <div class="row cartSum">
            <h3><b><u>Cart Summary</u></b></h3>
        </div>
        <div class="row">
            <div class="col-10 offset-1 col-md-8 offset-md-1">
                <!--Total amount of items in cart-->
                <h4>Quanitity of items:</h4>
            </div>
            <div class="col ms-auto text-end">
                <p><b><?php echo $totalQuantity ?></b></p>
            </div>
        </div>
        <div class="row">
            <div class="col-10 offset-1 col-md-8 offset-md-1">
                <!--Total price of items in cart-->
                <h4>Total:</h4>
            </div>
            <div class="col ms-auto text-end">
                <p><b>R <?php echo number_format($totalPrice, 2) ?></b></p>
            </div>
        </div>
        <div class="row">
            <div class="col checkButton">
                <!--Checkout button if there are items in the cart-->
                <?php if ($totalQuantity > 0) { ?>
                    <a href="Checkout.php"><button type="button" class="btn btn-danger w-50">Checkout</button></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!--Footer: footer-->
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

<!-- Option 2: Separate Popper and Bootstrap JS -->
<!--
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>
-->

</body>
</html>