<?php
    /************************************************
     * 
     * Landingpage to logout the loggedin user
     * 
     ************************************************/

    session_start();
    // Destroys the current logged in session
    session_destroy();
    //redirects to the index.php
    header("Location: ../index.php", true, 301);
?>