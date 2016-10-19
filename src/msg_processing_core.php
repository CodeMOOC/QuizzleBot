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

            $context->reply("Ok.");
        }
        else {
            $context->reply("Riddle still open! Give answer");
        }
    }
    else if($command === 'help') {
        $context->reply("Ah, a <i>te</i> serve aiuto?");
    }
    else if($command === null) {
        if($last_open_riddle_id === null) {
            $context->reply("Nessun quiz attivo.");
            if($context->is_abmin()) {
                $context->reply("Devi attivare un quiz col comando /new.");
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

                    if($answer[1]) {
                        telegram_send_message($answer[0], "Giusto! La tua risposta è stata la {$i}° ad essere registrata. 👍");
                    }
                    else {
                        telegram_send_message($answer[0], "Sbagliato! La risposta corretta era “{$text}”. 😞");
                    }

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
            $result = insert_answer($in->from_id, $text, $last_open_riddle_id);
            $context->reply("Result: " . print_r($result, true));
            return;
        }
    }
    else {
        $context->reply("Comando non previsto");
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
            $context->reply("Messaggio non supportato");
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
