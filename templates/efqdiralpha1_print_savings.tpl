<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
<meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>">
<meta http-equiv="content-language" content="<{$xoops_langcode}>">
<meta name="robots" content="<{$xoops_meta_robots}>">
<meta name="keywords" content="<{$xoops_meta_keywords}>">
<meta name="description" content="<{$xoops_meta_description}>">
<meta name="rating" content="<{$xoops_meta_rating}>">
<meta name="author" content="<{$xoops_meta_author}>">
<meta name="copyright" content="<{$xoops_meta_copyright}>">
<meta name="generator" content="XOOPS">
<title><{$xoops_sitename}> - <{$xoops_pagetitle}></title>
</head>
<body bgcolor="#ffffff">
<div style='width: 600px; border: 3px dashed #000000; padding: 6px; font-family: Tahoma, Verdana, sans-serif; font-size: 10px; '>

  <table border='1' cellspacing='0' bordercolor='#000000' width='580'>
    <tr>
            <td>

    <table border=0 width='580' cellspacing='0' cellpadding='2'>
        <tr>
            <td width='140'>
             <{if $coupon.logourl != ""}>
                 <img src="<{$xoops_url}>/modules/<{$smartydir}>/images/shots/<{$coupon.logourl}>" alt="<{$coupon.linkTitle}> Logo">
             <{/if}>
            <br>
              <span style="font-family: arial, helvetica, sans-serif; font-size: xx-small; "><{$coupon.linkTitle}><br><{$coupon.address}><br>
              <{$coupon.address2}> <{$coupon.state}> <{$coupon.zip}><br>
              <{$coupon.country}></span><br>
            <img src='/images/spacer.gif' height='4'><br>
            <span style="font-family: arial, helvetica, sans-serif; font-size: small;  font-weight: bold;"><{$coupon.phone}></span>
            </td>
            <td></td>
            <td valign='top'>
                <table border='0' cellspacing='0' cellpadding='2' width='100%'>
                    <tr>
                        <td align='right'><span style="font-size: x-small; font-family: arial, helvetica, sans-sarif; ">No. <span style="font-weight: bold;"><span style="color: #ff0000; "><{$coupon.counter}><{$coupon.couponid}></span></span></b></td>

                    </tr>
                    <tr>
                        <td><span style="font-family: arial, helvetica, sans-sarif; color: #ff0000; font-size: medium;  font-weight: bold; font-weight: bold;"><b><{$coupon.heading}></b></span><br><span style="font-weight: bold;"><{$coupon.linkTitle}></span></td>
                    </tr>
                     <tr><td><!-- nothing --></td></tr>
                    <tr>
                        <td>
                        <{$coupon.description}>
                    <{if $coupon.image}>
                        <img src='<{$xoops_url}>/uploads/<{$coupon.image}>' align='right'>
                    <{/if}>
                    </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <{if $coupon.expire != 0}>
                       <tr>
                           <td align='center'><span style="font-family: arial, helvetica, sans-serif; color: #000000; font-size: x-small;  font-weight: bold; font-weight: bold;"><{$smarty.const._MD_EXPIRESON}><b><{$coupon.expire}></b></td>
                       </tr>
                    <{/if}>
                </table>

            </td>
        </tr>
        </table>

        </td>
        </tr>
        <tr>

      <td bgcolor='#CCFFFF' align='center'><span style="font-size: smaller; font-family: arial, helvetica, sans-serif;  font-weight: bold; font-weight: bold;"><b><{$coupon_footer}></b></span></td>
        </tr>
    </table>

            </div>
    <table border=0 width='580' cellspacing='0' cellpadding='2'>
        <tr>
            <td align="center">
                <form name=close action='<{$SCRIPT_NAME}>' method=get>
        <input type="button" onclick="window.print()" value='Print'>
                </form>
            </td>
        </tr>
    </table>
  </body>
</html>
