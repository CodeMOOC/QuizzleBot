<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Database base support library. Don't change a thing here.
 */

 function db_default_error_logging($connection, $message = "Database error") {
    $errno = mysqli_errno($connection);
    $error = mysqli_error($connection);
    echo '(DB) ' . "$message #$errno: $error". PHP_EOL;
 }

/**
 * Closes the existing connection to the database, if any.
 */
function db_close_connection() {
    if(isset($GLOBALS['db_connection'])) {
        $connection = $GLOBALS['db_connection'];

        mysqli_close($connection);
    }

    unset($GLOBALS['db_connection']);
}

/**
 * Creates or retrieves a connection to the database.
 * @param bool $quick True if the connection should be returned untested.
 * @return object A valid connection to the database.
 */
function db_open_connection($quick = false) {
    if(isset($GLOBALS['db_connection'])) {
        $connection = $GLOBALS['db_connection'];

        if(!$quick) {
            // Ping the connection just to be safe
            // This can be removed for performance since we usually have no
            // long-running scripts.
            if(!mysqli_ping($connection)) {
                Logger::fatal('Database connection already open but does not respond to ping', __FILE__);
            }
        }

        return $connection;
    }
    else {
        // Check configuration
        if(!DATABASE_USERNAME || !DATABASE_NAME) {
            Logger::fatal('Please configure the database connection in file config.php', __FILE__);
        }

        // Open up a new connection
        $connection = mysqli_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);

        if(!$connection) {
            $errno = mysqli_connect_errno();
            $error = mysqli_connect_error();
            Logger::fatal("Failed to establish database connection. Error #$errno: $error", __FILE__);
        }

        if(!mysqli_real_query($connection, 'SET NAMES utf8')) {
            Logger::warning("Failed to set names UTF8", __FILE__);
        }

        // Store connection for later
        $GLOBALS['db_connection'] = $connection;

        // Register clean up function for termination
        register_shutdown_function('db_close_connection');

        return $connection;
    }
}

/**
 * Performs an "action query" (UPDATE, INSERT, REPLACE, or similar)
 * and returns the number of rows affected on success.
 * @param string $sql SQL query to perform.
 * @return bool | int Auto-generated ID for INSERT or UPDATE queries, if
 *                    applicable, otherwise the number of affected rows
 *                    or false on failure.
 */
function db_perform_action($sql) {
    $connection = db_open_connection();

    if(!mysqli_real_query($connection, $sql)) {
        db_default_error_logging($connection, "Failed to perform query ($sql)");
        return false;
    }

    $generated_id = mysqli_insert_id($connection);
    if($generated_id > 0) {
        return $generated_id;
    }

    $affected_rows = mysqli_affected_rows($connection);
    if($affected_rows < 0) {
        db_default_error_logging($connection, "Failed to check for affected rows");
        return false;
    }

    return $affected_rows;
}

/**
 * Performs a "select query" which is expected to return one
 * single scalar value.
 * @param string $sql SQL query to perform.
 * @return mixed The single scalar value returned by the query,
 *               null if no value was returned, or false on failure.
 */
function db_scalar_query($sql) {
    $connection = db_open_connection();

    if(!mysqli_real_query($connection, $sql)) {
        db_default_error_logging($connection);
        return false;
    }

    $result = mysqli_store_result($connection);
    if($result === false) {
        db_default_error_logging($connection, "Failed to store query results");
        return false;
    }

    // Sanity checks
    if(mysqli_field_count($connection) !== 1) {
        mysqli_free_result($result);
        Logger::error("Query ($sql) generated results with multiple fields (non-scalar)", __FILE__);
        return false;
    }
    $num_rows = mysqli_num_rows($result);
    if($num_rows > 1) {
        mysqli_free_result($result);
        Logger::error("Query ($sql) generated more than one row of results (non-scalar)", __FILE__);
        return false;
    }
    else if($num_rows == 0) {
        mysqli_free_result($result);
        return null;
    }

    // Extract first row
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);

    //Error checking on results (just for the sake of it)
    if($row == null) {
        Logger::error("Failed to access first row of query results", __FILE__);
        return false;
    }
    if(count($row) < 1) {
        Logger::error("Results row is empty", __FILE__);
        return false;
    }

    // Extract and return first field
    return $row[0];
}

/**
 * Performs a "select query" and returns the complete
 * result data as a matrix. This function is best suited
 * for queries expected to return little data.
 * @param string $sql SQL query to perform.
 * @return bool | array The results table or false on failure.
 */
function db_table_query($sql) {
    $connection = db_open_connection();

    if(!mysqli_real_query($connection, $sql)) {
        db_default_error_logging($connection);
        return false;
    }

    $result = mysqli_store_result($connection);
    if($result === false) {
        db_default_error_logging($connection, "Failed to store query results");
        return false;
    }

    $matrix = [];
    while(($row = mysqli_fetch_row($result)) != null) {
        $matrix[] = $row;
    }

    mysqli_free_result($result);

    return $matrix;
}

/**
 * Performs a "select query" and returns one row of data as
 * an array. This function is inteded for queries expected to
 * return only one single row of data (e.g., queries with a
 * "LIMIT 1" SQL clause or with a "WHERE" clause you are sure
 * evaluates to true for only one row of the table).
 * This query will fail if more than one row of results is
 * generated.
 * @param string $sql SQL query to perform.
 * @return mixed The results row as an array, null if
 *               no results where generated, or false on failure.
 */
function db_row_query($sql) {
    $connection = db_open_connection();

    if(!mysqli_real_query($connection, $sql)) {
        db_default_error_logging($connection);
        return false;
    }

    $result = mysqli_store_result($connection);
    if($result === false) {
        db_default_error_logging($connection, "Failed to store query results");
        return false;
    }

    $num_rows = mysqli_num_rows($result);
    if($num_rows > 1) {
        mysqli_free_result($result);
        Logger::error("Query ($sql) generated more than one row of results", __FILE__);
        return false;
    }
    else if($num_rows == 0) {
        mysqli_free_result($result);
        return null;
    }

    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);

    return $row;
}

/**
 * Escapes a string to be used as a value inside an SQL query.
 * @return string Escaped string.
 */
function db_escape($s) {
    return mysqli_real_escape_string(db_open_connection(true), (string)$s);
}

const SESSION_ID                = 0;
const SESSION_START_DATE        = 1;
const SESSION_CREATOR_ID        = 2;

const RIDDLE_ID                 = 0;
const RIDDLE_SESSION_ID         = 1;
const RIDDLE_START_TIME         = 2;
const RIDDLE_ENS_TIME           = 3;
const RIDDLE_ANSWER             = 4;
const RIDDLE_SALT               = 5;
const RIDDLE_CHANNEL_MESSAGE_ID = 6;

const ANSWER_TELEGRAM_ID    = 0;
const ANSWER_RIDDLE_ID      = 1;
const ANSWER_TEXT           = 2;
const ANSWER_LAST_UPDATE    = 3;

const IDENTITY_TELEGRAM_ID          = 0;
const IDENTITY_FIRST_NAME           = 1;
const IDENTITY_FULL_NAME            = 2;
const IDENTITY_GROUP_NAME           = 3;
const IDENTITY_STATUS               = 4;
const IDENTITY_RIDDLE_ID            = 5;
const IDENTITY_PARTICIPANTS_COUNT   = 6;

const IDENTITY_STATUS_DEFAULT       = 0;
const IDENTITY_STATUS_ANSWERING     = 1;
const IDENTITY_STATUS_REG_CONFIRM   = 2;
const IDENTITY_STATUS_REG_NAME      = 3;
const IDENTITY_STATUS_REG_COUNT     = 4;
const IDENTITY_STATUS_NEW_SESSION   = 10;
