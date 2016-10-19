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

function process_text_message($context, $text) {
    $last_open_riddle_id = get_last_open_riddle_id();

    $command = extract_command($text);
    echo "Input text: {$text} (command: {$command})" . PHP_EOL;

    if($command === 'new' && $context->is_abmin()) {
        if($last_open_riddle_id === null) {
            // New question!
            $new_riddle_id = open_riddle();

            Logger::info("New riddle ID: {$new_riddle_id}", __FILE__, $context);

            $context->reply(QUIZ_CREATED_OK);
        }
        else {
            $context->reply(QUIZ_ALREADY_OPEN);
        }
    }
    else if($command === 'help') {
        $context->reply(COMMAND_HELP);
    }
    else if($command === null) {
        if($last_open_riddle_id === null) {
            $context->reply(ANSWER_NO_QUIZ);
            if($context->is_abmin()) {
                $context->reply(ANSWER_NO_QUIZ_ADMIN);
            }
            return;
        }

        if($context->is_abmin()) {
            // Correct answer given
            try {
                $stats = close_riddle($last_open_riddle_id, $text);

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
            insert_answer($in->from_id, $text, $last_open_riddle_id);

            $context->reply(ANSWER_ACCEPTED);
            return;
        }
    }
    else {
        $context->reply(COMMAND_UNKNOWN);
    }
}

function process_message($context) {
    if($context->get_message()->is_group()) {
        // Group messaging not supported
        error_log('Group message ignored');
    }
    else if($context->get_message()->is_private()) {
        if($context->get_message()->is_text()) {
            process_text_message($context, $context->get_message()->text);
        }
        else {
            $context->reply(MESSAGE_NOT_SUPPORTED);
        }
    }
}

// Set default timezone for date operations
date_default_timezone_set('UTC');

// Basic payload loading
$in = new IncomingMessage($message);
$context = new Context($in);

process_message($context);

// Done! Bye bye
