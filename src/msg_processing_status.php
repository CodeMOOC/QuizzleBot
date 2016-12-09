<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Message processing functionality for conversation status.
 */

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

        case IDENTITY_STATUS_NEW_SESSION:
            if(in_array(extract_response($text), REGISTER_AFFIRMATIVE)) {
                open_new_session($context->get_telegram_user_id());

                $context->reply(SESSION_NEW_CONFIRM);
            }
            else {
                $context->reply(GENERIC_NEVERMIND);
            }

            set_identity_default_status($context->get_telegram_user_id());

            return true;
    }

    return false;
}
