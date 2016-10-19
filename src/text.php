<?php
/*
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Text strings.
 */

const QUIZ_CREATED_OK = "Nuovo quiz attivo. Codice: ";
const QUIZ_ALREADY_OPEN = "Quiz già attivo: all'invio della risposta corretta il quiz corrente viene chiuso e le risposte vengono consegnate.";

const COMMAND_HELP = "Ciao, sono un semplice bot per gestire dei quiz. Se ci sono quiz attivi puoi registrare direttamente la tua risposta qui, scrivendomi. Quando il quiz verrà chiuso, ti scriverò la risposta corretta e terrò traccia del tuo punteggio!";

const ANSWER_NO_QUIZ = "Digita il codice del quiz per partecipare (oppure scansiona il QR Code).";
const ANSWER_NO_QUIZ_ADMIN = "Nessun quiz attivo. Attiva un nuovo quiz col comando /new.";
const ANSWER_CORRECT = "<b>Giusto!</b> Hai risposto correttamente come il %PERCENT_CORRECT%% di partecipanti. 👍";
const ANSWER_WRONG = "<b>Sbagliato!</b> La risposta corretta era “%CORRECT_ANSWER%”. 😞 (Il %PERCENT_CORRECT% dei partecipanti ha risposto correttamente.)";

const ANSWER_ACCEPTED = "Ok, ho registrato la tua risposta. Vedi il risultato sul canale @" . LIVE_CHANNEL_ID . ".";

const START_UNKNOWN_PAYLOAD = "Ops, non riconosco questo codice. 😕\nDigita il codice del quesito (o scansiona il QR Code).";
const START_ALREADY_ANSWERED = "Hai già risposto a questo quesito (la tua prima risposta era “%ANSWER%”).";
const START_RECOGNIZED = "Scrivi qui la tua risposta al quesito.";

const RESET_OK = "All is forgotten.";

const CHANNEL_NEW_RIDDLE = "Nuovo quesito! <a href=\"" . TELEGRAM_DEEP_LINK_URI_BASE . "%PAYLOAD%\">Rispondi al bot</a>.";
const CHANNEL_FINAL = "🏁 <b>Quesito chiuso</b> (%CODE%)\nTotale risposte: <b>%TOTAL_COUNT%</b>\nPartecipanti: <b>%TOTAL_PARTICIPANTS%</b>\nRisposte corrette: <b>%PERCENT_CORRECT%%</b>\n\n<b>Prime risposte corrette:</b>";

const COMMAND_UNKNOWN = "Comando non riconosciuto.";

const MESSAGE_NOT_SUPPORTED = "Eh? Usa il comando /help per avere informazioni.";
