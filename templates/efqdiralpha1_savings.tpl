<table>
      <tr>
        <th colspan="2"> <div align="center"><a href="<{$xoops_url}>/modules/<{$moddir}>/savings.php?itemid=<{$itemid}>"><{$smarty.const._MD_SPECIALOFFERS_FOR}>&nbsp;<{$itemtitle}></a>
        </div></th>
        </tr>
      <{foreach item=coupon from=$coupons}>
            <tr class='<{cycle values=odd,even}>'>
                <td>
                    <div><{if $coupon.logourl}> <img src="<{$xoops_url}>/modules/<{$moddir}>/assets/images/shots/<{$coupon.logourl}>" border="0">
        <{/if}>&nbsp;&nbsp;
        <div> <{if $admin}> <a href="<{$xoops_url}>/modules/<{$moddir}>/addcoupon.php?couponid=<{$coupon.couponid}>&item=<{$coupon.itemid}>"><img src="<{$xoops_url}>/modules/<{$moddir}>/assets/images/editicon.gif" alt="<{$smarty.const._MD_EDITCOUPON}>"></a>
          <{/if}> <a href="<{$xoops_url}>/modules/<{$moddir}>/listing.php?item=<{$coupon.itemid}>"><{$itemtitle}></a><br>
          <br>

          <{$smarty.const._MD_PUBLISHEDON}><{$coupon.publish}>
          <{if $coupon.expire > 0}> <br>
          <{$smarty.const._MD_EXPIRESON}><{$coupon.expire}> <{/if}> </div>
      </div>
                    </td>

    <td> <{$coupon.heading}><a href="javascript:openWithSelfMain('<{$xoops_url}>/modules/<{$moddir}>/printcoupon.php?coupid=<{$coupon.couponid}>', 'print', 625, 380);">&nbsp;&nbsp;<img src="<{$xoops_url}>/modules/<{$moddir}>/assets/images/print.png" alt="<{$smarty.const._MD_PRINTERFRIENDLY}>"></a>
      <br>
      <br>
                    <{$coupon.descr}>
                </td>
            </tr>
            <{if $admin}>
                <tr>
                    <td colspan="2" class="foot">
                        <a href="<{$xoops_url}>/modules/<{$moddir}>/addcoupon.php?couponid=<{$coupon.couponid}>&item=<{$coupon.itemid}>"><{$smarty.const._MD_EDITCOUPON}></a>
                    </td>
                </tr>
            <{/if}>
    <{foreachelse}>
        <tr>
            <td colspan="2">
                <{$smarty.const._MD_NOSAVINGS}>
            </td>
        </tr>
    <{/foreach}>
</table>
