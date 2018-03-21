<?php namespace XoopsModules\Efqdirectory;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package      efqdirectory
 * @since
 * @author       Martijn Hertog (aka wtravel)
 * @author       XOOPS Development Team,
 */

use XoopsModules\Efqdirectory;

/**
 * Class Subscription
 * @package XoopsModules\Efqdirectory
 */
class Subscription extends \XoopsObject
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        //Constructor
    }

    /**
     * Function currencyArray: creates array of options for currency selbox
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @return array $arr
     */
    public function currencyArray()
    {
        //create array of options for duration selbox: months, weeks, year, days etc.
        $arr = ['0' => '---', 'USD' => _MD_CURR_USD, 'AUD' => _MD_CURR_AUD, 'EUR' => _MD_CURR_EUR, 'GBP' => _MD_CURR_GBP, 'YEN' => _MD_CURR_YEN];

        return $arr;
    }

    /**
     * Function notifyExpireWarning
     * Notify user of a subscription order that is about to expire.
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param int|string $orderid - Default: '0' - Order ID
     * @param int|string $userid  - Default: '0' - User ID
     */
    public function notifyExpireWarning($orderid = '0', $userid = '0')
    {
        global $xoopsConfig, $moddir;
        require_once XOOPS_ROOT_PATH . '/class/mail/xoopsmultimailer.php';

        $xoopsMailer = new \XoopsMailer();
        $xoopsMailer->useMail();
        $template_dir = XOOPS_URL . '/modules/' . $moddir . '/language/' . $xoopsConfig['language'] . '/mail_template/';
        $template     = 'expirewarning.tpl';
        $subject      = _MD_LANG_EXPIREWARNING_SUBJECT;
        $xoopsMailer->setTemplateDir($template_dir);
        $xoopsMailer->setTemplate($template);
        $xoopsMailer->setToUsers($userid);
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject($subject);
        $success = $xoopsMailer->send();
    }



    /**
     * Function durationArray: creates array of options for duration selbox:
     * months, weeks, year, days etc.
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @return array $arr
     */
    public function durationArray()
    {
        $arr = ['0' => '---', '1' => _MD_DAYS, '2' => _MD_WEEKS, '3' => _MD_MONTHS, '4' => _MD_QUARTERS, '5' => _MD_YEARS];

        return $arr;
    }

    /**
     * Function durationArray: creates array of options for duration selbox:
     * single items like: month, week, year, day etc.
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @return array $arr
     */
    public function durationSingleArray()
    {
        $arr = ['0' => '---', '1' => _MD_DAY, '2' => _MD_WEEK, '3' => _MD_MONTH, '4' => _MD_QUARTER, '5' => _MD_YEAR];

        return $arr;
    }


}
