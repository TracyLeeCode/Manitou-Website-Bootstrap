<?php
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

// Initialize error variables
$emailError = "";
$numberError = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    // Escape user inputs for security
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $email = $conn->real_escape_string($_POST['email']);
    $number = $conn->real_escape_string($_POST['number']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);
    $password = $conn->real_escape_string($_POST['password']);

    // Validate inputs
    if (empty($fname) || empty($lname) || empty($email) || empty($number) || empty($birthdate) || empty($password)) {
        die($fieldError = "All fields are required.");
    }
    
    // Check if email already exists
    $emailQuery = "SELECT * FROM customer WHERE Email = '$email'";
    $emailResult = $conn->query($emailQuery);
    if ($emailResult->num_rows > 0) {
        $emailError = "Email already exists. Please use a different email.";
    }
    
    // Check if phone number already exists
    $numberQuery = "SELECT * FROM customer WHERE PhoneNumber = '$number'";
    $numberResult = $conn->query($numberQuery);
    if ($numberResult->num_rows > 0) {
        $numberError = "Phone number already exists. Please use a different phone number.";
    }
    
    // If email or phone number already exists, display error messages
    if (!empty($emailError) || !empty($numberError)) {
        // Display error messages in red
        $emailError = '<span class="error">' . $emailError . '</span>';
        $numberError = '<span class="error">' . $numberError . '</span>';
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // SQL query to insert data into the table
        $sql = "INSERT INTO customer (name, surname, Email, PhoneNumber, Birthdate, Password)
                VALUES ('$fname', '$lname', '$email', '$number', '$birthdate', '$password_hash')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to login.php after successful signup
            header("Location: login.php");
            exit(); // Ensure no further code execution after redirection
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close connection
$conn->close();
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
    <link rel="stylesheet" href="registers.css" media="all" />
    <!--Title of page-->
    <title>Register</title>

  </head>
  <body>
    
    <!--Navigation-->
    <nav class="navbar navbar-expand-md navbar-light" id="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="./Images/Main.php" class="navbar-brand">
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

<!--Register form-->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 forms-container">
            <form action="register.php" method="post" id="registerForm" class="forms">
                <fieldset style="border: 1px solid black;">
                    <legend>Register Now!</legend>
                    <div class="mb-3">
                        <!--Field to enter name-->
                        <label for="fname" class="form-label">First name:</label>
                        <input type="text" class="form-control" id="fname" name="fname" required>
                    </div>
                    <div class="mb-3">
                        <!--Field to enter last name-->
                        <label for="lname" class="form-label">Surname:</label>
                        <input type="text" class="form-control" id="lname" name="lname" required>
                    </div>
                    <div class="mb-3">
                        <!--Field to enter email-->
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <!--Handles email already existing-->
                        <?php echo $emailError; ?>
                    </div>
                    <div class="mb-3">
                        <!--Field to enter phone number-->
                        <label for="number" class="form-label">Phone Number:</label>
                        <input type="number" class="form-control" id="number" name="number" required>
                        <!--Handles phone number already existing-->
                        <?php echo $numberError; ?>
                    </div>
                    <div class="mb-3">
                        <!--Field to enter birthdate-->
                        <label for="birthdate" class="form-label">Birth date:</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                    </div>
                    <div class="mb-3">
                        <!--Field to enter password-->
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <!--Button to sign up and adds information to the database-->
                    <button type="submit" class="btn btn-danger">Sign Up</button>
                </fieldset>
            </form>
        </div>
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