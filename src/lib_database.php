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
function get_riddle($riddle_id) {
    return db_row_query("SELECT * FROM `riddle` WHERE `id` = $riddle_id");
}

function open_riddle() {
    return db_perform_action("INSERT INTO `riddle` VALUES ()");
}

function close_riddle($riddle_id, $text) {
    return db_perform_action("UPDATE `riddle` SET `answer` = '$text', `end_time` = CURRENT_TIMESTAMP WHERE `riddle`.`id` = $riddle_id");
}

function get_last_open_riddle_id() {
    return db_scalar_query("SELECT id FROM `riddle` WHERE end_time IS NULL ORDER BY `start_time` DESC LIMIT 1");
}

function is_riddle_closed($riddle_id) {
    return db_scalar_query("SELECT IF(`answer` IS NULL, '0', '1') FROM `riddle` WHERE id = $riddle_id");
}

//IDENTITIES
function get_identity($identity_id) {
    return db_row_query("SELECT * FROM `identity` WHERE `id` = $identity_id");
}

function insert_identity($telegram_id, $first_name, $full_name = "") {
    return db_perform_action("INSERT INTO `indentity` (`telegram_id`, `first_name`, `full_name`) VALUES ('$telegram_id', '$first_name', '$full_name')");
}


//ANSWERS
function get_answer($answer_id) {
    return  db_row_query("SELECT * FROM `answer` WHERE `id` = $answer_id");
}

function is_answer_correct($answer_id) {
    $answer_row = get_answer($answer_id);
    echo "#".print_r($answer_row, true)."#".PHP_EOL;
    echo "## ".$answer_row[ANSWER[ANSWER_RIDDLE_ID]].PHP_EOL;

    if(is_riddle_closed(trim($answer_row[ANSWER[ANSWER_RIDDLE_ID]])) != 1){
        throw new ErrorException('Riddle still open');
    }

    $riddle_row = get_riddle($answer_row[ANSWER[ANSWER_RIDDLE_ID]]);

    return strcasecmp (trim($riddle_row[RIDDLE[RIDDLE_ANSWER]]), trim($answer_row[ANSWER[ANSWER_TEXT]])) == 0;

}

function insert_answer($user_id, $text) {
    $last_open_riddle_id = get_last_open_riddle_id();
    if($last_open_riddle_id)
        return db_perform_action("INSERT INTO `answer` (`riddle_id`, `identity_id`, `text`) VALUES ('$last_open_riddle_id', '$user_id', '$text')");

    throw new ErrorException('No open riddles');
}

function check_answer($answer_id) {

}