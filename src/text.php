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

const ANSWER_NO_QUIZ = "Non ci sono quiz attivi al momento.";
const ANSWER_NO_QUIZ_ADMIN = "Attiva un nuovo quiz col comando /new.";
const ANSWER_CORRECT = "Giusto! Hai risposto correttamente come il %PERCENT_CORRECT%% di partecipanti. 👍";
const ANSWER_WRONG = "Sbagliato! La risposta corretta era “%CORRECT_ANSWER%”. 😞";

const ANSWER_ACCEPTED = "Ok, ho registrato la tua risposta. Vedi il risultato sul canale @" . LIVE_CHANNEL_ID . ".";

const START_UNKNOWN_PAYLOAD = "Ops, non conosco questo codice.";
const START_ALREADY_ANSWERED = "Hai già risposto a questo quesito (la tua risposta è “%ANSWER%”).";
const START_RECOGNIZED = "Scrivi qui la tua risposta al quesito.";

const RESET_OK = "All is forgotten.";

const COMMAND_UNKNOWN = "Comando non riconosciuto.";

const MESSAGE_NOT_SUPPORTED = "Eh? Usa il comando /help per avere informazioni.";
