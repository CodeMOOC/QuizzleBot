<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Main command processing lib.
 */

require_once('model/context.php');


/**
 * Handles start commands received from users
 * @param $text String the whole command string
 * @param $context user context
 * @return bool
 */
function handle_start_cmd($text, $context) {

    $payload = extract_command_payload($text);

    if(is_guid($payload)){
        // identify guid and handle it properly
        switch(guid_type($payload)){

            case CMD_PAYLOAD_ACTIVATION_TYPE:
                //handle group activation
                handle_group_activation($payload, $context);
                break;

            case CMD_PAYLOAD_VICTORY_TYPE:
                //handle group victory
                handle_group_victory($payload, $context);
                break;

            case CMD_PAYLOAD_GAME_REGISTRATION_TYPE:
                //handle game registration
                handle_game_registration($payload, $context);
                break;

            case CMD_PAYLOAD_GROUP_REGISTRATION_TYPE:
                //handle group registration
                handle_group_registration($payload, $context);
                break;

            case CMD_PAYLOAD_LOCATION_TYPE:
                //handle group location
                handle_group_location($payload, $context);
                break;

            default:
                handle_unsupported_command($payload, $context);
        }
    } else {
        Logger::debug("Start command with payload not GUID-like");

        // Naked /start message
        if($payload === '') {
            handle_initial_command($payload, $context);
        }
        // Unsupported /start payload
        else {
            handle_unsupported_command($payload, $context);
        }
    }
    return true;
}

/**
 * Handles group registration for a specific game.
 */
function handle_group_registration($payload, $context) {
    if(null === $context->get_group_state()) {
        if(!bot_register_new_group($context)) {
            $context->reply(TEXT_FAILURE_GENERAL);
        }
        else {
            $context->reply(TEXT_CMD_REGISTER_CONFIRM);

            msg_processing_handle_group_state($context);
        }
    }
    else {
        $context->reply(TEXT_CMD_REGISTER_REGISTERED);

        msg_processing_handle_group_state($context);
    }
}

/**
 * Handles game registration for a specific game associated with an event.
 */
function handle_game_registration($payload, $context) {
    //TODO Game registration associated with an event
}

/**
 * Handles group sending a victory command.
 */
function handle_group_victory($payload, $context) {
    Logger::debug("Prize code scanned", __FILE__, $context);

    if($context->get_group_state() === STATE_GAME_LAST_PUZ) {
        $winning_group = bot_get_winning_group($context);
        if($winning_group !== false) {
            $context->reply(TEXT_CMD_START_PRIZE_TOOLATE, array(
                '%GROUP%' => $winning_group
            ));
        }
        else {
            bot_update_group_state($context, STATE_GAME_WON);

            msg_processing_handle_group_state($context);

            Logger::info("Group {$context->get_group_id()} has reached the prize and won", __FILE__, $context, true);

            $context->channel(TEXT_GAME_WON_CHANNEL);
        }
    }
    else {
        $context->reply(TEXT_CMD_START_PRIZE_INVALID);

        Logger::warning("Group {$context->get_group_id()} has reached the prize but is in state {$context->get_group_state()}", __FILE__, $context);
    }
}

/**
 * Handles group sending an activation command
 */
function handle_group_activation($payload, $context) {
    $result = bot_promote_to_active($context);
    switch($result) {
        case true:
            $context->reply(TEXT_ADVANCEMENT_ACTIVATED);
            break;

        case 'not_found':
            $context->reply(TEXT_FAILURE_GROUP_NOT_FOUND);
            break;

        case 'already_active':
            $context->reply(TEXT_FAILURE_GROUP_ALREADY_ACTIVE);

            msg_processing_handle_group_state($context);
            break;

        case 'invalid_state':
            $context->reply(TEXT_FAILURE_GROUP_INVALID_STATE);

            msg_processing_handle_group_state($context);
            break;

        case false:
        default:
            $context->reply(TEXT_FAILURE_GENERAL);
            break;
    }
}

/**
 * Handles group sending new location command
 */
function handle_group_location($payload, $context) {
    Logger::debug("Treasure hunt code: '{$payload}'", __FILE__, $context);

    $result = bot_reach_location($context, $payload);

    if($result === false) {
        $context->reply(TEXT_FAILURE_GENERAL);
    }
    else if($result === 'unexpected') {
        $context->reply(TEXT_CMD_START_LOCATION_UNEXPECTED);
    }
    else if($result === 'wrong') {
        $context->reply(TEXT_CMD_START_LOCATION_WRONG);
    }
    else {
        $context->reply(TEXT_CMD_START_LOCATION_REACHED);

        msg_processing_handle_group_state($context);

        if($context->get_group_state() === STATE_GAME_LAST_PUZ) {
            //TODO warn others!
        }
    }
}

/**
 * Handles unsupported /start command payloads
 */
function handle_unsupported_command($payload, $context) {
    Logger::warning("Unsupported /start payload received: '{$payload}'", __FILE__, $context);

    $context->reply(TEXT_CMD_START_WRONG_PAYLOAD);
}

function handle_initial_command($payload, $context) {
    if(null !== $context->get_group_state()) {
        $context->reply(TEXT_CMD_START_REGISTERED);

        msg_processing_handle_group_state($context);
    }
    else {
        $context->reply(TEXT_CMD_START_NEW);
    }
}

/**
 * Identifies the GUID type, looking for it in the DB
 */
function guid_type($guid) {
    //TODO identify guid type looking for it in the DB
}