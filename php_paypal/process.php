<?php
/*
 * process.php
 *
 * PHP Toolkit for PayPal v0.51
 * http://www.paypal.com/pdn
 *
 * Copyright (c) 2004 PayPal Inc
 *
 * Released under Common Public License 1.0
 * http://opensource.org/licenses/cpl.php
 *
 */

//Configuration File
require_once __DIR__ . '/../paypal_includes/config.inc.php';

//Global Configuration File
require_once __DIR__ . '/../paypal_includes/global_config.inc.php';

?>

<html>
<head><title>::PHP PayPal::</title></head>
<body onLoad="document.paypal_form.submit();">
<form method="post" name="paypal_form" action="<?= $paypal[url] ?>">

    <?php
    //show paypal hidden variables

    showVariables();

    ?>

    <div style="text-align: center;"><span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: x-small; color: 333333; ">Processing Transaction . . . </span></div>

</form>
</body>
</html>
