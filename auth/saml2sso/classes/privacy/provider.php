<?php
/**
 * Privacy Subsystem implementation for auth_saml2sso.
 *
 * @package    auth_saml2sso
 * @copyright 2018 Marco Ferrante, University of Genoa (I) <marco@csita.unige.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_saml2sso\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for auth_saml2sso implementing null_provider.
 *
 * Live from the Moodlemoot Italia 2018! https://moodlemoot.org/mootit18/
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }

}