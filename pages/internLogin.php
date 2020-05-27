<?php

    /************************************************
     * 
     * Landingpage for the the admin to login
     * 
     ************************************************/

    // starts the session 
    session_start();
    //includes header file
    include("../includes/header.php");
    //includes user class
    include("../classes/user.php");
    //includes database class for database-connection and functions
    require_once("../classes/database.php");
    
    //creates new user object
    $user = new User();
    //saves currently serialized user to user object
    $user = unserialize($_SESSION['user']);
    //variable that gets information via parameter from URL
    $isadmin = $_GET['isadmin'];
    //check if there is an user and the user is an admin
    if($user!=false && $isadmin!=true ){
        //an admin user is already loggedin and redirects to admin landingpage
        header("Location: ../pages/adminStartpage.php", true, 301);
    }
    else{
        //if the user is no admin or there's noone loggedin. get data from form
        if(isset($_GET['login'])){
            $email = $_POST['uemail'];
            $password = $_POST['upassword'];
            //creates new database object
            $database = new Database();
            //calls database function to login an admin user
            $user = $database->adminLogin($email, $password);
            //check if there's an user found 
            if ($user) {
                // Saves the user class as an serealized value
                $_SESSION['user'] = serialize($user);
                //redirects loggedin admin user to the admin landingpage
                header("Location: ../pages/adminStartpage.php", true, 301);
            } else {
                // Dispays an error if the user is invalide 
                $errorMessage = "Please enter a valid Email or Password!";
            } 
        }
    }

    //check if there is an error-message
    if (isset($errorMessage)) { ?>
        <script>
            //opens popup-window with the error-message
            alert("<?php echo $errorMessage; ?>");
        </script>
    <?php } 
?>
    <!-- content -->
    <div class="container-login">
    <!-- creates for for the user to enter login data -->
    <form action="?login=1" method="post">
        <input type="text" id="uemail" name="uemail" placeholder="Your email.." required>
        <input type="password" id="upassword" name="upassword" placeholder="Your password.." required>
        <input type="submit" value="Login">
    </form>
    </div>


<?php 

    //includes footer file
    include("../includes/footer.php");

    /**
     * 
     * 
     * 
     * (c) LAP 28.5.2020
     * 
     * Judith Heinzl
     * 
     * 
     * 
     */

?>