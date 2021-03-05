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
 * @package auth_saml2sso
 * @author Daniel Miranda <daniellopes at gmail.com>
 * @author Marco Ferrante, AulaWeb/University of Genoa <staff@aulaweb.unige.it>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * Parts of the code was original made for another moodle plugin available at
 * https://moodle.org/plugins/auth_saml2
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/auth/saml2sso/locallib.php');

/**
 * Plugin for authentication using SimpleSAMLphp Service Provider
 * For SimpleSAMLphp instructions, go to https://simplesamlphp.org/
 */
class auth_plugin_saml2sso extends auth_plugin_base {

    /**
     * The name of the component. Used by the configuration.
     */
    const COMPONENT_NAME = \auth_saml2sso\COMPONENT_NAME;
    /**
     * Legacy name of the component.
     */
    const LEGACY_COMPONENT_NAME = 'auth/saml2sso';

    /**
     * Config vars
     * @var string
     */
    public $defaults = array(
        'sp_path' => '',
        'dual_login' => 1,
        'single_signoff' => 1,
        'idpattr' => '',
        'moodle_mapping' => 'username',
        'autocreate' => 0,
        'authsource' => '',
        'logout_url_redir' => '',
        'edit_profile' => 0,
        'allow_empty_email' => 0,
        'field_idp_fullname' => 1,
        'field_idp_firstname' => 'cn',
        'field_idp_lastname' => 'cn',
        'delete_if_empty' => false,  // Delete the profile field value if the correspondig attribute is missing/empty
    );

    /**
     * Mapping vars
     * @var string
     */
    public static $stringmapping = array(
        'email' => 'email',
        'idnumber' => 'idnumber',
        'firstname' => 'givenName',
        'lastname' => 'surname'
    );

    /**
     * Constructor
     */
    public function __construct() {
        $this->authtype = \auth_saml2sso\AUTH_NAME;
        $componentName = (array) get_config(self::COMPONENT_NAME);
        $legacyComponentName = (array) get_config(self::LEGACY_COMPONENT_NAME);
        $this->config = (object) array_merge($this->defaults, $componentName, $legacyComponentName);
        if (empty($this->config->authsource)) {
            // Uses old entityid key.
            $this->config->authsource = $this->config->entityid;
            debugging('authsource config key empty, using old entityid key', DEBUG_DEVELOPER);
        }
        $this->mapping = (object) self::$stringmapping;
    }

    /**
     * Load SimpleSAMLphp library autoloader
     * 
     * @since 3.6.0 Dropped support for non namespaced functions
     */
    private function getsspauth() {
        require_once($this->config->sp_path . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '_autoload.php');

        return new \SimpleSAML\Auth\Simple($this->config->authsource);
    }

    /**
     * Makes the saml2 plugin appear as a idsp on login screen
     * @param string $wantsurl
     * @return array
     * Added by Praxis
     */
    public function loginpage_idp_list($wantsurl) {
        $url = '?saml=on';

        if (!empty($this->config->button_url)) {
            $button_path = new moodle_url($this->config->button_url);
        } else {
            $button_path = new moodle_url('/auth/saml2sso/pix/login-btn.png');
        }
        $button_name = 'SAML Login';
        if (!empty(trim($this->config->button_name))) {
                $button_name = (new moodle_url($this->config->button_name))->out();
        }

        return [[
            'url' => new moodle_url($url),
            'name' => $button_name,
            'iconurl' => $button_path
        ]];
    }

    protected function get_attribute_mapping($username_attribute = null) {
        $configarray = (array) $this->config;

        $moodleattributes = array();
        $userfields = array_merge($this->userfields, $this->get_custom_user_profile_fields());
        foreach ($userfields as $field) {
            if (isset($configarray["field_map_$field"])) {
                $moodleattributes[$field] = $configarray["field_map_$field"];
            }
        }

        if ($username_attribute) {
            $moodleattributes['username'] = $username_attribute;
        }

        return $moodleattributes;
    }

    /**
     * Read user information from the current simpleSAMLphp session.
     *
     * @param string $username username, if not the current SSO user, returns false
     *
     * @return mixed array with no magic quotes or false on error
     */
    public function get_userinfo($username) {
        $auth = $this->getsspauth();
        if (!$auth->isAuthenticated()) {
            return false;
        }

        $attributes = $auth->getAttributes();
        $uid = trim(core_text::strtolower($attributes[$this->config->idpattr][0]));
        if (core_text::strtolower($username) != $uid) {
            // Not the current user.
            return false;
        }

        $attrmap = $this->get_attribute_mapping($this->config->idpattr);

        $result = array();
        foreach ($attrmap as $key => $value) {
            // Check if attribute is present.
            if (empty($attributes[$value])) {
                // If the IdP aggregate different information sources, an attribute
                // can be missing due to a temporary problem. It is unreasobable
                // deleting the old value.
                if ($this->config->delete_if_empty) {
                    $result[$key] = '';
                }
                continue;
            }

            // Make usename lowercase
            if ($key == 'username'){
                $result[$key] = strtolower($attributes[$value][0]);
            }
            else {
                $result[$key] = $attributes[$value][0];
            }
        }

        return $result;
    }

    /**
     * @global string $SESSION
     * @return type
     */
    public function loginpage_hook() {
        global $SESSION;

        if(!isset($SESSION->saml)){
            $SESSION->saml = '';
        }

        $saml = optional_param('saml', $SESSION->saml, PARAM_TEXT);

        // Check if dual login is enabled.
        // Can bypass IdP auth.
        // To bypass IdP auth, go to <moodle-url>/login/index.php?saml=off
        // Thanks to Henrik Sune Pedersen.
        if ((int) $this->config->dual_login && $saml !== 'on') {
            $saml = 'off';
        }

        // If saml=off, go to default login page regardless any other
        // settings. Useful to administrators to recover from misconfiguration
        if ($saml == 'off') {
            $SESSION->saml = 'off';
            return;
        }

        // If dual login is disabled or saml=on, the user is redirect to the IdP
        if ($saml == 'on') {
            $SESSION->saml = 'on';
            $this->saml2_login();
        }

        // Otherwise, is checked the last option in session.
        if (!empty($SESSION->saml) && $SESSION->saml == 'off') {
            return;
        }
        if ($this->config->dual_login) {
            return;
        }
        $this->saml2_login();
    }

    /**
     * Called when user hit the logout button
     * Will get the URL from the logged in IdP if Single Sign Off is setted
     * and then redirect to config logout URL setted up in plugin config
     * If URL is invalid or empty, redirect to Moodle main page
     */
    public function logoutpage_hook() {
        global $CFG, $USER;

        if ($USER->auth != $this->authtype) {
            // SingleLogOut must not be called for user handled by other plugins.
            return;
        }

        $urllogout = filter_var($this->config->logout_url_redir, FILTER_VALIDATE_URL) ? $this->config->logout_url_redir : $CFG->wwwroot;

        // Check if we need to sign off users from IdP too
        if ((int) $this->config->single_signoff) {
            $auth = $this->getsspauth();

            $urllogout = $auth->getLogoutURL($urllogout);
        }

        require_logout();

        redirect($urllogout);
    }

    /**
     * Do all the magic during SSO login procedure
     * @global type $DB
     * @global type $USER
     * @global type $CFG
     */
    public function saml2_login() {
        global $DB, $USER, $CFG;

        $auth = $this->getsspauth();
        $param = ['KeepPost' => true];
        
        // Admins can have multiple sessions.
        $apply_session_control = !is_siteadmin($USER->id)
                && $this->config->session_control
                && $CFG->limitconcurrentlogins == 1;
        if ($apply_session_control) {
            // Force a reauthentication even if a SSO session is active in the SP.
            // Throw away the POST values because after reauthentication user must
            // fill the form again: session control is used in exams or similar
            // situation, if we keep the POST data, cheating is still possible.
            $param = ['ForceAuthn' => true, 'KeepPost' => false];
        }
        // Retrieve the Moodle session ID from the local SSO session data
        $sspsession = \SimpleSAML\Session::getSessionFromRequest();
        $prevmoodlesession = $sspsession->getData('\Moodle\\' . \auth_saml2sso\COMPONENT_NAME, 
            'moodle:session'
        );

        // Moodle session changed within the same local SSO session.
        if (!empty($prevmoodlesession) && $prevmoodlesession != session_id()) {
            if ($apply_session_control) {
                $event = \auth_saml2sso\event\user_kicked_off::create(array());
                $event->trigger();
            }
            $sspsession->deleteData('\Moodle\\' . \auth_saml2sso\COMPONENT_NAME, 
                'moodle:session'
            );
            $auth->login($param);
        }
        else {
            $auth->requireAuth($param);
        }

        // Save the Moodle session ID in the local SSO session data.
        $sspsession->setData('\Moodle\\' . \auth_saml2sso\COMPONENT_NAME, 
            'moodle:session',
            session_id()
        );
            
        $attributes = $auth->getAttributes();

        // Email attribute
        // here we insure that e-mail returned from identity provider (IdP) is catched
        // whenever it is email or mail attribute name.
        if (isset($attributes['email'])) {
            $attributes[$this->mapping->email][0] = core_text::strtolower(trim($attributes['email'][0]));
        } else if (isset($attributes['mail'])) {
            $attributes[$this->mapping->email][0] = core_text::strtolower(trim($attributes['mail'][0]));
        } else if (!$this->config->allow_empty_email) {
            $this->error_page(get_string('error_novalidemailfromidp', self::COMPONENT_NAME));
        }
        // if $this->config->allow_empty_email is true and the IdP don't provide an
        // email address, the user is redirect to the profile page to complete.

        // If the field containing the user's name is a unique field, we need to break
        // into firstname and lastname.
        if ((int) $this->config->field_idp_fullname) {
            // First name attribute
            $attributes[$this->mapping->firstname][0] = strstr($attributes[$this->config->field_idp_firstname][0], " ", true)
                            ? core_text::strtoupper(trim(strstr($attributes[$this->config->field_idp_firstname][0], " ", true)))
                            : core_text::strtoupper(trim($attributes[$this->config->field_idp_firstname][0]));
            // Last name attribute
            $attributes[$this->mapping->lastname][0] = strstr($attributes[$this->config->field_idp_lastname][0], " ")
                            ? core_text::strtoupper(trim(strstr($attributes[$this->config->field_idp_lastname][0], " ")))
                            : core_text::strtoupper(trim($attributes[$this->config->field_idp_lastname][0]));
        } else {
            $attributes[$this->mapping->firstname][0] = trim($attributes[$this->config->field_idp_firstname][0]);
            $attributes[$this->mapping->lastname][0] = trim($attributes[$this->config->field_idp_lastname][0]);
        }

        // User Id returned from IdP
        // Will be used to get user from our Moodle database if exists
        // create_user_record lowercases the username, so we need to lower it here.
        $uid = trim(core_text::strtolower($attributes[$this->config->idpattr][0]));

        // Now we check if the key returned from IdP exists in our Moodle database
        $attributes = $this->get_userinfo($uid);
        if (!isset($attributes[$this->config->moodle_mapping])) {
            $event = \auth_saml2sso\event\not_searchable::create(array());
            $event->trigger();
            $this->error_page(get_string('error_nokey', \auth_saml2sso\COMPONENT_NAME));
        }
        $criteria = array($this->config->moodle_mapping => $attributes[$this->config->moodle_mapping]);
        $isuser = $DB->get_record('user', $criteria);

        $newuser = false;
        if (!$isuser) {
            // Verify if user can be created
            if ((int) $this->config->autocreate) {
                // Insert new user
                $isuser = create_user_record($uid, '', $this->authtype);
                $newuser = true;
            } else {
                //If autocreate is not allowed, show error
                $this->error_page(get_string('nouser', self::COMPONENT_NAME) . $uid);
            }
        }

        /**
         * We expected that here we have a existing user or a new one
         */
        if ($isuser) {
            $USER = get_complete_user_data('username', $isuser->username);
        } else {
            $this->error_page(get_string('error_create_user', self::COMPONENT_NAME));
        }

        $isuser = update_user_record_by_id($isuser->id);

        // now we get the URL to where user wanna go previouly
        $urltogo = core_login_get_return_url();

        // and pass to login method
        $this->do_login($isuser, $urltogo);
    }

    /**
     * Do Moodle login will set session and cookie to authenticated user
     * @global type $USER
     * @global type $CFG
     * @param type $urltogo
     */
    protected function do_login($user, $urltogo) {
        global $USER, $CFG;

        $USER = complete_user_login($user);
        $USER->loggedin = true;
        $USER->site = $CFG->wwwroot;
        set_moodle_cookie($USER->username);

        $apply_session_control = !is_siteadmin($USER->id)
                && $this->config->session_control
                && $CFG->limitconcurrentlogins == 1;
        if ($apply_session_control) {
            // https://tracker.moodle.org/browse/MDL-62753?jql=text%20~%20%22session%20kill%22
            // moodle\auth\shibboleth\classes\helper.php

            // Honour limit Concurrent Logins.
            // https://moodle.org/mod/forum/discuss.php?d=387784
            \core\session\manager::apply_concurrent_login_limit($user->id, session_id());
        }
        
        // If we are not on the page we want, then redirect to it.
        if (qualified_me() !== $urltogo) {
            redirect($urltogo);
            exit;
        }
    }

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function auth_plugin_saml2sso() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Returns true if the username and password work or don't exist and false
     * if the user exists and the password is wrong.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password) {
        return false;
    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     *
     */
    public function user_update_password($user, $newpassword) {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function prevent_local_passwords() {
        return true;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    public function can_change_password() {
        return false;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    public function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    public function can_reset_password() {
        return false;
    }

    /**
     * The plugin can be manually set in csv import.
     *
     * @return bool
     */
    public function can_be_manually_set() {
        return true;
    }

    /**
     * @return type
     */
    public function can_edit_profile() {
        return (int) $this->config->edit_profile;
    }

    /**
     * In loginpage_hook() the page rendering is not active yet, so is not
     * enoght to throw an Exception.
     * Probably should be better to invoke Moodle fatal_error() function
     * instead of rewriting it, but Moodle docs discourage this.
     *
     * @global type $PAGE
     * @global type $OUTPUT
     * @global type $SITE
     * @param type $msg
     */
    protected function error_page($msg) {
        global $PAGE, $OUTPUT, $SITE;

        $auth = $this->getsspauth();

        $urltogo = $this->config->logout_url_redir;
        if (empty($urltogo)) {
            $urltogo = (new moodle_url('/'))->out();
        }
        $logouturl = $auth->getLogoutURL($urltogo);

        $PAGE->set_course($SITE);
        $PAGE->set_url('/');
        $PAGE->set_title(get_string('error') . ' - ' . $msg);
        $PAGE->set_heading($PAGE->course->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->box($msg, 'errorbox alert alert-danger', null, array('data-rel' => 'fatalerror'));
        echo $OUTPUT->box(get_string('error_you_are_still_connected', self::COMPONENT_NAME)
                . ' <a href="' . $logouturl . '">'
                . get_string('label_logout', self::COMPONENT_NAME) . '</a>', 'errorbox alert');
        echo $OUTPUT->footer();
        exit;
    }

    /**
     * Test if settings are correct, print info to output.
     * @author Marco Ferrante <marco at csita.unige.it>
     */
    public function test_settings() {
        global $OUTPUT;

        // NOTE: this is not localised intentionally, admins are supposed to understand English at least a bit...

        if (empty($this->config->sp_path)) {
            echo $OUTPUT->notification('SimpleSAMLphp lib path not set', \core\output\notification::NOTIFY_ERROR);
            return;
        }
        if (!empty(getenv('SIMPLESAMLPHP_CONFIG_DIR')) && $this->config->sp_path != dirname(getenv('SIMPLESAMLPHP_CONFIG_DIR'))) {
            echo $OUTPUT->notification('SimpleSAMLphp lib path differs from the environment default ('
                    . dirname(getenv('SIMPLESAMLPHP_CONFIG_DIR'))
                    . '): it could be fine, but check if the library has been updated', \core\output\notification::NOTIFY_WARNING);
        }
        if (!file_exists($this->config->sp_path . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '_autoload.php') || !file_exists($this->config->sp_path . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php')) {
            echo $OUTPUT->notification('SimpleSAMLphp lib path seems to be invalid', \core\output\notification::NOTIFY_ERROR);
            return;
        }

        require($this->config->sp_path . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '_autoload.php');
        $sspconfig = \SimpleSAML\Configuration::getInstance();
        if (version_compare($sspconfig->getVersion(), '1.18.6') < 0) {
            echo $OUTPUT->notification('SimpleSAMLphp lib seems too old ('
                    . $sspconfig->getVersion() . ') and insecure, consider to upgrade it', \core\output\notification::NOTIFY_WARNING);
        } else {
            echo $OUTPUT->notification('SimpleSAMLphp version is ' . $sspconfig->getVersion(), \core\output\notification::NOTIFY_INFO);
        }
        
        @include($this->config->sp_path . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        if ($config['store.type'] == 'phpsession') {
            echo $OUTPUT->notification('It seems SimpleSAMLphp uses default PHP session storage, it could be troublesome: switch to another store.type in config.php', \core\output\notification::NOTIFY_INFO);
        }

        $sourcesnames = array_map(function($source){
            return $source->getAuthId();
        }, \SimpleSAML\Auth\Source::getSourcesOfType('saml:SP'));
        if (empty($this->config->authsource) || !in_array($this->config->authsource, $sourcesnames)) {
            echo $OUTPUT->notification('Invalid authentication source. Available sources: '
                    . implode(', ', $sourcesnames), \core\output\notification::NOTIFY_WARNING);
            return;
        }

        $auth = $this->getsspauth();
        if (empty($auth)) {
            echo $OUTPUT->notification('SimpleSAMLphp library loading failed!', \core\output\notification::NOTIFY_WARNING);
            return;
        }

        if (empty($this->config->idpattr)) {
            echo $OUTPUT->notification('The attribute from the IdP to use as Moodle Username is not set',
                    \core\output\notification::NOTIFY_WARNING);
        }
        else {
            $attrmap = $this->get_attribute_mapping($this->config->idpattr);
            if (empty($attrmap[$this->config->moodle_mapping])) {
                echo $OUTPUT->notification('The user will be search by ' . $this->config->moodle_mapping
                        . ' but no attribute from the IdP is map to this field',
                        \core\output\notification::NOTIFY_WARNING);
            }
        }

        if (!empty($this->config->user_directory)) {
            $plugin = get_auth_plugin($this->config->user_directory);
            if (!$plugin) {
                echo $OUTPUT->notification('Invalid directory plugin \''
                        . $this->config->user_directory . '\'', \core\output\notification::NOTIFY_WARNING);
            }
            if (method_exists($plugin, 'test_settings')) {
                $options[$this->config->user_directory] = get_string('pluginname', 'auth_'.$this->config->user_directory);
                $url = new moodle_url('/auth/test_settings.php', array('sesskey'=>sesskey(), 'auth' => $this->config->user_directory));
                echo $OUTPUT->notification('A sync process with \'' . get_string('pluginname', 'auth_'.$this->config->user_directory)
                        . '\' auth plugin is enabled. <a href="' . $url
                        . '">Check its configuration</a>.', \core\output\notification::NOTIFY_INFO);

            }
            else {
                echo $OUTPUT->notification('A sync process with \'' . get_string('pluginname', 'auth_'.$this->config->user_directory)
                        . '\' auth plugin is enabled. Please check its configuration too.', \core\output\notification::NOTIFY_INFO);
            }
        }

        if (!$this->config->edit_profile && $this->config->allow_empty_email) {
            echo $OUTPUT->notification('The plugin accepts SAML assertion with empty '
                    . 'e-mail address, but the user is not enabled to edit '
                    . 'his profile to add it by himself. Users without e-mail will be locked out by Moodle.',
                    \core\output\notification::NOTIFY_WARNING);
        }

        if ($this->config->field_idp_fullname) {
            echo $OUTPUT->notification('The feature <tt>field_idp_fullname</tt> of splitting the full '
                    . 'name into the first and the last names '
                    . 'is deprecated and will be removed in the future. '
                    . 'Use an authproc in the SimpleSAMLphp config to achieve the same result.',
                    \core\output\notification::NOTIFY_WARNING);
        }

        echo $OUTPUT->notification('Everything seems ok', \core\output\notification::NOTIFY_SUCCESS);
    }

}
