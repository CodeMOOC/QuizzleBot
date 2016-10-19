<?php
/*
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Class wrapping the bot's context in this run.
 */

require_once('lib.php');

require_once('incoming_message.php');

class Context {

    private $message;

    /**
     * Construct Context class.
     * @param §message IncomingMessage.
     */
    function __construct($message) {
        if(!($message instanceof IncomingMessage))
            die('Message variable is not an IncomingMessage instance');

        $this->message = $message;
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
     * Enables markdown parsing and disables web previews by default.
     */
    function reply($message, $additional_values = null) {
        $hydration_values = array(
            '%FIRST_NAME%' => $this->get_message()->get_sender_first_name(),
            '%FULL_NAME%' => $this->get_message()->get_sender_full_name()
        );

        $hydrated = hydrate($message, unite_arrays($hydration_values, $additional_values));

        return telegram_send_message(
            $this->get_telegram_chat_id(),
            $hydrated,
            array(
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            )
        );
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

}
