<?php
// Create a connection to the database
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "manitoudb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories from the database where associated products have Type 'merchandise'
$categories_sql = "SELECT DISTINCT pc.* FROM `Product category` pc
                    INNER JOIN `Products` p ON pc.CategoryID = p.CategoryID
                    WHERE p.Type = 'merchandise' AND p.Visibility = 'Yes'";
$categories_result = $conn->query($categories_sql);

// Initialize the category filter
$category_filter = "";

// Check if a category is selected
if(isset($_GET['category']) && $_GET['category'] != '0') {
    $category_filter = "AND p.CategoryID = ".$_GET['category'];
}

// Initialize the inStock filter
$in_stock_filter = "";

// Check if the "In stock" checkbox is checked
if(isset($_GET['inStock']) && $_GET['inStock'] == '1') {
    $in_stock_filter = "AND p.inStock = 1";
}

// Fetch merchandise from the database with the selected category and inStock filters
$sql = "SELECT * FROM `products` p WHERE p.Type = 'merchandise' AND p.Visibility = 'Yes' ".$category_filter." ".$in_stock_filter;
$result = $conn->query($sql);

// Initialize the search filter
$search_filter = "";

// Check if a search query is provided
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    // Escape special characters to prevent SQL injection
    $search_query = $conn->real_escape_string($search_query);
    // Construct the search filter
    $search_filter = "AND p.Name LIKE '%$search_query%'";
}

// Fetch machines from the database with the selected category, inStock, and search filters
$sql = "SELECT * FROM `products` p WHERE p.Type = 'merchandise' AND p.Visibility = 'Yes' ".$category_filter." ".$in_stock_filter." ".$search_filter;
$result = $conn->query($sql);
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
    <link rel="stylesheet" href="machineshop.css" media="all"/>
    <!--Title of page-->
    <title>Manitou Merchandise</title>

    <script src="CheckScript.js"></script>

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
                    session_start();
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

<!--TopSection: naviagation for shopping area-->
<div class="container-fluid TopSection">
    <div class="row filter">
        <div class="col-5 col-md-2">
            <div class="form-floating">
                <!--Allows users to look at products in certain categories-->
                <select class="form-select" id="floatingSelect" aria-label="Floating label select"
                        onchange="location = this.value;">
                    <option value="merch.php?category=0" <?php if(isset($_GET['category']) && $_GET['category'] == '0') echo 'selected';?>>None</option>
                    <?php
                    if ($categories_result->num_rows > 0) {
                        while ($category = $categories_result->fetch_assoc()) {
                            echo '<option value="merch.php?category='.$category['CategoryID'].'" '.(isset($_GET['category']) && $_GET['category'] == $category['CategoryID'] ? 'selected':'').'>' . $category['catName'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <label for="floatingSelect">Category:</label>
            </div>
        </div>
        <div class="col-md-2 d-none d-md-block">
            <div class="form-check">
                <!--Allows users to see products in stock-->
                <input class="form-check-input" type="checkbox" value="1" id="flexCheckCheckedLarge" <?php if(isset($_GET['inStock']) && $_GET['inStock'] == '1') echo 'checked';?> onchange="location.href = 'merch.php?category=<?php echo isset($_GET['category']) ? $_GET['category'] : '0'; ?>&inStock=' + (this.checked ? '1' : '0');">
                <label class="form-check-label" for="flexCheckCheckedLarge">
                    In stock
                </label>
            </div>
        </div>
        <div class="col">
        </div>
        <div class="col-5 col-md-2">
            <!--Allows users to search for products-->
            <form class="form-floating d-flex" method="GET">
                <input type="text" class="form-control" id="floatingInputValue" placeholder="Search" name="search">
                <label for="floatingInputValue">Search</label>
                <button type="submit" class="btn btn-primary search-button">Search</button>
            </form>
        </div>
    </div>
    <div class="row d-md-none">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="flexCheckCheckedSmall" <?php if(isset($_GET['inStock']) && $_GET['inStock'] == '1') echo 'checked';?> onchange="location.href = 'merch.php?category=<?php echo isset($_GET['category']) ? $_GET['category'] : '0'; ?>&inStock=' + (this.checked ? '1' : '0');">
            <label class="form-check-label" for="flexCheckCheckedSmall">
                In stock
            </label>
        </div>
    </div>
</div>


<!--Products-->
<div class="container-fluid">
    <div class="row">
        <?php
        // Check if any merchandise items are found
        if ($result->num_rows > 0) {
            // Output data of each merchandise item
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4">
                    <div class="card">
                        <!--Cards to show products information-->
                        <img src="./Images/<?php echo $row['ImageName']; ?>" class="card-img-top imagess" alt="Product Image">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-center"><?php echo $row['Name']; ?></h5>
                            <p class="card-text text-center"> R <?php echo $row['Price']; ?></p>
                            <div class="mt-auto">
                                <?php if(isset($_SESSION['CustomerID'])) { ?>
                                    <form method="post" action="Cart.php">
                                        <input type="hidden" name="ProductID" value="<?php echo $row['ProductID']; ?>">
                                        <input type="hidden" name="CustomerID" value="<?php echo $_SESSION['CustomerID']; ?>">
                                        <button type="submit" class="btn btn-danger float-end">Add to Cart</button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "No merchandise found";
        }
        ?>
    </div>
</div>

<!--Section 4: footer-->
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
                        <a href="https://www.instagram.com/manitou_worldwide/"><img src="./Images/insta.PNG"
                                                                                        alt="instagram"></a>
                    </td>
                    <td>
                        <a href="https://www.linkedin.com/company/manitou-mea/posts/?feedView=all"><img
                                    src="Linkedin.PNG" alt="LinkedIn"></a>
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