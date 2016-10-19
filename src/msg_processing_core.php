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

function process_text_message($context, $text) {
    Logger::debug("Processing text '{$text}', last open riddle ID {$context->get_last_open_riddle_id()}", __FILE__, $context);

    if(process_command($context, $text)) {
        return;
    }

    if($context->get_last_open_riddle_id() === null) {
        $context->reply(ANSWER_NO_QUIZ);
        if($context->is_abmin()) {
            $context->reply(ANSWER_NO_QUIZ_ADMIN);
        }
        return;
    }

    if($context->is_abmin()) {
        // Correct answer given
        try {
            $stats = close_riddle($context->get_last_open_riddle_id(), $text);

            $i = 1;
            foreach($stats as $answer) {
                echo "{$answer[0]} answer is {$answer[1]}" . PHP_EOL;

                $add_values = array(
                    '%INDEX%' => $i,
                    '%CORRECT_ANSWER%' => $text
                );
                $context->send($answer[0], ($answer[1]) ? ANSWER_CORRECT : ANSWER_WRONG, $add_values);

                ++$i;

                usleep(1000000 / 40); // 40 messages per second primitive rate limiting
            }
        }
        catch(exception $e) {
            $context->reply("Failure");
        }
    }
    else {
        // User answer given
        insert_answer($context->get_message()->from_id, $text, $last_open_riddle_id);

        $context->reply(ANSWER_ACCEPTED);
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
