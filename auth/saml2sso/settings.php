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
 * Admin settings and defaults
 *
 * @package auth_saml2sso
 * @copyright  2017 Stephen Bourget
 * @author Daniel Miranda <daniellopes at gmail.com>
 * @author Marco Ferrante <marco at csita.unige.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/auth/saml2sso/locallib.php'); // require_once('locallib.php') is ambigous

$usertotakeover = auth_saml2sso\get_known_plugin();
$takeoverpage = "$CFG->wwwroot/auth/saml2sso/takeover.php";

if ($hassiteconfig && !get_config(auth_saml2sso\COMPONENT_NAME, 'hide_takeover_page')
        && !empty($usertotakeover)) {
    $ADMIN->add('authsettings', new admin_externalpage('takeover', new lang_string('takeover', auth_saml2sso\COMPONENT_NAME),
         $takeoverpage));
}

if ($ADMIN->fulltree) {

    if (empty(getenv('SIMPLESAMLPHP_CONFIG_DIR'))
            && empty(get_config(auth_saml2sso\COMPONENT_NAME, 'sp_path'))) {
        $warning = $OUTPUT->notification('SIMPLESAMLPHP_CONFIG_DIR environment variable is not set'
                . ', review your Apache configuration or manually specify the lib path', \core\output\notification::NOTIFY_WARNING);
        $settings->add(new admin_setting_heading(auth_saml2sso\COMPONENT_NAME . '/envvar_missing', '', $warning));
    }

    $yesno = [get_string('no'), get_string('yes')];

    $settings->add(new admin_setting_heading(
            auth_saml2sso\COMPONENT_NAME . '/pluginname',
            new lang_string('settings_saml2sso', auth_saml2sso\COMPONENT_NAME),
            new lang_string('auth_saml2ssodescription', auth_saml2sso\COMPONENT_NAME)
        )
    );

    $defaultsspdir = !empty(getenv('SIMPLESAMLPHP_CONFIG_DIR')) ? dirname(getenv('SIMPLESAMLPHP_CONFIG_DIR')) : '';
    $field_setting = 'sp_path';
    $settings->add(new admin_setting_configdirectory('auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            $defaultsspdir)
    );
    
    // Migrate from misleading entityid config key
    $field_setting = 'authsource';
    $settings->add(new admin_setting_configtext_with_maxlength(
            'auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            '',
            PARAM_TEXT,
            50,
            255
        )
    );

    $field_setting = 'single_signoff';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting, 
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'), 
            0, 
            $yesno
        )
    );
    
    $field_setting = 'logout_url_redir';
    $settings->add(new admin_setting_configtext_with_maxlength(
            'auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            '',
            PARAM_URL,
            50,
            255
        )
    );

    $field_setting = 'idpattr';
    $settings->add(new admin_setting_configtext_with_maxlength(
            'auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            '',
            PARAM_TEXT,
            50,
            255
        )
    );
    
    $field_setting = 'moodle_mapping';
    $fields = [
        'username' => get_string('username'),
        'idnumber' => get_string('idnumber'),
        'email' => get_string('email'),
    ];
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting, 
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'), 
            0, 
            $fields
        )
    );    
    
    $field_setting = 'autocreate';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting, 
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'), 
            0, 
            $yesno
        )
    );
    
    $field_setting = 'session_control';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting, 
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'), 
            0, 
            $yesno
        )
    );
    
    // Dual login settings
    $settings->add(new admin_setting_heading('auth_saml2sso/dual_login_settings',
            new lang_string('label_dual_login_settings', 'auth_saml2sso'),
            new lang_string('label_dual_login_help', 'auth_saml2sso')));

    $field_setting = 'dual_login';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            0,
            $yesno
        )
    );

    $field_setting = 'button_url';
    $settings->add(new admin_setting_configtext_with_maxlength(
            'auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            '',
            PARAM_TEXT,
            80,
            255
        )
    );

    $field_setting = 'button_name';
    $settings->add(new admin_setting_configtext_with_maxlength(
            'auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            '',
            PARAM_TEXT,
            50,
            255
        )
    );

    // User synchronization with external source
    $settings->add(new admin_setting_heading(auth_saml2sso\COMPONENT_NAME . '/sync_settings',
            new lang_string('label_sync_settings', auth_saml2sso\COMPONENT_NAME),
            new lang_string('label_sync_settings_help', auth_saml2sso\COMPONENT_NAME)));

    if (!get_config(auth_saml2sso\COMPONENT_NAME, 'hide_takeover_page') && !empty($usertotakeover)) {
        $warning = $OUTPUT->notification(new lang_string('label_takeover_link', auth_saml2sso\COMPONENT_NAME, $takeoverpage),
                \core\output\notification::NOTIFY_WARNING);
        $settings->add(new admin_setting_heading(auth_saml2sso\COMPONENT_NAME . '/other_plugins', '', $warning));
    }

    // The user source plugin, must be a "directory style" auth source.
    $authsavailable = core_component::get_plugin_list('auth');
    $cansyncauthplugins = array();
    foreach ($authsavailable as $auth => $dir) {
        $authplugin = get_auth_plugin($auth);
        if (\auth_saml2sso\can_sync_user($authplugin)) {
            $cansyncauthplugins[$auth] = $authplugin;
        }
    }
    
    $field_setting = 'user_directory';
    $fields = [];
    foreach($cansyncauthplugins as $auth => $authplugin) {
        $fields[$auth] = $authplugin->get_title();
    }
    $fields[''] = null;
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            0,
            $fields
        )
    );

    $field_setting = 'do_update';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            0,
            $yesno
        )
    );

    $field_setting = 'verbose_sync';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            0,
            $yesno
        )
    );

    $field_setting = 'hide_takeover_page';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            1,
            $yesno
        )
    );
    
    $settings->add(new admin_setting_heading('auth_saml2sso/profile_settings',
            new lang_string('label_profile_settings', 'auth_saml2sso'), ''));

    $field_setting = 'edit_profile';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting, 
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'), 
            0, 
            $yesno
        )
    );
    
    $field_setting = 'allow_empty_email';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'),
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            0,
            $yesno
        )
    );

    $field_setting = 'field_idp_fullname';
    $settings->add(new admin_setting_configselect(
            'auth_saml2sso/' . $field_setting, 
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'), 
            0, 
            $yesno
        )
    );

    $field_setting = 'field_idp_firstname';
    $settings->add(new admin_setting_configtext_with_maxlength(
            'auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            '',
            PARAM_TEXT,
            50,
            255
        )
    );
    
    $field_setting = 'field_idp_lastname';
    $settings->add(new admin_setting_configtext_with_maxlength(
            'auth_saml2sso/'. $field_setting,
            new lang_string('label_' . $field_setting, 'auth_saml2sso'), 
            new lang_string('help_' . $field_setting, 'auth_saml2sso'),
            '',
            PARAM_TEXT,
            50,
            255
        )
    );
    
    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('saml2sso');
    display_auth_lock_options(
            $settings, $authplugin->authtype, $authplugin->userfields, new lang_string('auth_fieldlocks_help', 'auth'), true, false, $authplugin->get_custom_user_profile_fields()
    );
}
