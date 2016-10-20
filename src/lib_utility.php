<?php
/**
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Support library. Don't change a thing here.
 */

/**
 * Checks whether a text string starts with another.
 * Performs a case-insensitive check.
 * @param $text String to search in.
 * @param $substring String to search for.
 * @return bool True if $text starts with $substring.
 */
function starts_with($text = '', $substring = '') {
    return (strpos(mb_strtolower($text), mb_strtolower($substring)) === 0);
}

/**
 * Extracts the first command from a given text string.
 */
function extract_command($text = '') {
    $matches = array();
    if(preg_match("/^\/([a-zA-Z0-9_]*)( |$)/", $text, $matches) !== 1) {
        return null;
    }

    if(sizeof($matches) < 2) {
        return null;
    }

    return $matches[1];
}

/**
 * Extracts the command payload from a string.
 * @param $text String to search in.
 * @return string Command payload, if any, or empty string.
 */
function extract_command_payload($text = '') {
    return mb_ereg_replace("^\/([a-zA-Z0-9_]*)( |$)", '', $text);
}

/**
 * Extracts a cleaned-up response from the user.
 */
function extract_response($text) {
    if(!$text) {
        return '';
    }

    $lower_response = mb_strtolower(trim_response($text));
    return escape_accents($lower_response);
}

function trim_response($text) {
    return trim($text, ' /,.!?;:\'"');
}

/**
 * Hydrates a string value using a map of key/values.
 */
function hydrate($text, $map = null) {
    if(!$map || !is_array($map)) {
        $map = array();
    }

    foreach($map as $from => $to) {
        $text = str_replace($from, escape_markdown($to), $text);
    }
    return $text;
}

/**
 * Unite two arrays, even if they are null.
 * Always returns a valid array.
 */
function unite_arrays($a, $b) {
    if(!$a || !is_array($a)) {
        $a = array();
    }

    if($b && is_array($b)) {
        $a = array_merge($a, $b);
    }

    return $a;
}

/**
 * Escapes Markdown reserved characters so non-Markdown text can be
 * embedded in a Markdown message without issues.
 */
function escape_markdown($text) {
    return mb_ereg_replace('([_*\[\]\(\)])', '\\\1', $text);
}

/**
 * Escapes accents.
 */
function escape_accents($text)
{
    return preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($text, ENT_QUOTES, 'UTF-8'));
}

/**
 * Checks whether $text is a valid guid or not.
 * @param $text String the string to be checked.
 * @return boolean
 */
function is_guid($text) {
    return preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $text) > 0;
}

function generate_qr_code_url($code) {
    $deeplink_base_url = rawurlencode(TELEGRAM_DEEP_LINK_URI_BASE);

    return "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=$deeplink_base_url$code&choe=UTF-8";
}


?>
