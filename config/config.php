<?php
    session_start();

    define('DB_HOST', '303.itpwebdev.com');
    define('DB_USER', 'hyunbumy_db_user');
    define('DB_PASS', 'ITPusersql7');
    define('DB_NAME', 'hyunbumy_poi_db');

    // Session expires after 30 min of inactivity
    if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        session_unset();
        session_destroy();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
?>
