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
 *
 * @return Int the mew riddle Id
 */
function open_riddle() {
    return db_perform_action("INSERT INTO `riddle` VALUES ()");
}

/**
 * Close a riddle.
 *
 * @param $riddle_id Int the riddle id
 * @param $text String the answer.
 * @return bool
 */
function close_riddle($riddle_id, $text) {
    return db_perform_action("UPDATE `riddle` SET `riddle`.`answer` = '$text', `riddle`.`end_time` = CURRENT_TIMESTAMP WHERE `riddle`.`id` = $riddle_id");
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
 * Checks whether a riddle is open or not, returning 0 or 1.
 *
 * @param $riddle_id
 * @return int Returns 0 or 1.
 */
function is_riddle_closed($riddle_id) {
    return db_scalar_query("SELECT IF(`answer` IS NULL, '0', '1') FROM `riddle` WHERE id = $riddle_id");
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

    if(is_riddle_closed($answer_row[ANSWER[ANSWER_RIDDLE_ID]]) != 1){
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
 * @param $riddle_id ID of the riddle. Null default to last open riddle,. if any.
 * @return bool True on success. False otherwise.
 */
function insert_answer($telegram_id, $text, $riddle_id = null) {
    if($riddle_id === null) {
        $riddle_id = get_last_open_riddle_id();
    }

    if($riddle_id) {
        return db_perform_action("REPLACE INTO `answer` VALUES ({$telegram_id}, {$riddle_id}', '" . db_escape($text) . "', DEFAULT)") === 1;
    }

    throw new ErrorException('No open riddles');
}

//USERS
function user_stats($telegram_id) {
 //TODO: what stats?
}
