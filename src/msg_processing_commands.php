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

    if($command === 'new') {
        if(!$context->is_abmin()) {
            $context->reply(GENERIC_NOT_ADMIN);
            return true;
        }

        $last_open_riddle = get_last_open_riddle_id();
        if($last_open_riddle === null) {
            // New question!
            $new_riddle_data = open_riddle();
            Logger::info("New riddle ID: {$new_riddle_data[0]}", __FILE__, $context);

            // Notify on channel
            $channel_result = telegram_send_message(LIVE_CHANNEL_ID, hydrate(CHANNEL_NEW_RIDDLE, array(
                '%PAYLOAD%' => $new_riddle_data[1] . $new_riddle_data[0]
            )), array(
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ));
            $channel_msg_id = $channel_result['message_id'];
            Logger::debug("Channel message ID {$channel_msg_id}", __FILE__, $context);

            set_riddle_channel_message_id($new_riddle_data[0], $channel_msg_id);

            // Send QR Code deep link
            $riddle_deeplink_url = get_riddle_qrcode_url($new_riddle_data[0]);
            telegram_send_photo($context->get_telegram_chat_id(), $riddle_deeplink_url,
                QUIZ_CREATED_OK . $new_riddle_data[1] . $new_riddle_data[0]);
        }
        else {
            $context->reply(QUIZ_ALREADY_OPEN);
        }

        return true;
    }
    else if($command === 'session') {
        if(!$context->is_abmin()) {
            $context->reply(GENERIC_NOT_ADMIN);
            return true;
        }

        change_identity_status($context->get_telegram_user_id(), IDENTITY_STATUS_NEW_SESSION);

        $context->confirm(SESSION_NEW_ASK);

        return true;
    }
    else if($command === 'reset' && $context->is_abmin()) {
        reset_db();

        $context->reply(RESET_OK);

        return true;
    }
    else if($command === 'register' || $text === '/start register') {
        if($text === '/start register') {
            $context->reply(REGISTER_WELCOME);
        }

        change_identity_status($context->get_telegram_user_id(), IDENTITY_STATUS_REG_CONFIRM);

        $context->confirm(REGISTER_QUERY_CONFIRM);

        return true;
    }
    else if($command === 'help') {
        $context->reply(COMMAND_HELP);

        return true;
    }
    else if($command === 'start' && !$context->is_abmin()) {
        // User might want to answer riddle
        $payload = extract_command_payload($text);
        Logger::debug("Start payload '{$payload}'", __FILE__, $context);

        if($payload) {
            switch_to_riddle($context, $payload);
        }
        else {
            $context->reply(COMMAND_HELP);
        }

        return true;
    }

    return false;
}
