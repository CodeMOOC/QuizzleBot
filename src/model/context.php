<?php
/*
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Class wrapping the bot's context in this run.
 */

require_once(dirname(__FILE__) . '/../lib.php');

require_once(dirname(__FILE__) . '/incoming_message.php');

class Context {

    private $message;

    private $group_name = null;
    private $group_participants = 1;
    private $status = 0;
    private $active_riddle_id = null;

    /**
     * Construct Context class.
     * @param §message IncomingMessage.
     */
    function __construct($message) {
        if(!($message instanceof IncomingMessage))
            die('Message variable is not an IncomingMessage instance');

        $this->message = $message;

        $this->refresh();
    }

    /* True if the talking user is an admin */
    function is_abmin() {
        return $this->message->from_id === ADMIN_TELEGRAM_ID;
    }

    /**
     * Gets the user's Telegram ID.
     */
    function get_telegram_user_id() {
        return $this->message->from_id;
    }

    /**
     * Gets the user's chat ID.
     */
    function get_telegram_chat_id() {
        return $this->message->chat_id;
    }

    /**
     * Gets the raw message.
     */
    function get_message() {
        return $this->message;
    }

    /**
     * Gets the current riddle ID based on user and bot status.
     * @return int Current riddle ID or null if no active riddle.
     */
    function get_current_riddle_id() {
        // Non-admins, while answering to a given riddle
        if(!$this->is_abmin() && $this->status === IDENTITY_STATUS_ANSWERING && $this->active_riddle_id !== null) {
            return $this->active_riddle_id;
        }

        // Get last open riddle for admins
        if($this->is_abmin()) {
            return get_last_open_riddle_id();
        }

        return null;
    }

    /**
     * Gets the user's status.
     */
    function get_status() {
        return $this->status;
    }

    /**
     * Gets a cleaned-up response from the user, if any.
     */
    function get_response() {
        $text = $this->message->text;
        if($text)
            return extract_response($text);
        else
            return '';
    }

    /**
     * Replies to the current incoming message.
     * Enables HTML parsing and disables web previews by default.
     */
    function reply($message, $additional_values = null, $additional_parameters = null) {
        $hydration_values = array(
            '%FIRST_NAME%' => $this->get_message()->get_sender_first_name(),
            '%FULL_NAME%' => $this->get_message()->get_sender_full_name()
        );

        $hydrated = hydrate($message, unite_arrays($hydration_values, $additional_values));

        return telegram_send_message(
            $this->get_telegram_chat_id(),
            $hydrated,
            unite_arrays(array(
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
                // "Hide keyboard" is added by default to all messages because
                // of a bug in Telegram that doesn't hide "one-time" keyboards after use
                'reply_markup' => array(
                    'hide_keyboard' => true
                )
            ), $additional_parameters)
        );
    }

    /**
     * Replies to the user asking for confirmation (yes/no).
     */
    function confirm($message, $additional_values = null) {
        return $this->reply($message, $additional_values, array(
            'reply_markup'=> array(
                'keyboard' => array(
                    array('Sì', 'No')
                ),
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            )
        ));
    }

    /**
     * Sends message to a known telegram chat by ID.
     */
    function send($telegram_id, $message, $additional_values = null) {
        $hydration_values = array(
            // TODO: get known values from DB
        );

        $hydrated = hydrate($message, unite_arrays($hydration_values, $additional_values));

        return telegram_send_message(
            $telegram_id,
            $hydrated,
            array(
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            )
        );
    }

    /**
     * Refreshes the context from the DB.
     */
    function refresh() {
        $identity = get_identity($this->get_telegram_user_id(), $this->message->get_sender_first_name(), $this->message->get_sender_full_name());

        $this->group_name = $identity[IDENTITY_GROUP_NAME];
        $this->group_participants = intval($identity[IDENTITY_PARTICIPANTS_COUNT]);
        if($identity[IDENTITY_STATUS])
            $this->status = intval($identity[IDENTITY_STATUS]);
        if($identity[IDENTITY_RIDDLE_ID])
            $this->active_riddle_id = intval($identity[IDENTITY_RIDDLE_ID]);

        Logger::debug("Group {$this->group_name}, part. {$this->group_participants}, status {$this->status}, active riddle {$this->active_riddle_id} (" . (($this->is_abmin()) ? "is admin" : "is NOT admin") . ")", __FILE__, $this);
    }

}
