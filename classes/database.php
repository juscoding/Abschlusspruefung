<?php

    require_once ('../classes/user.php');
    require_once ('../classes/course.php');
    require_once ('../classes/document.php');
    require_once("../includes/password.php");

    class Database {

        private $mysqli;

        public function __construct() {

            $this->mysqli = new mysqli('localhost', 'root', 'root', 'LAP');
            if ($this->mysqli->connect_errno) {
                echo "Failed to connect to MySQL: " . $this->mysqli->connect_error;
            }
        }

        public function getDatabase() {
            return $this->mysqli;
        }


        /**
         * Standard User Login
         *
         * @param string $email email that is used to login
         * @param string $password password that is used to login
         *
         * @return string|false found user = login, or false.
         */
        public function userLogin($email, $password){
            //get user from database for comparison
            $user = $this->getUser($email);
            if ($user) {
                //if there's an user found with the email that is entered
                if (password_verify($password, $user->password)) {
                    //hashed password from database and password from login form are the same
                    return $user;
                } else {
                    //passwords do not match
                    return false;
                }
            } else {
                //no user found with that email
                return false;
            }
        }


        /**
         * Search for an user by email
         *
         * @param string $email search with email for an user
         *
         * @return string|false found user, or false.
         */
        public function getUser($email){
            //database statement
            $statement = "SELECT * FROM t_user WHERE email = '$email';";
            //executes the query
            $result = mysqli_query($this->mysqli, $statement);
            //creates an associative array from the result
            $sqlResult = mysqli_fetch_array($result, MYSQLI_ASSOC);

            //checks if there are more than 0 results in array
            if (count($sqlResult) > 0) {
                //creates new User
                $user = new User();
                $user->userid = $sqlResult['user_id'];
                $user->email = $sqlResult['email'];
                $user->password = $sqlResult['password'];
                $user->firstname = $sqlResult['firstname'];
                $user->lastname = $sqlResult['lastname'];
                $user->user_role = $sqlResult['user_role'];
            }
            //returns the found user (or not if there's none)
            return $user;
        }


        /**
         * Login for Admin and Trainer
         *
         * @param string $email email that is used to login
         * @param string $password password that is used to login
         *
         * @return string|false found user = login, or false.
         */
        public function adminLogin($email, $password){
            $user = $this->getAdminUser($email);
            if ($user) {
                if (password_verify($password, $user->password)) {
                    return $user;
                } else {
                    return false;
                }
            } else {
                return false;
            }
                
        }


        /**
         * Search for Admin or Trainer with email
         * yes, that would also be possible with getUser(param) but I just missed it
         *
         * @param string $email search with email for an user
         *
         * @return string|false found user, or false.
         */
        public function getAdminUser($email){
            //database statement
            $statement = "SELECT * FROM t_user WHERE email = '$email' and user_role in (1,2);";
            //executes the query
            $result = mysqli_query($this->mysqli, $statement);
            //creates an associative array from the result
            $sqlResult = mysqli_fetch_array($result, MYSQLI_ASSOC);

            //checks if there are more than 0 results in array
            if (count($sqlResult) > 0) {
                //creates new User
                $user = new User();
                $user->userid = $sqlResult['user_id'];
                $user->email = $sqlResult['email'];
                $user->password = $sqlResult['password'];
                $user->firstname = $sqlResult['firstname'];
                $user->lastname = $sqlResult['lastname'];
            }
            //returns the found user (or not if there's none)
            return $user;
        }


        /**
         * Create new User
         *
         * @param array $user. Data from adminStartpage.php form and an user-class object
         *
         * @return boolean false = if something went wrong and there's no new user created
         */
        public function createUser($user) {
            //creates a hashed version of the password
            $hashedPassword = password_hash($user->password, PASSWORD_DEFAULT);
            //database insert statement
            $statement = "INSERT INTO `t_user` (`email`, `password`, `firstname`,`lastname`,`user_role`) 
            VALUES ('$user->email','$hashedPassword','$user->firstname','$user->lastname', '$user->user_role')";
            //executes the query and returns the result at the same time
            return $this->mysqli->query($statement);
        }


        /**
         * Shows an Admin all User
         * 
         * @return array $results_array. array that holds alle found user
         */
        public function getAllUser() {
            //database select all statement
            $statement = "SELECT * FROM t_user";
            //creates an empty array
            $results_array = array();
            //executes the query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches a row as an associative array
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //return all found user
            return $results_array;
        }


        /**
         * Search for an user with the userid
         *
         * @param int $id the identifier for the user
         *
         * @return string|false false = if no user fund
         */
        public function getUserById($id){
            //database select all statement
            $statement = "SELECT * FROM t_user WHERE user_id = '$id';";
            //executes the statement
            $result = mysqli_query($this->mysqli, $statement);
            //turns result into an assosiative array
            $sqlResult = mysqli_fetch_array($result, MYSQLI_ASSOC);
            //if there are more than 0 results in array
            if (count($sqlResult) > 0) {
                //creates new User object
                $user = new User();
                $user->userid = $sqlResult['user_id'];
                $user->email = $sqlResult['email'];
                $user->password = $sqlResult['password'];
                $user->firstname = $sqlResult['firstname'];
                $user->lastname = $sqlResult['lastname'];
                $user->user_role = $sqlResult['user_role'];
            }
            //returns found user or false
            return $user;
            
        }


        /**
         * Editing the user in the DB via Update
         *
         * @param array $user the edited user-information
         * @param string $oldpw the old password the user used
         *
         * @return boolean false = something went wrong and user couldn't be updated
         */
        public function editUser($user, $oldpw){
            //check if the password is changed
            if($user->password != $oldpw){
                //if the password is changed, the new password has to be hashed
                $newpw = password_hash($user->password, PASSWORD_DEFAULT);
                //statement for updating an user incl. the password
                $statement = "UPDATE t_user SET email='$user->email', password='$newpw', firstname='$user->firstname', lastname='$user->lastname', user_role='$user->user_role' WHERE user_id = '$user->userid';";
            }
            else{
                //if the password is the same. prepares statement without changing the password
                $statement = "UPDATE t_user SET email='$user->email', firstname='$user->firstname', lastname='$user->lastname', user_role='$user->user_role' WHERE user_id = '$user->userid';";
            }
            //executes the query and returns the result
            return mysqli_query($this->mysqli, $statement);
        }


        /**
         * List of all Courses. Only an Admin can see this
         *
         * @return array $results_array false = something went wrong and or no user found. 
         */
        public function getAllCourses() {
            //database statement where all information from table t_course, t_document, and trainer information 
            $statement = "SELECT t_course.*, t_user.firstname, t_user.lastname, t_user.email FROM t_course
            LEFT JOIN t_user ON t_user.user_id = t_course.trainer_id";
            //creates new array
            $results_array = array();
            //executes database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches a row as an associative array
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all courses
            return $results_array;
        }


        /**
         * Get all of the courses the currently loggedin user has
         *
         * @param int $userid the indentifier for the searched courses
         *
         * @return boolean false = something went wrong and user couldn't be updated
         */
        public function getAllMyCourses($userid){
            //database statement that shows the course-name and id, but also trainer information
            $statement = "SELECT t_course.name, t_course.course_id, t_user.firstname, t_user.lastname, t_user.email FROM t_course_p
                            INNER JOIN t_course ON t_course_p.course_id = t_course.course_id
                            LEFT JOIN t_user on t_user.user_id = t_course.trainer_id
                            WHERE t_course_p.user_id = $userid ";
            //creates new array
            $results_array = array();
            //executes database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches a row as an associative array
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all courses that the loggedin user has
            return $results_array;

        }


        /**
         * Get all of the files for every course the currently loggedin user has
         *
         * @param aray $allMyCourses the information about the courses the user has
         *
         * @return array multi-dimensional array with alle the files for every course
         */
        public function getCourseFiles($userid){
            //prepares sql statement
            $statement = "SELECT * FROM t_document
            LEFT JOIN t_course on t_course.course_id = t_document.course_id
            WHERE t_document.course_id IN (
            SELECT t_course.course_id FROM t_course
            LEFT JOIN t_course_p ON t_course_p.course_id = t_course.course_id
            WHERE t_course_p.user_id = $userid
            )";
            //creates new array
            $results_array = array();
            //executes database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches a row as an associative array
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all courses that the loggedin user has
            return $results_array;
        }


        /**
         * Get all of the courses the currently loggedin user can still book (a course can only be booked 1x)
         *
         * @param int $userid currently loggedin user
         *
         * @return array courses that are still available ore false, if there're no left
         */
        public function getAvailableCourses($userid){
            var_dump($userid);
            //database statement that shows all courses that the user has not already 
            $statement = "SELECT * FROM t_course
            WHERE t_course.course_id NOT IN (
                SELECT t_course.course_id FROM t_course
                LEFT JOIN t_course_p ON t_course_p.course_id = t_course.course_id
                WHERE t_course_p.user_id = $userid
            )";
            //creates an array
            $results_array = array();
            //executes database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches result into an associatove array row after row
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all rows with entries
            return $results_array;

        }


        /**
         * Get all existing trainer
         *
         * @return array $results_array all trainer
         */
        public function getAllTrainer(){
            //database statement selects all existing trainer
            $statement = "SELECT t_user.user_id, t_user.firstname, t_user.lastname, t_user.email FROM `t_course`
            LEFT JOIN t_user on t_user.user_id = t_course.trainer_id";
            //creates an array
            $results_array = array();
            //executes database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches result into an associatove array row after row
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all rows with entries
            return $results_array;
        }


        /**
         * Get selected course by courseid
         *
         * @param int $courseid search for course with the id
         * 
         * @return array $results_array all trainer
         */
        public function getCurrentCourse($courseid){
            //database statement selects all existing trainer
            $statement = "SELECT t_course.course_id, t_course.name, t_user.email, t_user.firstname, t_user.lastname FROM `t_course`
            LEFT JOIN t_user on t_user.user_id = t_course.trainer_id
            WHERE t_course.course_id = $courseid";
            //creates an array
            $results_array = array();
            //executes database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches result into an associatove array row after row
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all rows with entries
            return $results_array;
        }


        /**
         * Get selected course by courseid
         *
         * @param int $courseid search for participants with the id
         * 
         * @return array $results_array all trainer
         */
        public function getAllParticipant($courseid){
            //database statement selects all existing trainer
            //select userinfo from every user that has the courseid from @param
            $statement = "SELECT t_user.user_id, t_user.firstname, t_user.lastname, t_user.email FROM t_user 
            WHERE t_user.user_id IN (
                SELECT t_course_p.user_id FROM t_course_p
                WHERE t_course_p.course_id IN (
                    SELECT t_course.course_id FROM `t_course`
                    LEFT JOIN t_user on t_user.user_id = t_course.trainer_id
                    WHERE t_course.course_id = $courseid
                )
            )";
            //creates an array
            $results_array = array();
            //executes database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches result into an associatove array row after row
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all rows with entries
            return $results_array;
        }

        /**
         * Get the files for currently selected course
         *
         * @param int $courseid the course that is selected
         *
         * @return array all files from currently selected course
         */
        public function getAllFiles($courseid){
            //database statement that selects all documents with the current courseid
            $statement = "SELECT * FROM t_document WHERE t_document.course_id = $courseid";
            //executes the database query
            $result = mysqli_query($this->mysqli, $statement);
            //fetches result into an associatove array row after row
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            //returns all rows with entries
            return $results_array;
        }


        /**
         * delete selected file from database
         *
         * @param int $fid the fileid
         *
         * @return string|false 
         */
        public function deleteFile($fid){
            $statement = "DELETE FROM t_document WHERE t_document.document_id = $fid;";
            return mysqli_query($this->mysqli, $statement);
        }
    }


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