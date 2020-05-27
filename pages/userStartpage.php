<?php

    /************************************************
     * 
     * Landingpage for the loggedin standard user
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

    //set errors to flase
    $error = false;
    //creates new user object
    $user = new User();
    //saves the previos serialised user to the new user object
    $user = unserialize($_SESSION['user']);
    //creates new database object
    $database = new Database();
    //calls database function with parameter and excepts a return value
    $user_c = $database->getUserById($user->userid);
    //check if there's an user and if the user is not admin
    if($user_c && $user_c->user_role != 1){
        //calls database function and excepts a return of all the courses a user has
        $allMyCourses = $database->getAllMyCourses($user_c->userid);
        //calls database function and get all files to the courses the user has
        $filesArray = $database->getCourseFiles($user_c->userid);
    }

    function getfiles($fileid){
        echo "ok";
    }
?>
    <!-- content -->
    <h1>your courses</h1>
    <div class="container-user">
        <!-- creates table with course information -->
        <table style="width:100%">
            <tr style="text-align: left;">
                <th>Course Name</th>
                <th>Trainer</th>
            </tr>
            <?php 
                //loop that runs through the course array
                foreach ($allMyCourses as $course) { ?>
            <tr>
                <!-- output of the course array on the different columns -->
                <td><?php echo $course[name]; ?></td>
                <td><?php echo "$course[firstname] $course[lastname]";?><br><?php echo $course[email];?></td>
            </tr>
            <?php } ?>
            <tr>
                <!-- button to add a new course with parameter of the loggedin user -->
                <td colspan="3"><button type="button"><a href="../pages/addCourse.php?id=<?= $user_c->userid; ?>">Add another course</a></button></td>
            </tr>
        </table>
    </div>
    
    <h1>your course files</h1>
    <div class="container-user">
        <!-- creates table with course information -->
        <table style="width:100%">
            <tr style="text-align: left;">
                <th>Course Name</th>
                <th>File</th>
            </tr>
            <?php 
                //loop that runs through the course array
                foreach ($filesArray as $f) { ?>
            <tr>
                <!-- output of the course array on the different columns -->
                <td><?php echo $f[name]; ?></td>
                <td><a href="../documents/<?php echo $f[display_name]; ?>" target="_blank"><?php echo $f[display_name]; ?></a></td>
            </tr>
            <?php } ?>
        </table>
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