<?php

/**
 * @package Commentator
 * @author Bouzid Nazim Zitouni
 * @version 1.80
 */
/*
Plugin Name: Commentator
Plugin URI: http://angrybyte.com/wordpress-plugins/commentator-wordpress-comments-generator/
Description: Commentator will generate comments for your posts, you can use it to jump start your new blog, or break the silence and encourage users to comment.
Author: Bouzid Nazim Zitouni
Version: 1.80
Author URI: http://angrybyte.com
*/

add_action('admin_menu', 'commentatormenu');
add_option("commentatortemplates", 'Thanks', 'Commentator Templates', 'yes');
add_option("commentatornames", 'AngryByte', 'Commentator Templates', 'yes');


function commentatormenu()
{

    add_options_page('commentator', 'Commentator', 8, __file__,
        'commentator_plugin_options');
}


function commentator_plugin_options()
{
    ;
    echo '<div class="wrap">';


    $serv = "/wp-admin/options-general.php?page=commentator.php";
    global $post;
    global $wpdb;
    $pfx = $wpdb->prefix;

    if ($_POST["fit"])
    {
        $wpdb->query("delete from {$pfx}comments where comment_author_IP='1.1.1.1' ");

        $myposts = get_posts('numberposts=-1');
        foreach ($myposts as $post)
        {

            wp_update_comment_count($post->ID);
            $txtmat = "All Commentator Comments were deleted OK!";
        }

    }
    if ($_POST["xx"])
    {

        $temps = $_POST["xx"];
        $rcount = $_POST["rcount"];
        $names = $_POST["names"];
        $pcount = 0;
        update_option('commentatortemplates', $temps);
        update_option('commentatornames', $names);

        $temps = explode("\n", $temps);
        $temps = array_unique($temps);
        $ta = "";
        for ($rts = 0; $rts <= count($temps) - 1; $rts++)
        {
            if (!trim($temps[$rts]) == "")
            {
                $ta .= trim($temps[$rts]) . "\n";
            }


        }
        $ta = trim($ta);
        $temps = explode("\n", $ta);
        $names = explode("\n", $names);

        $myposts = $wpdb->get_col("SELECT id FROM `{$pfx}posts` WHERE `post_status` = 'publish'");
        $nofmatchs = 0;

        for ($i = 0; $i <= $rcount; $i++)
        {

            $post = $myposts[(rand(0, count($myposts) - 1))];
            $post = get_post($post);
            if (1)
            {

                $prod = $temps[(rand(0, count($temps) - 1))];
                $catss = wp_get_post_categories($post->ID);
                $zecat = get_the_category($post->ID);
                $zecat = $zecat[0]->cat_name;
                $zetit = get_the_title($post->ID);
                $zename = trim($names[(rand(0, count($names) - 1))]);
                $zename = str_replace(" ", ".", $zename);


                $prod = str_replace("[cat]", $zecat, $prod);

                $prod = str_replace("[title]", $zetit, $prod);

                $idd = $post->ID;
                $tim = new DateTime($post->post_date);

                $nn = new DateTime("now");
                $diff = get_time_difference($tim->format('Y/M/d h:i:s'), $nn->format('Y/M/d h:i:s'));

                $m = rand(1, 60);
                $h = rand(0, $diff['hours']);
                $dd = rand(0, $diff['days']);
                $l = rand(0, 20);
                $l = "";


                $tim->modify("+{$h} hours");

                $tim->modify("+{$m} minutes");


                $tim->modify("+{$dd} days");

                $tim = $tim->format('Y/m/d h:i:s');


                $wpdb->query("INSERT INTO `{$pfx}comments` (
`comment_ID` ,
`comment_post_ID` ,
`comment_author` ,
`comment_author_email` ,
`comment_author_url` ,
`comment_author_IP` ,
`comment_date` ,
`comment_date_gmt` ,
`comment_content` ,
`comment_karma` ,
`comment_approved` ,
`comment_agent` ,
`comment_type` ,
`comment_parent` ,
`user_id` 
)
VALUES (
NULL , '$idd', '$zename', '{$zename}@fakemail.com', '$l', '1.1.1.1', '$tim', '$tim', '$prod', '0', '1', 'Commentator 1.0', '', '0', '0');");

            }
        }

        foreach ($myposts as $post)
        {

            wp_update_comment_count($post);
        }
        $txtmat = "$rcount comments were sucessfully posted!";
    }
    $serv = str_replace('%7E', '~', $_SERVER['REQUEST_URI']);
    $plugurl = plugin_dir_url(__file__);

    $oldtemp = stripcslashes(get_option('commentatortemplates'));
    $oldnames = stripcslashes(get_option('commentatornames'));
    if ($txtmat != '')
    {
        echo "<div class='updated'>$txtmat</div> ";
    }


    echo <<< EOST
     <div id="icon-tools" class="icon32"><br />
</div>
<h2>Commentator</h2>
<table>
   <tr VALIGN="TOP">
      <td WIDTH="100%">
         
         <div class="metabox-holder" />
         <div id="gdpt_server" class="postbox gdrgrid frontleft" >
            <h3 class="hndle">
               <span>Commentator Settings</span>
            </h3>
            <div class="inside">
               <div class="table">
                  <table>
                     <tbody>
                        <tr class="first">
                           <td class="first b">Comment templates:</td>
                           <td class="t options">
                              <Form method="post" action="$serv">
EOST;

    //if(function_exists(wp_editor)){
    // wp_editor( $oldtemp, "xx" );
    //}else{
    //compatibility issues
    echo "<textarea name='xx' cols='100' rows='20'>$oldtemp</textarea>";
    //}


    echo <<< EOST
                                 
                           </td>
                        </tr>
                        <tr>
                        <td class="first b">Commenter names:</td>
                        <td class="t options"><textarea name="names" cols="100" rows="10">$oldnames</textarea></td>
                        </tr>
                        <tr>
                        <td class="first b">The required comments count:</td>
                        <td class="t options"><input type="text" name="rcount" value="10" /></td>
                        </tr>
                        <tr>
                        <td class="first b">Submit & Save:</td>
                        <td class="t options"> <input type="submit" value="submit" /> <br /> </form></td></tr>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <div id="gdpt_server" class="postbox gdrgrid frontleft">
            <h3 class="hndle">
               <span>Help</span>
            </h3>
            <div class="inside">
               <div class="table">
                  <table>
                     <tbody>
                        <tr class="first">
                           <td class="first b">
                              Commentator is a plugin that generates as many comments as you want to all posts on your blog.<br /><br /> You will enter a list of names to be used as your commenters.<br /><br /> Comments will be destributed randomly between posts to have a realistic feel in your blog.<br /><br /> Your comments can be costumized with your posts Title and category.<br /><br /> You can use these tags: <br /><br /><strong>[title]</strong> will be replaced by the post title, <br /><br /><strong>[cat]</strong> will be replaced by the category.<br /><br /> &nbsp; Please write every comment template in a new line.<br /><br />    &nbsp;You can clear all comments generated by Commentator when you hit this button<br /><br />
                              <Form method="post" action="$serv"><input type="hidden" value="fit" name="fit" id="fit" />
                                 <input type="submit" value="Remove all Commentator Comments" />
                              </form>
                              <br /> 
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
              
      </td>
      <td width="40%">
      <div class="metabox-holder" />
          <div id="gdpt_server" class="postbox gdrgrid frontleft">
            <h3 class="hndle">
               <span>Get Commentator Pro for Free!</span>
            </h3>
            <div class="inside">
               <div class="table">
                  <table>
                     <tbody>
                        <tr class="first">
                        
                           <td class="first b"><div id="fb-root"></div>
<p><script type="text/javascript">// <![CDATA[
    (function(d, s, id) {   var js, fjs = d.getElementsByTagName(s)[0];   if (d.getElementById(id)) return;   js = d.createElement(s); js.id = id;   js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&#038;appId=173688705979189";   fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));
// ]]></script>Now And for a limited time, you can get Commentator Pro for free! all you need to do is:<br />
                           <ol >
                           <li> <table style="display:inline";><tr><td>Give us a   <!-- Place this tag where you want the +1 button to render --><div class="g-plusone" data-size="medium" data-href="http://angrybyte.com"></div><!-- Place this render call where appropriate --><br /><script type="text/javascript">// <![CDATA[
      (function() {     var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;     po.src = 'https://apis.google.com/js/plusone.js';     var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);   })();// ]]></script></td><td> And a </td><td><div class="fb-like" data-href="http://angrybyte.com" data-send="false" data-layout="button_count" data-width="10" data-show-faces="false"></div></li></td></tr></table>
	                       <li><a href='http://www.facebook.com/pages/AngryBytecom/262969757073611?sk=wall'>Join our facebook page</a>, and share something cool with us!</li>
	                       <li><a href="http://wordpress.org/extend/plugins/commentator/"> Rate Commentator on wordpress.org </a>, 5 starts would be great!</li>
                        	
                            </ol>
                          <br />
                           <br /> 
                           For each 100 ratings on wordpress.org, we`ll randomly pick 3 lucky winners.<br/><br/><br/>
                          
                           </tr>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
          </div>
         <div class="metabox-holder" />
         <div id="gdpt_server2" class="postbox gdrgrid frontleft" />
         <h3 class="hndle">
            <span>Commentator Pro!</span>
         </h3>
         <div class="inside" />
         <div class="table">
            <table>
               <tbody>
                  <tr class="first">
                     <td class="first b">
                        <p style="font-size: 100%; width:660px">
                         <h2>To Get Commentator Pro! Now <a href="https://secure.avangate.com/order/checkout.php?PRODS=4548292&amp;QTY=1&amp;CART=2">Click here</a></h2><br />You think Commentator is Great? wait until you get commentator Pro! Get Pro now To have access to:<br /><br /> 
                        <table style="border-collapse: collapse;" border="0">
                           <colgroup> </colgroup>
                           <tbody valign="top">
                              <tr>
                                 <td style="padding-left: 7px; padding-right: 7px; border-top: solid #4bacc6 1.0pt; border-left: none; border-bottom: solid #4bacc6 1.0pt; border-right: none;"></td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-top: solid #4bacc6 1.0pt; border-left: none; border-bottom: solid #4bacc6 1.0pt; border-right: none;">
                                    <p style="text-align: center;"><span style="color: #31849b; font-size: 16pt;"><strong>PRO!</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-top: solid #4bacc6 1.0pt; border-left: none; border-bottom: solid #4bacc6 1.0pt; border-right: none;">
                                    <p style="text-align: center;"><span style="color: #31849b; font-size: 16pt;"><strong>Commentator</strong></span></p>
                                 </td>
                              </tr>
                              <tr style="background: #d2eaf1;">
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Generate random comments for all posts</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                              </tr>
                              <tr>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Safely delete all generated comments</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                              </tr>
                              <tr style="background: #d2eaf1;">
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Target specific categories or posts.</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: red; font-size: 18pt;"><strong>No</strong></span></p>
                                 </td>
                              </tr>
                              <tr>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Customizable comment time</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="color: red; font-size: 18pt;"><strong>No</strong></span></p>
                                 </td>
                              </tr>
                              <tr style="background: #d2eaf1;">
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Delete generated comments for specific categories or posts.</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: red; font-size: 18pt;"><strong>No</strong></span></p>
                                 </td>
                              </tr>
                              <tr>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Auto comment on new posts</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px;">
                                    <p style="text-align: center;"><span style="color: red; font-size: 18pt;"><strong>No</strong></span></p>
                                 </td>
                              </tr>
                              <tr style="background: #d2eaf1;">
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Display the number of generated comments per category.</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-left: none; border-right: none;">
                                    <p style="text-align: center;"><span style="color: red; font-size: 18pt;"><strong>No</strong></span></p>
                                 </td>
                              </tr>
                              <tr>
                                 <td style="padding-left: 7px; padding-right: 7px; border-bottom: solid #4bacc6 1.0pt;">
                                    <p style="text-align: center;"><span style="font-size: 12pt;">Customizable emails domain</span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-bottom: solid #4bacc6 1.0pt;">
                                    <p style="text-align: center;"><span style="color: #00b050; font-size: 18pt;"><strong>Yes</strong></span></p>
                                 </td>
                                 <td style="padding-left: 7px; padding-right: 7px; border-bottom: solid #4bacc6 1.0pt;">
                                    <p style="text-align: center;"><span style="color: red; font-size: 18pt;"><strong>No</strong></span></p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                        <br /> <br /> To get more info about the Pro version, <a href="http://angrybyte.com/wordpress-plugins/wordpress-automatic-comments-commentator-pro/">please check here</a><br /> <br /></p>
                        <br /><br />
                        <h4>Take a look at all the features included with Commentator Pro!</h4>
                        <br /><br />
                        <a href="https://secure.avangate.com/order/checkout.php?PRODS=4548292&amp;QTY=1&amp;CART=2"><img src="{$plugurl}images/commentatorpro.png" alt="commentator Pro" /></a>	
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </td>
   </tr>
</table>


EOST;

    echo "  ";

}
function get_time_difference($start, $end)
{
    $uts['start'] = strtotime($start);
    $uts['end'] = strtotime($end);
    if ($uts['start'] !== -1 && $uts['end'] !== -1)
    {
        if ($uts['end'] >= $uts['start'])
        {
            $diff = $uts['end'] - $uts['start'];
            if ($days = intval((floor($diff / 86400))))
                $diff = $diff % 86400;
            if ($hours = intval((floor($diff / 3600))))
                $diff = $diff % 3600;
            if ($minutes = intval((floor($diff / 60))))
                $diff = $diff % 60;
            $diff = intval($diff);
            return (array('days' => $days, 'hours' => $hours, 'minutes' => $minutes,
                'seconds' => $diff));
        } else
        {
           // trigger_error("Ending date/time is earlier than the start date/time",
            //    E_USER_WARNING);
        }
    } else
    {
      //  trigger_error("Invalid date/time data detected", E_USER_WARNING);
    }
    return (false);
}

?>