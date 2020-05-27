<?php

    /************************************************
     * 
     * Page to edit the selected course
     * only for admins possible
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
    
    function fileRemove_database($fid){
        //$check = $database->deleteFile($fid); 
        return $fid;
    }

    if($checkforadmin_user && $checkforadmin_user->user_role == 1){
        //if there's a user loggedin and an admin
        //calls database function to get all existing trainer
        $trainerArray = $database->getAllTrainer();
        //gets course id from URL
        $courseid = $_GET['courseid'];
        //calls database function to get all data from selected courseid
        $courseArray = $database->getCurrentCourse($courseid);
        //calls database function to get all participants from courseid
        $participantArray = $database->getAllParticipant($courseid);
        //calls database function to get all files from courseid
        $filesArray = $database->getAllFiles($courseid); 
        if(isset($_GET['editCourse'])){ 
            header("Location: ../pages/adminStartpage.php");
        }

    }
?>

    <!-- content -->
    <!-- creates form with the user data already filled in that the admin want to edit -->
    <h1>edit course</h1>
    <form action="?editCourse=1" method="post">
        <?php foreach($courseArray as $course){ ?>
            <lable>Course ID:</lable><br>
            <input class="cparticipant notEdible" type="text" id="cid" name="cid" value="<?php echo $course[course_id]?>" readonly><br>
            <lable>Course Name: </lable><br>
            <input class="cparticipant" type="text" id="cname" name="cname" value="<?php echo $course[name]?>" required><br>
            <lable>Current Trainer:</lable><br>
            <p class="cparticipant notEdible"><?php echo "$course[firstname] $course[lastname] - $course[email]"?></p>
        <?php } ?><br>
        <label for="ctrainer">select a new trainer or the old one:</label><br>
        <select class="ctrainer" id="ctrainer" name="ctrainer">
            <?php 
                //loop runs through the array of all trainer
                foreach($trainerArray as $trainer){ ?>
                <option value="<?php $trainer[user_id];?>"><?php echo "$trainer[firstname] $trainer[lastname] - $trainer[email]" ?></option>
                <?php } ?>
        </select><br>
        <label for="cparticipant">Participants:</label><br>
        <?php 
            //loop that runs through all participants 
            foreach($participantArray as $p){ 
        ?>
            <input class="cparticipant" type="text" id="cparticipant" name="cparticipant" value="<?php echo "$p[firstname] $p[lastname] -  $p[email]";?>" readonly>
            <button class="button_remove" id="removePbtn" type="button" value="<?php echo $p[user_id];?>" onclick="participantRemove()">remove</button>
            <br>
        <?php } ?>
        <label>Files:</label><br>
        <?php 
            //loop that runs through all files 
            foreach($filesArray as $f){ 
        ?>
            <p class="cparticipant"><a href="../documents/<?php echo $f[display_name]; ?>" target="_blank"><?php echo $f[display_name]; ?></a></p>
            <button class="button_remove" id="removeFbtn" type="button" value="<?php echo $f[document_id];?>" onclick="fileRemove()">remove</button>
            <br>
        <?php } ?>
        <input type="submit" value="Edit">
    </form>

    <script>
        function fileRemove() {
            var input = document.getElementById('removeFbtn').value;
            //var restult = <?php fileRemove_database(id) ?>;
            alert(input);
        }

        function participantRemove() {
            var input = document.getElementById('removePbtn').value;
            //var restult = <?php fileRemove_database(id) ?>;
            alert(input);
        }
    </script>
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