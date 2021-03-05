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
 * This script reasing users from an existing external authentication backend
 * to the SAML2SSO plugin
 *
 * @package auth_saml2sso
 * @copyright  2018 Marco Ferrante
 * @author Marco Ferrante <marco at csita.unige.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');

$confirm = optional_param('confirm', 0, PARAM_BOOL);
$returnurl = optional_param('returnurl', '/auth/saml2sso/takeover.php', PARAM_LOCALURL);
$returnurl = new moodle_url($returnurl);

admin_externalpage_setup('takeover');

require_once($CFG->dirroot . '/auth/saml2sso/locallib.php');
require_once($CFG->libdir.'/formslib.php');

class takeover_users extends \moodleform {

    /**
     * Define a "Takeover" button, and a fieldset with checkboxes for selectively purging separate caches.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('hidden', 'returnurl', $this->_customdata['returnurl']);
        $mform->setType('returnurl', PARAM_LOCALURL);

        if (empty($this->_customdata['plugins'])) {
            $mform->addElement('header', 'takeoverheader', get_string('takeover_nousers', \auth_saml2sso\COMPONENT_NAME));
        }

        $checkboxes = [];
        foreach ($this->_customdata['plugins'] as $name => $plugin) {
            if (!empty($plugin['auth'])) {
                $label = get_string('label_takeover_plugin', \auth_saml2sso\COMPONENT_NAME, $plugin);
            }
            else {
                $plugin['auth'] = $name;
                $label = get_string('label_takeover_unknown_plugin', \auth_saml2sso\COMPONENT_NAME, $plugin);
            }
            $mform->addElement('advcheckbox', 'takeoverpluginsoptions[' . $name . ']', '', $label);
        }

        $mform->addElement('submit', 'takeoversubmit', get_string('takeover_submit', \auth_saml2sso\COMPONENT_NAME));
//        $mform->addElement('advcheckbox', 'hideme', 'Hide me!'); // Not yet implemented

    }

    /**
     * @param array $data
     * @param array $files
     * @return array Error messages
     */
    public function validation($data, $files) {
        $errors = [];
        if (isset($data['takeoversubmit']) && empty(array_filter($data['takeoverpluginsoptions']))) {
            $errors['takeoverpluginsselected'] = get_string('takeover_nouser', \auth_saml2sso\COMPONENT_NAME);
        }
        return $errors;
    }
}

$form = new takeover_users(null, ['returnurl' => $returnurl,
        'plugins' => \auth_saml2sso\get_known_plugin()]);

// If we have got here as a confirmed action, do it.
if ($data = $form->get_data()) {

    $message = '';
    foreach ($data->takeoverpluginsoptions as $auth => $selected) {
        if (!$selected) {
            continue;
        }
        $count = auth_saml2sso\takeover($auth);
        $message .= get_string('takeover_count_migrated', \auth_saml2sso\COMPONENT_NAME, ['count' => $count, 'auth' => $auth]);
    }

    $message .= get_string('takeover_completed', \auth_saml2sso\COMPONENT_NAME);
}

if (isset($message)) {
    redirect($returnurl, $message);
}

// Otherwise, show a form select user to import.

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('label_takeover', \auth_saml2sso\COMPONENT_NAME));

echo $OUTPUT->box_start('generalbox', 'notice');
echo html_writer::tag('p', get_string('help_takeover', \auth_saml2sso\COMPONENT_NAME));
echo $form->render();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
