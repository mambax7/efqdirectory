<table width="100%"><tr><td><{$category_path}></td><td align="right"><{$submit_link}></td></tr></table>
<hr><div align="right"><{$searchform}></div>
<br>
<{$lang_noresults}>
<{if count($categories) gt 0}>
<table border='0' cellspacing='5' cellpadding='0' align="center" width="100%">
  <tr>
  <!-- Start category loop -->
  <{foreach item=category from=$categories}>

    <td valign="top" width="40%"><img src="<{$category.image}>"><b><{$category.title}></b></a>&nbsp;(<{$category.totallisting}>)<br><{if $category.totallisting != 0}><{$category.subcategories}><{/if}></td>

    <{if $category.count is div by 3}>
    </tr><tr>
    <{/if}>

  <{/foreach}>
  <!-- End category loop -->

  </tr>
</table>
<br><br>
<hr><br>
<{/if}>

<{if $show_nav == true}>
      <div class="sortby"><{$lang_sortby}>&nbsp;&nbsp;<{$lang_title}> (<a href="index.php?catid=<{$category_id}>&amp;<{$sort1}>&orderby=titleA"><img src="images/up.gif" border="0" align="middle" alt=""></a><a href="index.php?catid=<{$category_id}>&amp;orderby=titleD"><img src="images/down.gif" border="0" align="middle" alt=""></a>)<{$lang_date}> (<a href="index.php?catid=<{$category_id}>&amp;orderby=dateA"><img src="images/up.gif" border="0" align="middle" alt=""></a><a href="index.php?catid=<{$category_id}>&amp;orderby=dateD"><img src="images/down.gif" border="0" align="middle" alt=""></a>)<{$lang_rating}> (<a href="index.php?catid=<{$category_id}>&amp;orderby=ratingA"><img src="images/up.gif" border="0" align="middle" alt=""></a><a href="index.php?catid=<{$category_id}>&amp;orderby=ratingD"><img src="images/down.gif" border="0" align="middle" alt=""></a>)<{$lang_popularity}> (<a href="index.php?catid=<{$category_id}>&amp;orderby=hitsA"><img src="images/up.gif" border="0" align="middle" alt=""></a><a href="index.php?catid=<{$category_id}>&amp;orderby=hitsD"><img src="images/down.gif" border="0" align="middle" alt=""></a>)</div>
      <{if $lang_cursortedby != ""}><br><b><{$lang_cursortedby}></b><{/if}>
<{/if}>

<{if $listings != ""}>
<h4><{$lang_listings}></h4>
<table width="100%" cellspacing="0" cellpadding="10" border="0">
<tr>
<td width="100%" align="center" valign="top">

  <!-- Start new link loop -->
<{section name=i loop=$listings}>
    <{include file="db:efqdiralpha1_smalllisting.tpl" listing=$listings[i]}>
<{/section}>
 <!-- End new link loop -->
<{$page_nav}>
</td></tr>
</table>
<{/if}>
