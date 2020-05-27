<?php

    /************************************************
     * 
     * Landingpage for an loggedin admin 
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

    //sets error to false
    $error = false;
    //creates new user object
    $user = new User();
    //saves previously serialized user to the new user object
    $user = unserialize($_SESSION['user']);
    //creates database object
    $database = new Database();
    //calls database function to double-check if the user is an admin
    $checkforadmin_user = $database->getUserById($user->userid);

    if($checkforadmin_user && $checkforadmin_user->user_role == 1){
        //if there's a user loggedin and an admin
        //database function that returns all existing user
        $allUser = $database->getAllUser();
        //database function that returns all existing courses
        $allCourses = $database->getAllCourses();
        //get all data from form
        if(isset($_GET['register'])){
            //creates new user object and saves data from form to the new user object
            $newUser = new User();
            $newUser->email = $_POST['uemail'];
            $newUser->firstname = $_POST['ufirstname'];
            $newUser->lastname = $_POST['ulastname'];
            $newUser->user_role = $_POST['uuser'];
            $passwordc = $_POST['upassword-c'];
            $newUser->password = $_POST['upassword'];
            //check if the entered email is valid
            if (!filter_var($newUser->email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = 'Enter valid email address <br>';
                $error = true;
            }
            //check if there's a password entered and the password and the confirmed password are equal
            if (strlen($newUser->password) == 0 or  $newUser->password != $passwordc) {
                $errorMessage = 'Enter a valid password<br>';
                $error = true;
            }
            //check if there're some errors
            if (!$error) {
                //calls database function that returns all user data with the used email
                $databaseUser = $database->getUser($newUser->email);
                //checks if the entered email is already used
                if ($databaseUser->email) {
                    $errorMessage = 'Email already used!<br>';
                    $error = true;
                }
            }
            //check if there're some errors
            if (!$error) {
                //calls database function that creates a new user with the data the admin entered in the form
                $result = $database->createUser($newUser);
                //checks if the insert statement worked
                if ($result) {
                    $message = 'New user is created!';
                } else {
                    $errorMessage = 'Ups - something went wrong!';
                }
            }
        }
    }
    else{
        $errorMessageNoAdmin = "Your're not an admin!";
        $error = true;
    }

    //check for errors
    if (isset($errorMessage)) { ?>
        <script>
            //opens popup-window with the error-message
            alert("<?php echo $errorMessage; ?>");
        </script>
    <?php }
    //check for other messages
    if (isset($message)) { ?>
        <script>
            //opens popup-window with the messages
            if (window.confirm('<?php echo $message?>')) 
            {
                //if the popup-window is confirmed with ok -> redirect to the admin landingpage
                window.location.href='../pages/adminStartpage.php';
            };
        
        </script>
    <?php 
    }
    //if the user is no admin, he shouldn't see any information only the admin can see
    if (!isset($errorMessageNoAdmin)) { 
    
?>
    <!-- content only the admin can see -->

        <!-- List of all user -->
        <details>
            <summary>all User</summary>
            <div class="container-user">
                <table style="width:100%">
                    <tr style="text-align: left;">
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>EMail</th>
                        <th>User Role</th>
                        <th>Bearbeiten</th>
                    </tr>
                    <?php 
                        //loop that runs through the user array
                        foreach ($allUser as $sUser) { ?>
                    <tr>
                        <!-- output of the user array on the different columns -->
                        <td><?php echo $sUser[firstname]; ?></td>
                        <td><?php echo $sUser[lastname]; ?></td>
                        <td><?php echo $sUser[email]; ?></td>
                        <td>
                            <?php 
                                //gives the user_role but in text form
                                if($sUser[user_role] == 1){ 
                                    echo "Admin";
                                }
                                if($sUser[user_role] == 2){ 
                                    echo "Trainer";
                                }
                                if($sUser[user_role] == 3){ 
                                    echo "User";
                                }  
                            ?>
                        </td>
                        <!-- redirects to the edit page with the parameter userid of the selected user -->
                        <td><button type="button"><a href="../pages/editUser.php?id=<?= $sUser[user_id]; ?>">Edit</a></button></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </details>

        <!-- List of all courses -->
        <details>
            <summary>all Courses</summary>
            <div class="container-user">
                <table style="width:100%">
                    <tr style="text-align: left;">
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Trainer</th>
                        <th>Edit</th>
                    </tr>
                    <?php 
                        //loop that runs through the course array
                        foreach ($allCourses as $course) { 
                    ?>
                    <tr>
                        <!-- output of the user array on the different columns -->
                        <td><?php echo $course[course_id]; ?></td>
                        <td><?php echo $course[name]; ?></td>
                        <td><?php echo "$course[firstname] $course[lastname]";?><br><?php echo $course[email];?></td>
                        <!-- redirects to the edit page with the parameter userid of the selected course -->
                        <td><button type="button"><a href="../pages/editCourse.php?courseid=<?= $course[course_id]; ?>">Edit</a></button></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </details>

        <!-- create new user -->
        <details>
            <summary>register new User</summary>
            <div class="container-login">
            <!-- creates form to enter the data for the new user-->
            <form action="?register=1" method="post">
                <input type="text" id="uemail" name="uemail" placeholder="Your email.." required>
                <input type="text" id="ufirstname" name="ufirstname" placeholder="Your first name.." required>
                <input type="text" id="ulastname" name="ulastname" placeholder="Your last name.." required>
                <input type="password" id="upassword" name="upassword" placeholder="Your password.." required>
                <input type="password" id="upassword-c" name="upassword-c" placeholder="Confirm your password.." required>
                <label for="uuser">Choose a user role:</label><br>
                <select id="uuser" name="uuser">
                    <option value="1">Admin</option>
                    <option value="2">Trainer</option>
                    <option value="3">User</option>
                </select>

                <input type="submit" value="Create">
            </form>
            </div>
        </details>
    <?php }else { ?>
        <!-- if the loggedin user is no admin -> no information are displayed -->
        <h1>You're not an admin!</h1>
        <!-- button that redirects the user to the admin login page -->
        <button type="button"><a href="../pages/internLogin.php?isadmin=false">go to Admin Login</a></button> 
    <?php } ?>

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