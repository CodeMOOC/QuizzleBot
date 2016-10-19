<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Basic message processing functionality.
 */

require_once('model/context.php');
require_once('lib.php');
require_once('msg_processing_commands.php');

function switch_to_riddle($context, $code) {
    $riddle_info = get_riddle_by_code($code);
    if($riddle_info === null) {
        $context->reply(START_UNKNOWN_PAYLOAD);
        return;
    }

    $riddle_id = $riddle_info[0];
    $prev_answer = get_answer($context->get_telegram_user_id(), $riddle_id);
    if($prev_answer !== null) {
        $context->reply(START_ALREADY_ANSWERED, array(
            '%ANSWER%' => $prev_answer[ANSWER_TEXT]
        ));
        return;
    }

    set_identity_answering_status($context->get_telegram_user_id(), $riddle_id);

    $context->reply(START_RECOGNIZED);
}

function process_text_message($context, $text) {
    Logger::debug("Processing text '{$text}'", __FILE__, $context);

    if(process_command($context, $text)) {
        return;
    }

    $current_riddle_id = $context->get_current_riddle_id();
    Logger::debug("Current riddle ID: {$current_riddle_id}", __FILE__, $context);

    if($current_riddle_id === null) {
        if($context->is_abmin()) {
            $context->reply(ANSWER_NO_QUIZ_ADMIN);
            return;
        }

        switch_to_riddle($context, $text);
        return;
    }

    if($context->is_abmin()) {
        // Correct answer given
        try {
            close_riddle($current_riddle_id, $text);

            // TODO: notify on channel
        }
        catch(exception $e) {
            $context->reply("Failure");
        }
    }
    else {
        // User answer given
        insert_answer($context->get_telegram_user_id(), $current_riddle_id, $text);

        if(is_riddle_closed($current_riddle_id)) {
            $answer_info = get_answer_info($context->get_telegram_user_id(), $current_riddle_id, $text);

            $context->reply(
                ($answer_info[0]) ? ANSWER_CORRECT : ANSWER_WRONG,
                array(
                    '%CORRECT_ANSWER%' => $answer_info[1],
                    '%PERCENT_CORRECT%' => $answer_info[2]
                )
            );
        }
        else {
            // Riddle is still open
            $context->reply(ANSWER_ACCEPTED);
        }

        set_identity_default_status($context->get_telegram_user_id());

        return;
    }
}

function process_message($context) {
    if($context->get_message()->is_private()) {
        if($context->get_message()->is_text()) {
            process_text_message($context, $context->get_message()->text);
        }
        else {
            $context->reply(MESSAGE_NOT_SUPPORTED);
        }
    }
    else {
        Logger::warning("Non-private message ignored", __FILE__, $context);
    }
}

// Set default timezone for date operations
date_default_timezone_set('UTC');

// Basic payload loading
$in = new IncomingMessage($message);
$context = new Context($in);

process_message($context);

// Done! Bye bye
