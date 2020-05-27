<?php

    /************************************************
     * 
     * Page to edit the selected user
     * only for admins possible
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

    //gets currently selected user that should be edited via userid 
    $uid = $_GET["id"];
    //creates new database object
    $database = new Database();
    //calls database function that returns user data from the selected user
    $editUser = $database->getUserById($uid);
    //creates new user object
    $user = new User();
    //saves previously serialized user to new user object
    $user = unserialize($_SESSION['user']);
    //check if there's an user found
    if($user){
        //get all information from form
        if(isset($_GET['edit'])){
            //creates new user object
            $updatedUser = new User();
            $updatedUser->userid = $_POST['uid'];
            $updatedUser->email = $_POST['uemail'];
            $updatedUser->password = $_POST['upassword'];
            $updatedUser->firstname = $_POST['ufirstname'];
            $updatedUser->lastname = $_POST['ulastname'];
            $updatedUser->user_role = $_POST['uuser'];
            $oldpw = $_POST['upassword-c'];
            //calls database to update current selected user
            $user = $database->editUser($updatedUser,$oldpw);
            //check if successfull
            if($user){
                $message = "Successfully changed!";
            }else{
                $message = "Something went wrong!";
            }
        }
    }

    if (isset($message)) { ?>
        <script>
            //opens popup-window with the messages
            if (window.confirm('<?php echo $message?>')) {
                window.location.href='../pages/adminStartpage.php';
            };
        </script>
    <?php 
    }
?>
    <!-- content -->
    <!-- creates form with the user data already filled in that the admin want to edit -->
    <form action="?edit=1" method="post">
        <!-- fills in all the user data from the selected user -->
        <input type="hidden" id="uid" name="uid" value="<?php echo $editUser->userid;?>" >
        <input type="text" id="uemail" name="uemail" value="<?php echo $editUser->email;?>" required>
        <input type="text" id="ufirstname" name="ufirstname" value="<?php echo $editUser->firstname;?>" required>
        <input type="text" id="ulastname" name="ulastname" value="<?php echo $editUser->lastname;?>" required>
        <input type="hidden" id="upassword-c" name="upassword-c" value="<?php echo $editUser->password;?>">
        <input type="password" id="upassword" name="upassword" value="<?php echo $editUser->password;?>" required>
        <label for="uuser">Choose a user role:</label><br>
        <select id="uuser" name="uuser">
            <option value="1" <?php if($editUser->user_role == 1){ ?> selected <?php } ?> >Admin</option>
            <option value="2" <?php if($editUser->user_role == 2){ ?> selected <?php } ?> >Trainer</option>
            <option value="3" <?php if($editUser->user_role == 3){ ?> selected <?php } ?>>User</option>
        </select>
        <input type="submit" value="Edit">
    </form>
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