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

    return false;
}
