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

const SESSION_NEW_ASK = "Stai per generare una nuova sessione di gioco. Vuoi continuare?";
const SESSION_NEW_CONFIRM = "Ok, nuova sessione di gioco attiva. Le statistiche di gioco faranno riferimento alla session in atto.";

const COMMAND_HELP = "Ciao, sono <b>Quizzle</b>, un bot che gestisce dei quiz multiutente. Puoi registrarti come gruppo col comando /register. Se ci sono quiz attivi, avrai ricevuto un codice o un <i>QR Code</i> che puoi utilizzare per rispondere. Ti informerò delle risposte corrette e dei risultati.\n\nIl mio codice è <a href=\"https://github.com/CodeMOOC/QuizzleBot\">disponibile su Github</a>.";

const ANSWER_NO_QUIZ = "Digita il codice del quiz per partecipare (oppure scansiona il QR Code).";
const ANSWER_NO_QUIZ_ADMIN = "Nessun quiz attivo. Attiva un nuovo quiz col comando /new.";
const ANSWER_CORRECT = "<b>Giusto!</b> Hai risposto correttamente come il %PERCENT_CORRECT%% di partecipanti. 👍";
const ANSWER_WRONG = "<b>Sbagliato!</b> La risposta corretta era “%CORRECT_ANSWER%”. 😞 (Il %PERCENT_CORRECT% dei partecipanti ha risposto correttamente.)";

const ANSWER_ACCEPTED = "Ok, ho registrato la tua risposta. Vedi il risultato sul canale " . LIVE_CHANNEL_ID . ".";

const START_UNKNOWN_PAYLOAD = "Ops, non riconosco questo codice. 😕\nDigita il codice del quesito (o scansiona il QR Code).";
const START_ALREADY_ANSWERED = "Hai già risposto a questo quesito (la tua prima risposta era “%ANSWER%”).";
const START_RECOGNIZED = "Scrivi qui la tua risposta al quesito.";

const RESET_OK = "Tutto dimenticato.";

const REGISTER_QUERY_CONFIRM = "Vuoi che ti registri come gruppo?";
const REGISTER_AFFIRMATIVE = array('si', 'sì', 'ok', 'certo', 'va bene', 'bene');
const REGISTER_QUERY_NAME = "Ok, %FIRST_NAME%. Qual è il nome del tuo gruppo?";
const REGISTER_QUERY_PARTICIPANTS = "In quanti siete? (Incluso te.)";
const REGISTER_QUERY_OK = "Perfetto. 👍";
const REGISTER_RESET= "Ok, %FIRST_NAME%. Ti considererò come giocatore individuale.";
const REGISTER_INVALID_NAME = "Non mi sembra un nome corretto.";
const REGISTER_INVALID_COUNT = "Specifica un numero valido in cifre.";
const REGISTER_TOO_HIGH_COUNT = "Siete un po' troppi, secondo me…";

const CHANNEL_NEW_RIDDLE = "📢 Nuovo quesito! <a href=\"" . TELEGRAM_DEEP_LINK_URI_BASE . "%PAYLOAD%\">Rispondi al bot</a>.";
const CHANNEL_FINAL = "🏁 <b>Quesito chiuso</b> (%CODE%)\nTotale risposte: <b>%TOTAL_COUNT%</b> (👥 Partecipanti: <b>%TOTAL_PARTICIPANTS%</b>)\nRisposte corrette: <b>%PERCENT_CORRECT%%</b>\n\n<b>Prime risposte corrette:</b>";
const CHANNEL_CORRECT_ANSWER = "La risposta corretta era “%ANSWER%”. ✅";

const GENERIC_NOT_SUPPORTED = "Eh? Usa il comando /help per avere informazioni.";
const GENERIC_NOT_ADMIN = "Non sei registrato come amministratore, questo comando non è disponibile. Spiacente. 😞";
const GENERIC_NEVERMIND = "No problem.";
