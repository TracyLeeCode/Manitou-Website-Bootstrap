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

// Fetch customer details
$customerName = "";
$customerSurname = "";
if (isset($_SESSION['CustomerID'])) {
    $customerID = $_SESSION['CustomerID'];

    // Prepare and execute the SQL statement to fetch customer details
    $sql = "SELECT Name, surname FROM customer WHERE CustomerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $customerName = $row['Name'];
        $customerSurname = $row['surname'];
    }

    $stmt->close();
}

// Fetch address details
$sql = "SELECT AddressID, Address, City, PostalCode, Country FROM address WHERE CustomerID = '$customerID'";
$result = $conn->query($sql);

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
    $cartResult = $stmt->get_result();

    // Iterate through each item in the cart
    while($row = $cartResult->fetch_assoc()) {
        $subtotal = $row['Quantity'] * $row['Price'];
        $totalPrice += $subtotal;
        $totalQuantity += $row['Quantity'];
    }

    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $addressID = $_POST['addressID'];
    $bank = $_POST['bank'];
    $accountNum = password_hash($_POST['accountNum'], PASSWORD_DEFAULT); // Hash the account number
    $expiry = $_POST['Expiry'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert payment details into the payment table
        $sql = "INSERT INTO payment (CustomerID, Bank, AccountNumber, Expiry) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $customerID, $bank, $accountNum, $expiry);
        $stmt->execute();

        // Get the PaymentID of the inserted payment
        $paymentID = $stmt->insert_id;

        // Close the statement
        $stmt->close();

        // Update cart items table to set Visibility to "No"
        $sql = "UPDATE `cart items` SET Visibility = 'No' WHERE CustomerID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        
        // Close the statement
        $stmt->close();

        // Insert order details into the order table
        $sql = "INSERT INTO `order` (PaymentID, Total, Quantity, AddressID) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idii", $paymentID, $totalPrice, $totalQuantity, $addressID);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Check if the order was successfully inserted
        if ($stmt->affected_rows > 0) {
            echo 'success';
        } else {
            echo 'failed';
        }

        $stmt->close();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo "Error: " . $e->getMessage(); // Log or display the error
    }
}

$conn->close();
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
                        echo '<a href="login.php" class="nav-link titles"> <i class="bi bi-person-fill"></i> Log in</a>';
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

<!--Payment area-->
<div class="container-fluid cartArea">
    <div class="container conCart">
        <div class="row">
            <h3 class="cartName"><b><u>Checkout</u></b></h3>
        </div>
        <div class="row">
            <h2 class="cartName"><b><?php echo htmlspecialchars($customerName) . ' ' . htmlspecialchars($customerSurname); ?></b></h2>
        </div>
        <!--Divider-->
        <div class="divider"></div>
        <div class="row">
            <h3 class="cartName choosePay"><b><u>Choose Address:</u></b></h3>
        </div>
        <div class="row radioCheck">
            <form action="" class="row g-3" method="POST">
                <div class="form-check">
                    <!--Allows user to choose address-->
                        <?php
                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                        ?>
                                <input class="form-check-input" type="radio" name="addressID" id="address<?php echo $row["AddressID"]; ?>" value="<?php echo $row["AddressID"]; ?>">
                                <label class="form-check-label" for="address<?php echo $row["AddressID"]; ?>">
                                        <div class="row addresses">
                                        <p> Address: <br>
                                        <?php echo $row["Address"]; ?> <br>
                                        <?php echo $row["City"]; ?> <br>
                                        <?php echo $row["PostalCode"]; ?> <br>
                                        <?php echo $row["Country"]; ?></p>
                                    </div>
                                    <div class="divider"></div>
                        <?php
                                }
                            } else {
                                echo "<div class='row addresses'><p>No addresses found. Please add an address in your profile.</p></div>";
                            }
                        ?>
                    </label>
                </div>
        </div>
        <div class="divider"></div>
        <div class="row">
            <h3 class="cartName choosePay"><b><u>Choose payment method:</u></b></h3>
        </div>
        <div class="row">
                <div class="col-md-6 mb-3" style="padding: 5px;">
                <!--Field to enter bank-->
                    <label for="bank" class="form-label">Bank:</label>
                    <input type="text" class="form-control" id="bank" name="bank" required>
                </div>
                <div class="col-md-6 mb-3" style="padding: 5px;">
                <!--Field to enter account number-->
                    <label for="accountNum" class="form-label">Account Number:</label>
                    <input type="text" class="form-control" id="accountNum" name="accountNum" required>
                </div>
                <div class="col-md-6 mb-3" style="padding: 5px;">
                <!--Field to enter expiry date-->
                    <label for="Expiry" class="form-label">Account Expiry:</label>
                    <input type="date" class="form-control" id="Expiry" name="Expiry" required>
                </div>
                <div class="col-md-6 mb-3" style="padding: 5px;">
                <!--Field to enter CVV number-->
                    <label for="cvv" class="form-label">Account CVV:</label>
                    <input type="password" class="form-control" id="cvv" name="cvv" required>
                </div>
                <div class="col-12 d-flex justify-content-center">
                    <!--Button to pay-->
                    <button type="submit" class="btn btn-danger"><i class="bi bi-lock"></i>  Payment</button>
                </div>
            </form>
        </div>
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