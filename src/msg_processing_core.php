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

/**
 * Attempts to switch to "quiz answering" mode, by quiz code.
 * Replies to user if code is not recognized.
 */
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

/**
 * Handles generic input text based on user status.
 */
function process_status($context, $text) {
    $status = $context->get_status();

    switch($status) {
        case IDENTITY_STATUS_REG_CONFIRM:
            if(in_array(extract_response($text), REGISTER_AFFIRMATIVE)) {
                change_identity_status($context->get_telegram_user_id(), IDENTITY_STATUS_REG_NAME);

                $context->reply(REGISTER_QUERY_NAME);
            }
            else {
                // Reset to defaults
                set_identity_default_status($context->get_telegram_user_id());
                change_identity_group_name($context->get_telegram_user_id());
                set_identity_participants_count($context->get_telegram_user_id());

                $context->reply(REGISTER_RESET);
            }
            return true;

        case IDENTITY_STATUS_REG_NAME:
            $clean_name = trim_response($text);
            if(strlen($clean_name) < 3) {
                $context->reply(REGISTER_INVALID_NAME);
            }
            else {
                change_identity_status($context->get_telegram_user_id(), IDENTITY_STATUS_REG_COUNT);
                change_identity_group_name($context->get_telegram_user_id(), $clean_name);

                $context->reply(REGISTER_QUERY_PARTICIPANTS);
            }
            return true;

        case IDENTITY_STATUS_REG_COUNT:
            $count = intval(extract_response($text));
            if($count <= 0) {
                $context->reply(REGISTER_INVALID_COUNT);
            }
            else if($count >= 500) {
                $context->reply(REGISTER_TOO_HIGH_COUNT);
            }
            else {
                set_identity_default_status($context->get_telegram_user_id());
                set_identity_participants_count($context->get_telegram_user_id(), $count);

                $context->reply(REGISTER_QUERY_OK);
            }
            return true;
    }

    return false;
}

function process_text_message($context, $text) {
    Logger::debug("Processing text '{$text}'", __FILE__, $context);

    if(process_command($context, $text)) {
        return;
    }

    if(process_status($context, $text)) {
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

            $riddle_info = get_riddle($current_riddle_id);
            if($riddle_info[RIDDLE_CHANNEL_MESSAGE_ID]) {
                telegram_edit_message(LIVE_CHANNEL_ID, $riddle_info[RIDDLE_CHANNEL_MESSAGE_ID], hydrate(CHANNEL_CORRECT_ANSWER, array(
                    '%ANSWER%' => $text
                )), array(
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true
                ));
            }

            $riddle_stats = get_riddle_current_stats($current_riddle_id);
            $riddle_top = get_riddle_topten($current_riddle_id);

            // Post to channel
            $channel_text = hydrate(CHANNEL_FINAL, array(
                '%CODE%' => $riddle_info[RIDDLE_SALT] . $riddle_info[RIDDLE_ID],
                '%TOTAL_COUNT%' => $riddle_stats[0],
                '%TOTAL_PARTICIPANTS%' => $riddle_stats[1],
                '%PERCENT_CORRECT%' => $riddle_stats[2]
            ));
            if(count($riddle_top) > 0)
                $channel_text .= "\n🥇 {$riddle_top[0][1]}";
            if(count($riddle_top) > 1)
                $channel_text .= "\n🥈 {$riddle_top[1][1]}";
            if(count($riddle_top) > 2)
                $channel_text .= "\n🥉 {$riddle_top[2][1]}";

            telegram_send_message(LIVE_CHANNEL_ID, $channel_text, array(
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ));
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
