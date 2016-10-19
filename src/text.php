<?php
/*
 * CodeMOOC QuizzleBot
 * ===================
 * UWiClab, University of Urbino
 * ===================
 * Text strings.
 */

const QUIZ_CREATED_OK = "Nuovo quiz attivo.";
const QUIZ_ALREADY_OPEN = "Quiz già attivo: all'invio della risposta corretta il quiz corrente viene chiuso e le risposte vengono consegnate.";

const COMMAND_HELP = "Ciao, sono un semplice bot per gestire dei quiz. Se ci sono quiz attivi puoi registrare direttamente la tua risposta qui, scrivendomi. Quando il quiz verrà chiuso, ti scriverò la risposta corretta e terrò traccia del tuo punteggio!";

const ANSWER_NO_QUIZ = "Non ci sono quiz attivi al momento.";
const ANSWER_NO_QUIZ_ADMIN = "Attiva un nuovo quiz col comando /new.";
const ANSWER_CORRECT = "Giusto! La tua risposta è stata la %INDEX%° ad essere registrata. 👍";
const ANSWER_WRONG = "Sbagliato! La risposta corretta era “%CORRECT_ANSWER%”. 😞";

const ANSWER_ACCEPTED = "Ok, ho registrato la tua risposta.";

const COMMAND_UNKNOWN = "Comando non riconosciuto.";

const MESSAGE_NOT_SUPPORTED = "Eh? Usa il comando /help per avere informazioni.";
