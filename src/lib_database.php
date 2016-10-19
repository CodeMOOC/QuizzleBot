<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Database support library. Don't change a thing here.
 */

require_once ('lib_core_database.php');


//RIDDLES
/**
 * Returns a specific riddle as an array.
 *
 * @param $riddle_id
 * @return mixed
 */
function get_riddle($riddle_id) {
    return db_row_query("SELECT * FROM `riddle` WHERE `id` = $riddle_id");
}

/**
 * Open a new riddle returning the new riddle Id.
 * @return Int New riddle ID.
 */
function open_riddle() {
    return db_perform_action("INSERT INTO `riddle` VALUES (DEFAULT, DEFAULT, NULL, NULL)");
}

/**
 * Closes a riddle then returns ordered riddle stats (telegram_id, success).
 *
 * @param $riddle_id Riddle ID.
 * @param $text Answer string.
 * @return array Answer stats of the closed riddle.
 */
function close_riddle($riddle_id, $text) {
    if(is_riddle_closed($riddle_id)){
        throw new ErrorException('Riddle already closed');
    }

    $clean_text = db_escape(extract_response($text));

    db_perform_action("START TRANSACTION");

    db_perform_action("UPDATE `riddle` SET `riddle`.`answer` = '$clean_text', `riddle`.`end_time` = CURRENT_TIMESTAMP WHERE `riddle`.`id` = $riddle_id");

    $stats = db_table_query("SELECT `answer`.`telegram_id` as telegram_id, `riddle`.`answer` = `answer`.`text` as success FROM `answer` LEFT JOIN `riddle` ON `answer`.`riddle_id` = `riddle`.`id` WHERE `riddle`.`id` = {$riddle_id} AND `riddle`.`answer` IS NOT NULL ORDER BY success DESC, `answer`.`last_update` ASC");

    db_perform_action("COMMIT");

    return $stats;
}

/**
 * Returns the id of the last open riddles (if there is one).
 *
 * @return Int
 */
function get_last_open_riddle_id() {
    return db_scalar_query("SELECT id FROM `riddle` WHERE end_time IS NULL ORDER BY `start_time` DESC LIMIT 1");
}

/**
 * Checks whether a riddle is open or not.
 *
 * @param $riddle_id
 * @return bool True if the riddle is closed.
 */
function is_riddle_closed($riddle_id) {
    return db_scalar_query("SELECT IF(`answer` IS NULL, '0', '1') FROM `riddle` WHERE id = $riddle_id") === 1;
}

//ANSWERS

/**
 * Returns a specific answer identified by $telegram_id, $riddle_id, as an array.
 *
 * @param $user_id
 * @param $riddle_id
 * @return mixed
 */
function get_answer($telegram_id, $riddle_id) {
    return  db_row_query("SELECT * FROM `answer` WHERE `telegram_id` = $telegram_id AND `riddle_id` = $riddle_id ORDER BY answer.last_update DESC" );
}

/**
 * Checks whether the answer (identified by $telegram_id, $riddle_id) is correct.
 *
 * @param $user_id
 * @param $riddle_id
 * @return bool
 * @throws ErrorException
 */
function is_answer_correct($telegram_id, $riddle_id) {
    $answer_row = get_answer($telegram_id, $riddle_id);

    if(is_riddle_closed($answer_row[ANSWER[ANSWER_RIDDLE_ID]]) === false){
        throw new ErrorException('Riddle still open');
    }

    $riddle_row = get_riddle($answer_row[ANSWER[ANSWER_RIDDLE_ID]]);

    return strcasecmp (trim($riddle_row[RIDDLE[RIDDLE_ANSWER]]), trim($answer_row[ANSWER[ANSWER_TEXT]])) == 0;
}

/**
 * Removes all answers to a riddle.
 *
 * @param $telegram_id
 * @param $riddle_id
 * @return bool|int
 */
function delete_already_answered($telegram_id, $riddle_id) {
    return db_perform_action("DELETE FROM `answer` WHERE riddle_id = $riddle_id AND telegram_id = $telegram_id");
}

/**
 * Inserts answer to a riddle.
 * @param $telegram_id Telegram ID of the user.
 * @param $text Answer to register.
 * @param $riddle_id ID of the riddle. Null defaults to last open riddle,. if any.
 * @return bool True on success. False otherwise.
 */
function insert_answer($telegram_id, $text, $riddle_id = null) {
    if($riddle_id === null) {
        $riddle_id = get_last_open_riddle_id();
    }

    if($riddle_id) {
        $clean_text = db_escape(extract_response($text));
        return db_perform_action("REPLACE INTO `answer` VALUES ({$telegram_id}, {$riddle_id}, '{$clean_text}', DEFAULT)") === 1;
    }

    throw new ErrorException('No open riddles');
}


//DB

/**
 * Completely wipes out the DB data.
 */
function reset_db() {

    db_perform_action("START TRANSACTION;");
    db_perform_action("TRUNCATE answer");
    db_perform_action("TRUNCATE identity");
    db_perform_action("DELETE FROM riddle");
    db_perform_action("ALTER TABLE riddle AUTO_INCREMENT = 1");
    db_perform_action("COMMIT");

}

