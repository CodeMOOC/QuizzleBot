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
        if($context->get_last_open_riddle_id() === null) {
            // New question!
            $new_riddle_id = open_riddle();

            Logger::info("New riddle ID: {$new_riddle_id}", __FILE__, $context);

            $context->reply(QUIZ_CREATED_OK);
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
