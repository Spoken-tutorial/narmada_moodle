<?php
/**
 * Event class
 *
 * @package auth_saml2sso
 * @copyright  2020 Marco Ferrante
 * @author Marco Ferrante, AulaWeb/University of Genoa <staff@aulaweb.unige.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_saml2sso\event;

require_once($CFG->dirroot.'/auth/saml2sso/locallib.php');

/**
 * The SAML2SSO event class for exceeding session limit.
 **/
class user_kicked_off extends \core\event\base {

    protected function init() {
        $this->context = \context_system::instance();
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('event_user_kicked_off', \auth_saml2sso\COMPONENT_NAME);
    }

    public function get_description() {
        return get_string('event_user_kicked_off_desc', \auth_saml2sso\COMPONENT_NAME);
    }

}