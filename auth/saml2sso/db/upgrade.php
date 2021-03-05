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
 * saml2sso authentication plugin upgrade code
 *
 * @package     auth_saml2sso
 * @copyright   2011 Petr Skoda (http://skodak.org)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Daniel Miranda <daniellopes at gmail.com>
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_saml2sso.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_saml2sso_upgrade($oldversion) {
    global $CFG, $DB;

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.
    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.
    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.
    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.
    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017020700) {
        // Convert info in config plugins from auth/saml2sso to auth_saml2sso.
        upgrade_fix_config_auth_plugin_names('saml2sso');
        upgrade_fix_config_auth_plugin_defaults('saml2sso');
        upgrade_plugin_savepoint(true, 2017020700, 'auth', 'saml2sso');
    }

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2018031000) {
        // Convert entityid key to authsource key
        $entityid = get_config('auth_saml2sso', 'entityid');
        if ($entityid && empty(get_config('auth_saml2sso', 'authsource'))) {
            set_config('authsource', $entityid, 'auth_saml2sso');

            // Delete old setting.
            set_config('entityid', null, 'auth_saml2sso');

            upgrade_plugin_savepoint(true, 2018031000, 'auth', 'saml2sso');
        }
    }

    if ($oldversion < 2018112200) {
        $show_tackeover_page = get_config('auth_saml2sso', 'takeover_users');
        set_config('hide_takeover_page', !$show_tackeover_page, 'auth_saml2sso');
        set_config('takeover_users', null, 'auth_saml2sso');

        upgrade_plugin_savepoint(true, 2018112200, 'auth', 'saml2sso');
    }

    return true;
}
