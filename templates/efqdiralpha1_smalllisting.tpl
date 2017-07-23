<table width='100%' class="<{$listing.class}>" cellpadding="10" cellspacing='1'>
  <tr>
    <td colspan="3" class="linkeven" width='60%' align='left' valign="bottom"><a href='../../modules/<{$moddir}>/listing.php?catid=<{$listing.catid}>&item=<{$listing.id}>'><{if $showlinkimages == 1}><img src='images/listing.gif' border='0' alt='<{$lang_visit}>'><b>&nbsp;<{/if}><{$listing.title}></b></a>
    <{if $link.coupons > 0}>
     <a href="savings.php?itemid=<{$listing.id}>"><img src="<{$xoops_url}>/modules/<{$moddir}>/assets/images/coupons.jpg" alt="<{$smarty.const._MD_SAVINGS}>"></a>
    <{/if}>
</td>
  </tr>

  <{if $showdatafieldsincat == false}>
  <tr>
    <td class="linkodd" colspan='2' align='left'><strong><{$lang_description}></strong><br>
<{if $listing.logourl != ""}>
   <a href="../../<{$moddir}>/listing.php?catid=<{$listing.catid}>&item=<{$listing.id}>"><img src="../../<{$moddir}>/assets/images/shots/<{$listing.logourl}>" width="<{$shotwidth}>" alt=""  align="right" vspace="3" hspace="7"></a>
<{/if}>
    <div style="text-align: justify;"><{$listing.description}></div><br></td>
  </tr>
  <{/if}>
    <{foreach from=$listing.fields item=item}>
        <tr>
        <td class="linkodd" align='left'><strong><{$item.label}>&nbsp;:&nbsp;</strong></td>
        <td><{$item.value}></td>
        </tr>
    <{/foreach}>

  <{if $allowrating}>
  <tr>
    <td class="linkeven" colspan='3' align='center'><b><{$lang_hits}></b><{$listing.hits}> <b>&nbsp;&nbsp;<{$lang_rating}></b><{$listing.rating}> (<{$listing.votes}>)</td>
  </tr>
  <{/if}>

  <tr>
    <td class="linkfoot" colspan='3' align='center'>
    <{if $allowrating}><a href="../../modules/<{$moddir}>/ratelisting.php?catid=<{$listing.cid}>&item=<{$listing.id}>"><{$lang_ratethissite}></a><{/if}>
    </td>
  </tr>
</table>
<br>
