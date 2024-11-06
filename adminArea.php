<?php
// Establish a connection to your database
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "manitoudb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted for adding a new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['catName']) && isset($_POST['catDesc'])) {
    // Retrieve values from the form submission
    $catName = $_POST['catName'];
    $catDesc = $_POST['catDesc'];

    // Insert values into the database
    $sql = "INSERT INTO `product category` (catName, Description) VALUES ('$catName', '$catDesc')";
    if ($conn->query($sql) === TRUE) {
        echo "New category added successfully";
    } else {
        // Handle error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Check if the form is submitted for adding a new product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proName']) && isset($_POST['proPrice']) && isset($_POST['type']) && isset($_POST['stock']) && isset($_POST['category'])) {
    // Retrieve values from the form submission
    $productName = $_POST['proName'];
    $productPrice = $_POST['proPrice'];
    $productType = $_POST['type'];
    $inStock = ($_POST['stock'] == 'Yes') ? 1 : 0;
    $productCategory = $_POST['category'];

    // Handle image upload
    $targetDir = "./Images/";
    $targetFile = $targetDir . basename($_FILES["formFile"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["formFile"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        // handle error
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($targetFile)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["formFile"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["formFile"]["tmp_name"], $targetFile)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["formFile"]["name"])). " has been uploaded.";
            
            // Get just the filename
            $imageName = basename($_FILES["formFile"]["name"]);
            
            // Insert product data into the database, using $imageName instead of $targetFile
            $sql = "INSERT INTO `products` (Name, Type, Price, inStock, CategoryID, ImageName) 
                    VALUES ('$productName', '$productType', '$productPrice', '$inStock', 
                    (SELECT CategoryID FROM `product category` WHERE catName = '$productCategory'), '$imageName')";
            if ($conn->query($sql) === TRUE) {
                echo "New product added successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Check if the form is submitted for updating a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upName'])) {
    // Retrieve values from the form submission
    $productName = $_POST['upName'];

    // Check if the product name is provided
    if(empty($productName)) {
        echo "Product Name to update is required";
    } else {
        // Check if the product exists
        $checkSql = "SELECT * FROM `products` WHERE Name = '$productName'";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            // Product exists, proceed with the update
            $updateSql = "UPDATE `products` SET";

            // Update the product type if provided
            if(isset($_POST['upType']) && $_POST['upType'] != 'None') {
                $productType = $_POST['upType'];
                $updateSql .= " Type = '$productType',";
            }

            // Update the product price if provided
            if(isset($_POST['upPrice']) && is_numeric($_POST['upPrice'])) {
                $productPrice = $_POST['upPrice'];
                $updateSql .= " Price = '$productPrice',";
            }


            // Update the product in-stock status if provided
            if(isset($_POST['upStock']) && $_POST['upStock'] != 'None') {
                $inStock = ($_POST['upStock'] == 1) ? 1 : 0;
                $updateSql .= " inStock = '$inStock',";
            }

            // Update the product category if provided
            if(isset($_POST['upCategory']) && $_POST['upCategory'] != 'None') {
                $productCategory = $_POST['upCategory'];
                $updateSql .= " CategoryID = (SELECT CategoryID FROM `product category` WHERE catName = '$productCategory'),";
            }

            // Update the product description if provided
            if(isset($_POST['upDesc']) && !empty($_POST['upDesc'])) {
                $productDesc = $_POST['upDesc'];
                $updateSql .= " Description = '$productDesc',";
            }

            // Update the product visibility if provided
            if(isset($_POST['visible']) && $_POST['visible'] != 'None') {
                $visible = $_POST['visible'];
                echo $visible;
                $updateSql .= " Visibility = '$visible',";
            }

            // Remove the trailing comma from the update SQL statement
            $updateSql = rtrim($updateSql, ",");

            // Add the WHERE clause
            $updateSql .= " WHERE Name = '$productName'";

            // Execute the update query
            if ($conn->query($updateSql) === TRUE) {
                echo "Product updated successfully";
            } else {
                echo "Error updating product: " . $conn->error;
            }
        } else {
            echo "Product with name '$productName' does not exist";
        }
    }
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
    <link rel="stylesheet" href="admin.css" media="all" />
    <!--Title of webpage-->
    <title>Admin Area</title>

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

<!--Admin area-->
<div class="container-fluid section1">
<div class="container inside">
    <div class="row paddings"></div>
        <div class="row newProHead">
            <!--Add product section-->
            <h2><u>Add a new product</u></h2>
        </div>
        <form action="" class="row g-3" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <!--Upload product picture-->
                <label for="formFile" class="form-label">Upload Product Image</label>
                <input class="form-control" type="file" id="formFile" name="formFile">
            </div>
            <div class="col-md-6 mb-3">
                <!--Product name-->
                <label for="proName" class="form-label">Product Name:</label>
                <input type="text" class="form-control" id="proName" name="proName" placeholder="Name">
            </div>
            <div class="col-md-6 mb-3">
                <!--Products price-->
                <label for="proPrice" class="form-label">Product Price:</label>
                <input type="text" class="form-control" id="proPrice" name="proPrice" placeholder="Price">
            </div>
            <div class="col-md-6 mb-3">
                <!--Products type: Machine, Parts, or Merchandise-->
                <label for="type" class="form-label">Type of product:</label>
                <select class="form-select" aria-label="Default select" id="type" name="type">
                    <option selected value="Machine">Machine</option>
                    <option value="Parts">Parts</option>
                    <option value="merchandise">Merchandise</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <!--Product stock avaliability-->
                <label for="stock" class="form-label">In stock:</label>
                <select class="form-select" aria-label="Default select" id="stock" name="stock">
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <!--Products category-->
                <label for="category" class="form-label">Product category:</label>
                <select class="form-select" aria-label="Default select" id="category" name="category">
                    <?php
                    // Retrieve category names from the database
                    $sql = "SELECT catName FROM `product category`";
                    $result = $conn->query($sql);

                    // Check if any categories are found
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<option>" . $row["catName"] . "</option>";
                        }
                    } else {
                        echo "<option>No categories found</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <!--products description-->
                <label for="desc" class="form-label">Product Description:</label>
                <input type="textarea" class="form-control" id="desc" name="desc" placeholder="description">
            </div>
            <div class="col mb-3 d-flex justify-content-center">
                <!--add product button-->
                <button class="btn btn-danger" type="submit">Add product</button>
            </div>
        </form>
        <div class="row newProHead">
            <!--New category section-->
            <h2><u>Add new category</u></h2>
        </div>
        <form action="" method="post" class="row g-3">
            <div class="col-md-6 mb-3">
                <!--Category name to enter-->
                <label for="catName" class="form-label">Category Name:</label>
                <input type="text" class="form-control" id="catName" name="catName" placeholder="Name">
            </div>
            <div class="col-md-6 mb-3">
                <!--The categories description-->
                <label for="catDesc" class="form-label">Category Description:</label>
                <input type="text" class="form-control" id="catDesc" name="catDesc" placeholder="Description">
            </div>
            <div class="col mb-3 d-flex justify-content-center">
                <!--Button to add category-->
                <button class="btn btn-danger" type="submit">Add category</button>
            </div>
        </form>
        <div class="row newProHead">
            <!--Update product section-->
            <h2><u>Update product</u></h2>
        </div>
        <form action="" method="post" class="row g-3">
            <div class="col-md-6 mb-3">
                <!--Product name to update, required-->
                    <label for="upName" class="form-label">Product Name to update:</label>
                    <input type="text" class="form-control" id="upName" name="upName" placeholder="Name">
                    <!-- Error message for missing product name -->
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upName']) && empty(trim($_POST['upName']))) { ?>
                        <div class="invalid-feedback">
                            Product Name is required.
                        </div>
                    <?php } ?>
                    <!-- Error message for non-existent product name -->
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upName']) && !empty(trim($_POST['upName'])) && $result->num_rows == 0) { ?>
                        <div class="invalid-feedback">
                            Product with the provided name does not exist.
                        </div>
                    <?php } ?>
            </div>
            <div class="col-md-6 mb-3">
                <!--To update product price-->
                    <label for="upPrice" class="form-label">Update Product Price:</label>
                    <input type="text" class="form-control" id="upPrice" name="upPrice" placeholder="Price">
            </div>
            <div class="col-md-6 mb-3">
                <!--To update products type-->
                <label for="upType" class="form-label">Update product type:</label>
                <select class="form-select" aria-label="Default select" id="upType" name="upType">
                    <option selected>None</option>
                    <option value="Machine">Machine</option>
                    <option value="Parts">Parts</option>
                    <option value="merchandise">Merchandise</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <!--To update products stock avaliability-->
                <label for="upStock" class="form-label">Update in-stock:</label>
                <select class="form-select" aria-label="Default select" id="upStock" name="upStock">
                    <option selected>None</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <!--Update product category-->
                <label for="upCategory" class="form-label">Update product category:</label>
                <select class="form-select" aria-label="Default select" id="upCategory" name="upCategory">
                    <option selected>None</option>
                    <?php
                    // Retrieve category names from the database
                    $sql = "SELECT catName FROM `product category`";
                    $result = $conn->query($sql);

                    // Check if any categories are found
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<option value=" . $row["catName"] . ">" . $row["catName"] . "</option>";
                        }
                    } else {
                        echo "<option>No categories found</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <!--Change products description-->
                <label for="upDesc" class="form-label">Update product description:</label>
                <input type="textarea" class="form-control" id="upDesc" name="upDesc" placeholder="description">
            </div>
            <div class="col-md-6 mb-3">
                <!--Deletes product from the website-->
                <label for="visible" class="form-label">Stock visibility (Yes-visible, no-not visible):</label>
                <select class="form-select" aria-label="Default select" id="visible" name="visible">
                    <option selected>None</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
            <div class="col-md-6 mb-3"></div>
            <div class="col mb-3 d-flex justify-content-center">
                <!--Button to finalize changes-->
                <button class="btn btn-danger" type="submit">Update Product</button>
            </div>
        </form>
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