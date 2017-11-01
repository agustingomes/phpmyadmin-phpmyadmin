<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Second authentication factor handling
 *
 * @package PhpMyAdmin
 */
namespace PhpMyAdmin\Plugins;

use PhpMyAdmin\Core;
use PhpMyAdmin\Message;
use PhpMyAdmin\SecondFactor;

/**
 * Second factor authentication plugin class
 *
 * This is basic implementation which does no
 * additional authentication, subclasses are expected
 * to implement this.
 */
class SecondFactorPlugin
{
    /**
     * @var string
     */
    public static $id = '';

    /**
     * @var SecondFactor
     */
    protected $_second;

    /**
     * @var boolean
     */
    protected $_provided;

    /**
     * @var string
     */
    protected $_message;

    /**
     * Creates object
     *
     * @param SecondFactor $second SecondFactor instance
     */
    public function __construct(SecondFactor $second)
    {
        $this->_second = $second;
        $this->_provided = false;
        $this->_message = '';
    }

    /**
     * Returns authentication error message
     *
     * @return string
     */
    public function getError()
    {
        if ($this->_provided) {
            if (!empty($this->_message)) {
                return Message::rawError(
                    sprintf(__('Two-factor authentication failed: %s'), $this->_message)
                )->getDisplay();
            }
            return Message::rawError(
                __('Two-factor authentication failed.')
            )->getDisplay();
        }
        return '';
    }

    /**
     * Checks authentication, returns true on success
     *
     * @return boolean
     */
    public function check()
    {
        return true;
    }

    /**
     * Renders user interface to enter second factor
     *
     * @return string HTML code
     */
    public function render()
    {
        return '';
    }

    /**
     * Renders user interface to configure second factor
     *
     * @return string HTML code
     */
    public function setup()
    {
        return '';
    }

    /**
     * Performs backend configuration
     *
     * @return boolean
     */
    public function configure()
    {
        return true;
    }

    /**
     * Get user visible name
     *
     * @return string
     */
    public static function getName()
    {
        return __('None two-factor');
    }

    /**
     * Get user visible description
     *
     * @return string
     */
    public static function getDescription()
    {
        return __('Login using password only.');
    }

    /**
     * Return an applicaiton ID
     *
     * Either hostname or hostname with scheme.
     *
     * @param boolean $return_url Whether to generate URL
     *
     * @return string
     */
    public function getAppId($return_url)
    {
        global $PMA_Config;

        $url = $PMA_Config->get('PmaAbsoluteUri');
        $parsed = [];
        if (!empty($url)) {
            $parsed = parse_url($url);
        }
        if (empty($parsed['scheme'])) {
            $parsed['scheme'] = $PMA_Config->isHttps() ? 'https' : 'http';
        }
        if (empty($parsed['host'])) {
            $parsed['host'] = Core::getenv('HTTP_HOST');
        }
        if ($return_url) {
            return $parsed['scheme'] . '://' . $parsed['host'] . (!empty($parsed['port']) ? ':' . $parsed['port'] : '');
        } else {
            return $parsed['host'];
        }
    }
}
