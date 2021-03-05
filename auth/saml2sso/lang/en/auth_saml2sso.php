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
 * Strings for component 'auth_saml2sso', language 'en'.
 *
 * @package auth_saml2sso
 * @author Daniel Miranda <daniellopes at gmail.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['auth_saml2ssodescription']                 = 'Users can login using SAML2 Identity Provider';
$string['pluginname']                               = 'SAML2 SSO Auth';
$string['settings_saml2sso']                        = '';

//label config strings
$string['label_button_url']                         = 'Url to button (image)';
$string['label_button_name']                        = 'Button caption';
$string['label_sp_path']                            = 'SimpleSAMLphp library path';
$string['label_dual_login']                         = 'Dual login';
$string['label_single_signoff']                     = 'Single Sign Off';
$string['label_idpattr']                            = 'Username attribute';
$string['label_moodle_mapping']                     = 'Username checking';
$string['label_autocreate']                         = 'Auto create users';
$string['label_authsource']                         = 'SP auth source name';
$string['label_logout_url_redir']                   = 'Logout URL';
$string['label_logout']                             = 'Click here to logout';
$string['label_edit_profile']                       = 'Can user edit profile?';
$string['label_field_idp_firstname']                = 'Firstname field from Identity Provider';
$string['label_field_idp_lastname']                 = 'Lastname field from Identity Provider';
$string['label_field_idp_fullname']                 = 'Full name from Identity Provider';
$string['label_instructions_title']                 = 'Instructions';
$string['label_instructions_p1']                    = '<p>You must fill the following Data mapping fields as:</p><ul><li>Firstname => givenName</li><li>Surname => surname</li><li>Email address: => email</li></ul><p>You can change this in <code>$stringmapping</code> array in <code>auth.php</code></p>';
$string['label_allow_empty_email']                  = 'Allow empty email';
$string['label_session_control']                    = 'Apply session limit';

//_help config strings
$string['help_allow_empty_email']                   = 'Allow the IdP/ADFS don\'t return Email or Mail value. User will be prompted to complete the profile.';
$string['help_button_url']                          = 'Url to an image that will be used as login button. Max 50px high';
$string['help_button_name']                         = 'A caption for the login button';
$string['help_sp_path']                             = 'Absolute path to Service Provider (SP) installation. Ex.: /var/www/simplesamlphp/';
$string['help_dual_login']                          = 'Define if prompt the user with standard Moodle login page';
$string['help_single_signoff']                      = 'Single Sign Off users from Moodle and IdP?';
$string['help_idpattr']                             = 'Attribute from the Identity Provider used as the Moodle username.';
$string['help_moodle_mapping']                      = 'Where to check if the username exists? If using \'' .
        get_string('idnumber') . '\', remember to mapping in Data mapping below';
$string['help_autocreate']                          = 'Allow create new users?';
$string['help_authsource']                          = 'Service Provider authentication source name available in /config/authsources.php SimpleSAMLphp installation';
$string['help_logout_url_redir']                    = 'URL to redirect users on logout. If the URL is invalid or empty, it will redirect to Moodle main page. (ex.: https://goto/another/url)';
$string['nouser']                                   = 'There is no user with the provided Id and auto signup is not allowed. The provided Id is: ';
$string['help_edit_profile']                        = 'If users cannot edit profile, they won\'t see the link to profile';
$string['help_field_idp_firstname']                 = '<strong>deprecated, use an authproc</strong> Field from Identity Provider containing the user first name' ;
$string['help_field_idp_lastname']                  = '<strong>deprecated, use an authproc</strong> Field from Identity Provider containing the user last name';
$string['help_field_idp_fullname']                  = '<strong>deprecated, use an authproc</strong> Is the username from Identity Provider in a single field? If yes, fill with the same value in the fields firstname and lastname below';
$string['help_session_control']                     = 'If the global setting \'' 
                                                    . (new lang_string('limitconcurrentlogins', 'core_auth'))->out('en')
                                                    . '\' is 1, honours the restriction for non-admin users.';

//error config strings
$string['error_create_user']                        = 'A error occured when create a user account. Please, contact the administrator.';
$string['error_sp_path']                            = 'The path to SimpleSAMLphp libraries must be given in config';
$string['error_idpattr']                            = 'A Username mapping attribute must be given';
$string['error_authsource']                         = 'A Service Provider source name must be given';
$string['error_field_idp_firstname']                = 'The Firstname field from Identity Provider is mandatory';
$string['error_field_idp_lastname']                 = 'The lastname field from Identity Provider is mandatory';
$string['error_lockconfig_field_map_firstname']     = 'The First name in Data mapping fields is mandatory';
$string['error_lockconfig_field_map_lastname']      = 'The Surname in Data mapping fields is mandatory';
$string['error_lockconfig_field_map_email']         = 'The Email address in Data mapping fields is mandatory';
$string['error_novalidemailfromidp']                = 'There is no valid e-mail address from Identity Provider';
$string['error_you_are_still_connected']            = 'You are still connected in a SSO session';
$string['error_nokey']                              = 'The Identity Provider has not provide the attribute need to identify you';

$string['success_config']                           = 'All the config fields were saved successfully';

$string['label_profile_settings']                   = 'SAML attributes and user profile';

$string['label_dual_login_settings']  = 'Dual login';
$string['label_dual_login_help']   = '
By default Dual login is setted to No and users are redirect to the IdP or discovery service
configured in the SimpleSAMLphp authentication source.<br />
To perform Moodle standard login, add saml=off parameter. Ex.: /login/index.php?saml=off<br />
Enabling Dual login users have to choose the authentication method.';
$string['label_sync_settings']        = 'Users sync';
$string['label_sync_settings_help']   = '
SAML IdPs cannot provide a user list suitable to users synchronization,
however they are often backended on a LDAP o DB source able to.
Configure the plugin for the backend authentication source.';
$string['label_user_directory']       = 'User source';
$string['help_user_directory']        = 'An auth plugin with listing capability';
$string['label_do_update']            = 'Update profiles';
$string['help_do_update']             = 'Profile fields of existing users will be update
with values from user source. If "no", only new users in user source will be created locally.
If "yes", user synchronization could overwrite values set at login by the IdP.';
$string['label_verbose_sync']        = 'Show details';
$string['help_verbose_sync']         = 'Enable verbose report';

$string['synctask']        = 'Users sync';

$string['label_hide_takeover_page']       = 'Hide import page';
$string['help_hide_takeover_page']        = '
The import page item appears in the admin menu only if there are
users belonging to other auth plugins that can be takeover.
It can be annoying if you want leave these users as-is.';

$string['takeover']             = 'Migrate users to ' . $string['pluginname'];
$string['label_takeover_link'] = '
There are still users handled by plugins compatible with this
one. Do you want <a href="{$a}">import them<a>?';
$string['label_takeover']       = 'Takeover existing users';
$string['help_takeover']        = '
Existing users belonging to the auth plugins listed below could be
converted to use ' . $string['pluginname'] . ' authentication.
<br />Deleted users will not migrate.';
$string['label_takeover_plugin']            = '{$a->auth} ({$a->count} active users)';
$string['label_takeover_unknown_plugin']    = 'Deleteted or corrupted "{$a->auth}" plugin ({$a->count} active users)';

$string['takeover_nouser']      = 'No plugin selected or plugins with no users';
$string['takeover_completed']   = 'Users converted';
$string['takeover_submit']      = 'Convert to ' . $string['pluginname'];
$string['takeover_count_migrated']      = '{$a->count} users imported from {$a->auth}<br />';
$string['event_user_migrate']       = 'User imported';
$string['event_user_migrate_desc']  = 'The user has been migrated to ' . $string['pluginname'];
$string['event_not_searchable']         = 'Not identifiable SSO user';
$string['event_not_searchable_desc']    = 'The IdP doesn\'t provided the attribute need to search for the user';
$string['event_user_kicked_off']        = 'Old sessions killed';
$string['event_user_kicked_off_desc']   = 'The user has activated a new Moodle session while the concurrent login limit was active: old sessions have been destroyed and unsaved data ignored';

$string['privacy:metadata'] = 'The SAML2 SSO authentication plugin does not store any personal data.';
