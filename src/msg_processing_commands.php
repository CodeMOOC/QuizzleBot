<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Command message processing functionality.
 */

/**
 * Processes commands.
 * @return bool True if the message was handled.
 */
function process_command($context, $text) {
    $command = extract_command($text);

    if($command === 'new' && $context->is_abmin()) {
        $last_open_riddle = get_last_open_riddle_id();
        if($last_open_riddle === null) {
            // New question!
            $new_riddle_data = open_riddle();

            Logger::info("New riddle ID: {$new_riddle_data[0]}", __FILE__, $context);

            $riddle_deeplink_url = get_riddle_qrcode_url($new_riddle_data[0]);

            telegram_send_photo($context->get_telegram_chat_id(), $riddle_deeplink_url,
                QUIZ_CREATED_OK . $new_riddle_data[1]);
        }
        else {
            $context->reply(QUIZ_ALREADY_OPEN);
        }

        return true;
    }
    else if($command === 'help') {
        $context->reply(COMMAND_HELP);

        return true;
    }
    else if($command === 'start' && !$context->is_abmin()) {
        // User wants to answer riddle
        $payload = extract_command_payload($text);
        Logger::debug("Start payload '{$payload}'", __FILE__, $context);

        $riddle_info = get_riddle_by_code($payload);
        if($riddle_info === null) {
            $context->reply(START_UNKNOWN_PAYLOAD);
            return true;
        }

        $riddle_id = $riddle_info[0];
        $prev_answer = get_answer($context->get_telegram_user_id(), $riddle_id);
        if($prev_answer !== null) {
            $context->reply(START_ALREADY_ANSWERED, array(
                '%ANSWER%' => $prev_answer[ANSWER_TEXT]
            ));
            return true;
        }

        set_identity_answering_status($context->get_telegram_user_id(), $riddle_id);

        $context->reply(START_RECOGNIZED);
        return true;
    }

    return false;
}
