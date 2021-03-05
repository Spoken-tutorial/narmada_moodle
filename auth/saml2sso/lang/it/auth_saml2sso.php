<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'auth_saml2sso', language 'it'.
 *
 * @package auth_saml2sso
 * @author Marco Ferrante, AulaWeb/University of Genoa <staff@aulaweb.unige.it>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['auth_saml2ssodescription']                 = 'Autentica gli utenti tramite SAML 2.0';
$string['pluginname']                               = 'SAML2 SSO Auth';
$string['settings_saml2sso']                        = '';

//label config strings
$string['label_button_url']                         = 'URL icona';
$string['label_button_name']                        = 'Etichetta bottone';
$string['label_sp_path']                            = 'Percorso librerie SimpleSAMLphp';
$string['label_dual_login']                         = 'Dual login';
$string['label_single_signoff']                     = 'Single Sign Off';
$string['label_idpattr']                            = 'Attributo ' . get_string('username');
$string['label_moodle_mapping']                     = 'Campo identificativo';
$string['label_autocreate']                         = 'Creazione automatica utente';
$string['label_authsource']                         = 'Nome sorgente autenticazione SP';
$string['label_logout_url_redir']                   = 'URL di logout';
$string['label_logout']                             = 'Disconnessione';
$string['label_edit_profile']                       = 'L\'utente può modificarsi il profilo?';
$string['label_field_idp_firstname']                = 'Attributo IdP del nome';
$string['label_field_idp_lastname']                 = 'Attributo IdP del cognome';
$string['label_field_idp_fullname']                 = 'Nome completo dall\'IdP?';
$string['label_instructions_title']                 = 'Istruzioni';
$string['label_instructions_p1']                    = '<p>La mappatura è richiesta per i campi:</p><ul><li>Nome => givenName</li><li>Cognome => surname</li><li>Indirizzo email => email</li></ul><p>Puoi cambiarla dall\'array <code>$stringMapping</code> in <code>auth.php</code></p>';
$string['label_allow_empty_email']                  = 'Accetta email nulle';
$string['label_session_control']                    = 'Applica limite di sessioni';

//_help config strings
$string['help_allow_empty_email']                   = 'Permette all\'IdP/ADFS di non fornire il valore Email o Mail. All\'utente verrà richiesto di completare il profilo';
$string['help_button_url']                          = 'URL dell\'icona da usare sul bottone di login. Massimo 50 pixel di altezza';
$string['help_button_name']                         = 'Etichetta per il bottone di login';
$string['help_sp_path']                             = 'Percorso assoluto dell\'installazione di SSP. Es.: /var/www/simplesamlphp/';
$string['help_dual_login']                          = 'Mostra all\'utente la maschera di login di Moodle';
$string['help_single_signoff']                      = 'Il logout da Moodle attiva anche il logout dall\'IdP e dalla sessione di Single SignOn';
$string['help_idpattr']                             = 'L\'attributo ricevuto dall\'IdP da usare come ' . get_string('username') . ' in Moodle.';
$string['help_moodle_mapping']                      = 'Il campo del profilo Moodle con cui cercare l\'utente. Se \'' .
        get_string('idnumber') . '\', ricordarsi di mapparlo nelle impostazioni più sotto';
$string['help_autocreate']                          = 'Crea l\'utente Moodle all\'accesso se non presente';
$string['help_authsource']                          = 'Nome della sorgente di autenticazione del Service Provider, come registrata in /config/authsources.php';
$string['help_logout_url_redir']                    = 'URL a cui ridirigere dopo il logout. Se non è valido o vuoto, si verrà rediretti alla pagina principale di Moodle. (es.: https://go.to/another/url)';
$string['nouser']                                   = 'Non c\'è un utente Moodle con l\'id restituito e la creazione automatica è disabilitata. L\'id restituito è: ';
$string['help_edit_profile']                        = 'Se gli utenti non possono modificare il proprio profilo, non vedranno il link al profilo';
$string['help_field_idp_firstname']                 = '<strong>deprecato, usare una authproc</strong> Attributo ricevuto dall\'IdP contenente il nome' ;
$string['help_field_idp_lastname']                  = '<strong>deprecato, usare una authproc</strong> Attributo ricevuto dall\'IdP contenente il cognome';
$string['help_field_idp_fullname']                  = '<strong>deprecato, usare una authproc</strong> Il nome completo è restituito dall\'IdP in un campo unico (es. cn)? Se sì, indicarlo sotto in entrambi gli attributi per il nome e il cognome';
$string['help_session_control']                     = 'Se l\'opzione  \'' 
                                                    . (new lang_string('limitconcurrentlogins', 'core_auth'))->out('it')
                                                    . '\' è impostato a 1, viene rispettata per gli utenti non amministratori.';

//error config strings
$string['error_create_user']                        = 'Errore nella creazione del profilo Moodle. Contattare l\'amministratore.';
$string['error_sp_path']                            = 'Il percorso delle librerie SimpleSAMLphp dev\'essere specificato nella configurazione';
$string['error_idpattr']                            = 'Un attributo id dev\'essere specificato';
$string['error_authsource']                         = 'Una sorgente di autenticazione dev\'essere specificata';
$string['error_field_idp_firstname']                = 'L\'attributo per il nome è obbligatorio';
$string['error_field_idp_lastname']                 = 'L\'attributo per il cognome è obbligatorio';
$string['error_lockconfig_field_map_firstname']     = 'La mappatura del nome è obbligatoria';
$string['error_lockconfig_field_map_lastname']      = 'La mappatura del cognome è obbligatoria';
$string['error_lockconfig_field_map_email']         = 'La mappatura dell\'Indirizzo email è obbligatoria';
$string['error_novalidemailfromidp']                = 'Il tuo Identity Provider non fornisce un indirizzo email valido';
$string['error_you_are_still_connected']            = 'Sei ancora connesso a una sessione SSO';
$string['error_nokey']                              = 'L\'Identity Provider non ha fornito un attributo necessario per identificarti';

$string['success_config']                           = 'La configurazione è stata salvata correttamente';

$string['label_profile_settings']                   = 'Attributi SAML e profilo utente';

$string['label_dual_login_settings']  = 'Login multiplo';
$string['label_dual_login_help']   = '
Il default del Dual login è disattivo e gli utenti vengono rediretti all\'IdP o al servizio di
discovery configurato nella sorgente di autenticazione SimpleSAMLphp.<br />
In questo caso per usare il login standard di Moodle occorre aggiungere il parametro saml=off. Es.: /login/index.php?saml=off<br />
Attivano il Dual login l\'utente deve scegliere il metodo di autenticazione.';
$string['label_sync_settings']        = 'Sincronizzazione utenti';
$string['label_sync_settings_help']   = '
Un IdP SAML non può fornire un elenco di utenti da sincronizzare, ma può 
appoggiarsi ad un backend LDAP / DB da cui possono essere letti.
La configurazione deve quindi essere impostata dal plugin di autenticazione della sorgente';
$string['label_user_directory']          = 'Sorgente utenti';
$string['help_user_directory']           = 'Un plugin di autenticazione in grado di elencare gli utenti';
$string['label_do_update']            = 'Aggiorna profili';
$string['help_do_update']             = 'Aggiorna i campi dei profili degli utenti esistenti
con i dati della sorgente utenti. Se "no", verranno solo creati localmente i nuovi
utenti nella sorgente utenti. Se "sì", la sincronizzazione potrebbe sovrascrive
i valori aggiornati dall\'IdP all\'ultimo login utente';
$string['label_verbose_sync']        = 'Mostra report';
$string['help_verbose_sync']         = 'Attiva il report dettagliato';

$string['synctask']        = 'Sincronizzazione utenti';

$string['label_hide_takeover_page']       = 'Nascondi pagina di importazione';
$string['help_hide_takeover_page']        = '
La voce della pagina di importazione appare nel menu di amministrazione
solo se ci sono utenti gestiti da altri plugin di autenticazione che
possono essere rilevati.
Può essere fastidiosa se si intende lasciare questi utenti così come sono.';

$string['takeover']             = 'Migrazione utenti a ' . $string['pluginname'];
$string['label_takeover_link']  = '
Ci sono ancora utenti gestiti da plugin compatibili con questo.
Vuoi <a href="{$a}">importarli<a>?';
$string['label_takeover']       = 'Rileva utenti esistenti';
$string['help_takeover']        = '
Gli utenti gestiti dai plugin elencati sotto possono essere convertiti
per autenticarsi con ' . $string['pluginname'] . '.
<br />Gli utenti cancellati non saranno migrati.';
$string['label_takeover_plugin']            = '{$a->auth} (attivi {$a->count} utenti)';
$string['label_takeover_unknown_plugin']    = 'Plugin "{$a->auth}" cancellato o corrotto (attivi {$a->count} utenti)';

$string['takeover_nouser']      = 'Nessun plugin selezionato o selezionati plugin senza utenti';
$string['takeover_completed']   = 'Utenti migrati';
$string['takeover_submit']      = 'Converti a ' . $string['pluginname'];
$string['takeover_count_migrated']      = '{$a->count} utenti importati da {$a->auth}<br />';
$string['event_user_migrate']       = 'Utente importato';
$string['event_user_migrate_desc']  = 'L\'utente è stato convertito per usare ' . $string['pluginname'];
$string['event_not_searchable']         = 'Utente SSO non identificabile';
$string['event_not_searchable_desc']    = 'L\'IdP non ha fornito l\'attributo richiesto per cercare l\'utente';
$string['event_user_kicked_off']        = 'Annullate vecchie sessioni';
$string['event_user_kicked_off_desc']   = 'L\'utente ha attivato una nuova sessione Moodle mentre il limite di autenticazioni contemporanee era attivo: le sessioni vecchie sono state eliminate e i dati non salvati ignorati';

$string['privacy:metadata'] = 'Il plugin di autenticazione SAML2 SSO non registra alcun dato personale.';
