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

// Set default timezone for date operations
date_default_timezone_set('UTC');

// Basic payload loading
$in = new IncomingMessage($message);
$context = new Context($in);

if($in->is_group()) {
    // Group messaging not supported
    error_log('Group message ignored');
    die();
}
else if($in->is_private()) {
    if($in->is_text()) {
        $command = extract_command($in->text);
        echo "Input text: {$in->text} (command: {$command})" . PHP_EOL;

        if($command === 'new' && $context->is_abmin()) {
            // New question!

            $context->reply("Ok.");
            return;
        }

        if($command === 'help') {
            $context->reply("Ah, a <i>te</i> serve aiuto?");
            return;
        }

        if($command === null) {
            $riddle_id = get_last_open_riddle_id();
            if($riddle_id === null) {
                $context->reply("Nessun quiz attivo.");
                if($context->is_abmin()) {
                    $context->reply("Devi attivare un quiz col comando /new.");
                }
                return;
            }

            if($context->is_abmin()) {
                // Correct answer given
            }
            else {
                // User answer given
                $result = insert_answer($in->from_id, $in->text, $riddle_id);
                $context->reply("Result: " . print_r($result, true));
                return;
            }
        }


        $context->reply("Come, scusa?");
    }
    else {
        $context->reply("Messaggio non supportato");
    }
}
