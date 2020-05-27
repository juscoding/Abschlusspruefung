<?php

    /************************************************
     * 
     * Landingpage for the the standard user to login
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
    //saves the previos serialised user to the new user object
    $user = unserialize($_SESSION['user']);
    //creates new database object
    $database = new Database();
    //get current user information
    $checkforadmin_user = $database->getUserById($user->userid);
    //verifies if user is already loggedin and not an admin
    if($checkforadmin_user && $checkforadmin_user->user_role != 1){
        //if user loggedin and not admin go to user landingpage
        header("Location: ../pages/userStartpage.php", true, 301);
    }
    else{
        //if no user is loggedin 
        if(isset($_GET['login'])){
            //get the information the user typed into the form
            $email = $_POST['uemail'];
            $password = $_POST['upassword'];
            //calls database function to login an user with email and password
            $user = $database->userLogin($email, $password);
            //check if user is set and correct
            if ($user && $user !== false ) {
                // Saves the user class as an serealized value
                $_SESSION['user'] = serialize($user);
                //redirects user to the user landingpage 
                header("Location: ../pages/userStartpage.php", true, 301);
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
        <!-- creates form for the user to enter login data -->
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