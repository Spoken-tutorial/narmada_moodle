<?php
/**
 * Common functions
 *
 * @package auth_saml2sso
 * @copyright  2018 Marco Ferrante
 * @author Marco Ferrante <marco at csita.unige.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_saml2sso;

defined('MOODLE_INTERNAL') || die;

const AUTH_NAME = 'saml2sso';
const COMPONENT_NAME = 'auth_' . AUTH_NAME;

// Known auth mechanisms based on Moodle internal
// Only auth mechanism in which the username is handle from a central "istitutional" 
// backend can be converted to SSO
const LOCAL_AUTH_PLUGINS = [
    'cas' => false,
    'db' => false,
    'email' => true,
    'ldap' => false,
    'lti' => true,
    'manual' => true,
    'mnet' => true,
    'nologin' => true,
    'none' => true,
    'oauth2' => true,
    'oidc' => true,
    'shibboleth' => false,
    'webservice' => true,
];

require_once 'classes/event/user_migrated.php';

/**
 * An helper to test if a plugin can sync users.
 *
 * @param type $plugin An auth plugin
 * @return bool true if $plugin can sync users
 */
function can_sync_user($plugin) {
    if ($plugin instanceof \auth_plugin_base
            && method_exists($plugin, 'sync_users')) {
        // Check argument number?
        return true;
    }

    return false;
}

function get_known_plugin($knownauthplugins = LOCAL_AUTH_PLUGINS) {
    global $DB;

    $authsavailable = \core_component::get_plugin_list('auth');

    $fields = [];

    // Check for authsources assigned in user table, even if the plugin is
    // not present. This cope with unavailable plugins (eg. incomptabile ones)
    $usedsauth = $DB->get_records_sql_menu('SELECT DISTINCT auth, COUNT(auth) FROM {user} WHERE deleted=0 GROUP BY auth');
    foreach ($usedsauth as $auth => $count) {
        if ($auth == AUTH_NAME) {
            // Skip itself.
            continue;
        }

        if (!empty($knownauthplugins[$auth])) {
            continue;
        }

        if (empty($authsavailable[$auth])) {
            $fields[$auth] = ['auth' => null, 'count' => $count];
            continue;
        }

        $authplugin = \get_auth_plugin($auth);
        $fields[$auth] = ['auth' => $authplugin->get_title(), 'count' => $count];
    }

    return $fields;
}

/**
 * Migrate users.
 *
 * @param type $auth Plugin to migrate
 * @return int the number of user migrate or false in case of error
 */
function takeover($auth) {
    global $DB;
    
    $known_plugins = get_known_plugin();
    if (empty($known_plugins[$auth])) {
        // Could not migrate.
        debugging('user belongin to ' . $auth . ' cannot migrate', DEBUG_NORMAL);
        return false;
    }

    $users = $DB->get_records('user', array('auth'=>$auth, 'deleted'=>0));
    if (count($users) == 0) {
        debugging('no user authenticate by ' . $auth . ' to migrate', DEBUG_NORMAL);
        return false;
    }

    $count = 0;
    foreach ($users as $userid => $user) {
        if (!\core_user::is_real_user($userid)) {
            // Admin.
            continue;
        }
        $user->auth = AUTH_NAME;
        user_update_user($user, false, false);
        $event = \auth_saml2sso\event\user_migrated::create(array(
            'userid' => $user->id
        ));
        $event->trigger();

        $count++;
    }

    return $count;
}