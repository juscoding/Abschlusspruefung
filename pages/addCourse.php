<?php

    /************************************************
     * 
     * page for the user to add a new course
     * 
     * note: not fully developed
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
    //saves previously serialized user to the new user object
    $user = unserialize($_SESSION['user']);
    var_dump($user->userid);
    //$getMyAvailableCourses = $database->getAvailableCourses($user->userid);
    //var_dump($getMyAvailableCourses);

    if(isset($_GET['add'])){


    }

?>

    <h1>add new course</h1>
    <div class="container-login">
        <form action="?add=1" method="post">
            <label for="ucourseid">Choose course</label><br>
            <select id="ucourseid" name="ucourseid">
                <option value="1">Admin</option>
                <option value="2">Trainer</option>
                <option value="3">User</option>
            </select>

            <input type="submit" value="Add">
        </form>
    </div>

<?php include("./includes/footer.php");?>