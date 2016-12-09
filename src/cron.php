<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Cron job periodic updates.
 */

require_once(dirname(__FILE__) . '/lib.php');

echo "Updating open questions..." . PHP_EOL;

$open_riddles = get_open_riddles_with_count();
if(sizeof($open_riddles) > 0) {
    echo "Open questions: " . sizeof($open_riddles) . PHP_EOL;

    foreach($open_riddles as $open_riddle) {
        telegram_edit_message(LIVE_CHANNEL_ID, $open_riddle[3], hydrate(CHANNEL_NEW_RIDDLE . CHANNEL_NEW_RIDDLE_UPDATE, array(
            '%PAYLOAD%' => $open_riddle[1] . $open_riddle[0],
            '%ANSWERS%' => intval($open_riddle[4])
        )), array(
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ));
    }
}

echo "Done." . PHP_EOL;