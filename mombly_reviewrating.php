<?php
   
/*
Plugin Name: Mombly Review Rating
Plugin URI: http://mombly.com/wordpress-plug-ins/mombly-review-rating/
Version: 1.02
Author: ccjx
Description: A plugin which allows you to add a simple rating to your reviews on your wordpress blog. The format to add a rating is simply just <code>[Rating:3.5/5]</code>. You can even use your own pictures for the stars of the ratings. There is also text support for RSS feeds.
*/
/*  Copyright 2009  ccjx  (email : ccjxcai@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
$mombly_rating_dir = get_settings('siteurl').'/wp-content/plugins/mombly-review-rating';
$mombly_rating_optionsdomain = "mombly_rating_domain";

add_filter('the_content', 'mombly_rating_addRating', 1);
add_filter('the_excerpt', 'mombly_rating_addRating', 1);
register_activation_hook(__FILE__,'mombly_rating_install');
register_deactivation_hook(__FILE__,'mombly_rating_uninstall');
add_action('admin_menu', 'mombly_rating_add_admin_pages');

class mombly_rating_vars{
	public static $useImagesContent_name = 'mr_useImagesContent';
	public static $useImagesRSS_name = 'mr_useImagesRSS';
	public static $contentTextFormat_name = 'mr_contentTextFormat';
	public static $RSSTextFormat_name = 'mr_RSSTextFormat';
	public static $starText_name = 'mr_starText';
	public static $halfStarText_name = 'mr_halfStarText';
	public static $emptyStarText_name = 'mr_emptyStarText';
	public static $maxStars_name = 'mr_maxStars';
	public static $appendScore_name = 'mr_appendScore';
	public static $starFilename_name = 'mr_starFilename';
	public static $halfStarFilename_name = 'mr_halfStarFilename';
	public static $emptyStarFilename_name = 'mr_emptyStarFilename';
}

function mombly_rating_print_head(){
	?>
		
		<style type="text/css">
		<!--
		.freqlist {
			 border-top:1px solid #647168;
		 border-right:1px solid #647168;
		 margin:1em auto;
		 border-collapse:collapse;
			
		}
		.freqlist th {
			font:bold 1.2em/2em "Century Gothic","Trebuchet MS",Arial,Helvetica,sans-serif;
			padding:4px;
			background-color: #B1BEA4;
			border-bottom-width: 1px;
			border-left-width: 1px;
			border-bottom-style: solid;
			border-left-style: solid;
			border-bottom-color: #647168;
			border-left-color: #647168;
		}
		.freqlist td {
			background-color: #F1EDC0;
			border-bottom-width: 1px;
			border-left-width: 1px;
			border-bottom-style: solid;
			border-left-style: solid;
			border-bottom-color: #647168;
			border-left-color: #647168;
			//font-family: "Century Gothic", "Trebuchet MS", Arial, Helvetica, sans-serif;
			padding:4px;

		}
		.freqlist .alt td {
			//font-family: "Century Gothic", "Trebuchet MS", Arial, Helvetica, sans-serif;
			background-color: #CED59F;
			border-bottom-width: 1px;
			border-left-width: 1px;
			border-bottom-style: solid;
			border-left-style: solid;
			border-bottom-color: #647168;
			border-left-color: #647168;
			padding:4px;
		-->
		</style>
	<?php
}

function mombly_rating_add_admin_pages() {
    // Add a new submenu under Manage:
    $plugin_page=add_options_page('Mombly Review Rating', 'Mombly Review Rating', 8, 'managemomblyrating', 'mombly_rating_manage_page');
	add_action( 'admin_head-'. $plugin_page, 'mombly_rating_print_head' );

}

function mombly_rating_manage_page() {
	global $mombly_rating_optionsdomain;
	
	$updateType_name = 'momblyrating_updateType';
	$updateSettings_name = 'momblyrating_updateSettings';
		

	// Read in existing option value from database
	$useImagesContent_val = get_option( mombly_rating_vars::$useImagesContent_name );
	$useImagesRSS_val = get_option( mombly_rating_vars::$useImagesRSS_name );
	$contentTextFormat_val = get_option( mombly_rating_vars::$contentTextFormat_name );
	$RSSTextFormat_val = get_option( mombly_rating_vars::$RSSTextFormat_name );
	$starText_val = get_option( mombly_rating_vars::$starText_name );
	$halfStarText_val = get_option( mombly_rating_vars::$halfStarText_name );
	$emptyStarText_val = get_option( mombly_rating_vars::$emptyStarText_name );
	$maxStars_val = get_option( mombly_rating_vars::$maxStars_name );
	$appendScore_val = get_option( mombly_rating_vars::$appendScore_name );
	$starFilename_val = get_option( mombly_rating_vars::$starFilename_name );
	$halfStarFilename_val = get_option( mombly_rating_vars::$halfStarFilename_name );
	$emptyStarFilename_val = get_option( mombly_rating_vars::$emptyStarFilename_name );

    // See if the user has posted us some information
    
    if( $_POST[ $updateType_name ] == $updateSettings_name ) {
		
		if(!isset($_POST[ mombly_rating_vars::$useImagesContent_name ])){
			$useImagesContent_val = 0;
		}
		else{
			$useImagesContent_val = 1;
		}
		if(!isset($_POST[ mombly_rating_vars::$useImagesRSS_name ])){
			$useImagesRSS_val = 0;
		}
		else{
			$useImagesRSS_val = 1;
		}
		if(!isset($_POST[ mombly_rating_vars::$appendScore_name ])){
			$appendScore_val = 0;
		}
		else{
			$appendScore_val = 1;
		}
		
		$contentTextFormat_val = $_POST[  mombly_rating_vars::$contentTextFormat_name ];
		$RSSTextFormat_val = $_POST[  mombly_rating_vars::$RSSTextFormat_name ];
		$starText_val = $_POST[  mombly_rating_vars::$starText_name ];
		$halfStarText_val = $_POST[  mombly_rating_vars::$halfStarText_name ];
		$emptyStarText_val = $_POST[  mombly_rating_vars::$emptyStarText_name ];
		$maxStars_val = $_POST[  mombly_rating_vars::$maxStars_name ];
		$appendScore_val = $_POST[  mombly_rating_vars::$appendScore_name ];
		$starFilename_val = $_POST[  mombly_rating_vars::$starFilename_name ];
		$halfStarFilename_val = $_POST[  mombly_rating_vars::$halfStarFilename_name ];
		$emptyStarFilename_val = $_POST[  mombly_rating_vars::$emptyStarFilename_name ];
		
        // Read their posted value
		
		//print_r( $_POST );
        // Save the posted value in the database
        update_option( mombly_rating_vars::$useImagesContent_name, $useImagesContent_val );
		update_option( mombly_rating_vars::$useImagesRSS_name, $useImagesRSS_val );
		update_option( mombly_rating_vars::$contentTextFormat_name, $contentTextFormat_val );
		update_option( mombly_rating_vars::$RSSTextFormat_name, $RSSTextFormat_val );
		update_option( mombly_rating_vars::$starText_name, $starText_val );
		update_option( mombly_rating_vars::$halfStarText_name, $halfStarText_val );
		update_option( mombly_rating_vars::$emptyStarText_name, $emptyStarText_val );
		update_option( mombly_rating_vars::$maxStars_name, $maxStars_val );
		update_option( mombly_rating_vars::$appendScore_name, $appendScore_val );
		update_option( mombly_rating_vars::$starFilename_name, $starFilename_val );
		update_option( mombly_rating_vars::$halfStarFilename_name, $halfStarFilename_val );
		update_option( mombly_rating_vars::$emptyStarFilename_name, $emptyStarFilename_val );

        // Put an options updated message on the screen

	?>
	<div class="updated"><p><strong><?php _e('Options saved.', $mombly_rating_optionsdomain ); ?></strong></p></div>
	<?php

    }
	// Now display the options editing screen

	echo '<div class="wrap">';

	// header

	echo "<h2>" . __( 'Mombly Review Rating Plugin Options', $mombly_rating_optionsdomain ) . "</h2>";

	// options form
	
	?>

	<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="<?php echo $updateType_name; ?>" value="<?php echo $updateSettings_name; ?>">
	<h3><?php _e('Common Settings', $mombly_rating_optionsdomain ); ?></h3>
	<table class="form-table" style="width:600px">
	
	<tr>
	<th scope="row"><?php _e("Star Image Filename:", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$starFilename_name; ?>" value="<?php echo $starFilename_val; ?>"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> star.png <?php _e("(All images must be in <code>/wp-content/plugins/mombly_reviewrating/images/</code> folder)", $mombly_rating_optionsdomain ); ?></td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Half Star Image Filename:", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$halfStarFilename_name; ?>" value="<?php echo $halfStarFilename_val; ?>"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> halfstar.png</td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Empty Star Image Filename:", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$emptyStarFilename_name; ?>" value="<?php echo $emptyStarFilename_val; ?>"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> emptystar.png</td>
	</tr>
	
	
	<tr>
	<th scope="row"><?php _e("Star (text version):", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$starText_name; ?>" value="<?php echo $starText_val; ?>"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> *</td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Half Star (text version):", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$halfStarText_name; ?>" value="<?php echo $halfStarText_val; ?>"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> &frac12; (&amp;frac12;)</td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Empty Star (text version):", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$emptyStarText_name; ?>" value="<?php echo $emptyStarText_val; ?>"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> ~</td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Max Stars:", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$maxStars_name; ?>" value="<?php echo $maxStars_val; ?>"><br>
		<?php _e("The maximum number of stars to display, any total more than that amount will result in the score being converted to fit the max number of stars. <br><br>E.g. 70/100 will result in a 7/10 displayed. This affects both text and images.", $mombly_rating_optionsdomain ); ?></td>
	</tr>
	</table>
	
	
	
	<h3><?php _e('Normal Post Rating Settings', $mombly_rating_optionsdomain ); ?></h3>
	<table class="form-table">

	<tr>
	<th scope="row"><label for="<?php echo mombly_rating_vars::$useImagesContent_name; ?>"><?php _e("Use Images for Post Ratings:", $mombly_rating_optionsdomain ); ?></label></th>
	<td><input type="checkbox" name="<?php echo mombly_rating_vars::$useImagesContent_name; ?>" id="<?php echo mombly_rating_vars::$useImagesContent_name; ?>" value="1" <?php echo ($useImagesContent_val==1)?"checked":""; ?>></td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo mombly_rating_vars::$appendScore_name; ?>"><?php _e("Append Score after Image:", $mombly_rating_optionsdomain ); ?></label></th>
	<td><input type="checkbox" name="<?php echo mombly_rating_vars::$appendScore_name; ?>" id="<?php echo mombly_rating_vars::$appendScore_name; ?>" value="1" <?php echo ($appendScore_val==1)?"checked":""; ?>>
	<?php _e("Adds a score after the image. E.g. [images of stars] (3.5/5)", $mombly_rating_optionsdomain ); ?></td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Normal Post Rating Text Format:", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$contentTextFormat_name; ?>" value="<?php echo $contentTextFormat_val; ?>" style="width:300px"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> -</td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Preview (Update to refresh):", $mombly_rating_optionsdomain ); ?></th>
	<td><?
		echo mombly_rating_prepareString($contentTextFormat_val, 3.5, 5);
	?></td>
	</tr>
	</table>

	<h3><?php _e('RSS Posts Rating Settings', $mombly_rating_optionsdomain ); ?></h3>
	<table class="form-table">
	
	<?php
	/* <tr>
	<th scope="row"><label for="<?php echo mombly_rating_vars::$useImagesRSS_name; ?>"><?php _e("Use Images for RSS Feed Ratings:", $mombly_rating_optionsdomain ); ?></label></th>
	<td><input type="checkbox" name="<?php echo mombly_rating_vars::$useImagesRSS_name; ?>" id="<?php echo mombly_rating_vars::$useImagesRSS_name; ?>" value="1" <?php echo ($useImagesRSS_val==1)?"checked":""; ?>></td>
	</tr> */ ?>
	<tr>
	<th scope="row"><?php _e("RSS Post Rating Text Format:", $mombly_rating_optionsdomain ); ?></th>
	<td><input type="text" name="<?php echo mombly_rating_vars::$RSSTextFormat_name; ?>" value="<?php echo $RSSTextFormat_val; ?>" style="width:300px"><br>
		<?php _e("Default:", $mombly_rating_optionsdomain ); ?> -</td>
	</tr>
	<tr>
	<th scope="row"><?php _e("Preview (Update to refresh):", $mombly_rating_optionsdomain ); ?></th>
	<td><?
		echo mombly_rating_prepareString($RSSTextFormat_val, 3.5, 5);
	?></td>
	</tr>
	</table>
	
	<h3><?php _e('Text Format Legend', $mombly_rating_optionsdomain ); ?></h3>
	<table class="freqlist">
	<tr>
	<th>Formatting Key</th>
	<th>Description</th>
	</tr>
	<tr>
	<td>%score%</td>
	<td>One number representing the score. e.g. 3.5, 4</td>
	</tr>
	<tr class="alt">
	<td>%total%</td>
	<td>One number representing the total score. e.g. 5, 10</td>
	</tr>
	<tr>
	<td>%stars%</td>
	<td>Text Stars representing the score. e.g. ***&frac12;-, ****------</td>
	</tr>
	<tr class="alt">
	<td>%starsspace%</td>
	<td>Text Stars representing the score with spacing. e.g. * * * &frac12; -, * * * * - - - - - -</td>
	</tr>
	</table>
	
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Update Options', $mombly_rating_optionsdomain ) ?>" />
	</p>

	</form>
	
	</div>

	<?php

}

function mombly_rating_prepareString($formatstring, $score, $total){
	$maxStars = get_option(mombly_rating_vars::$maxStars_name);
	$starText_val = get_option( mombly_rating_vars::$starText_name );
	$halfStarText_val = get_option( mombly_rating_vars::$halfStarText_name );
	$emptyStarText_val = get_option( mombly_rating_vars::$emptyStarText_name );
	
	$half = false;
	$emptyStars = 0;
	$actualScore = $score;
	$actualTotal = $total;
	
	if($total > $maxStars){
		$score = round($score/$total*$maxStars*10);
		$scoreDecimal = $score % 10;
		$score -= $scoreDecimal;
		$score = $score / 10;
		if(($scoreDecimal >= 3) and ($scoreDecimal <= 7)){
			$half = true;
		}
		else if($scoreDecimal > 7){
			$score++;
		}
		$total = $maxStars;
		$emptyStars = $maxStars - $score;
	}
	else{
		$score = $score*10;
		$scoreDecimal = $score % 10;
		$score -= $scoreDecimal;
		$score = $score / 10;
		if(($scoreDecimal >= 3) and ($scoreDecimal <= 7)){
			$half = true;
		}
		else if($scoreDecimal > 7){
			$score++;
		}
		$emptyStars = $total - $score;
	}
	
	
	if($half === true) $emptyStars --;
	
	$i = 0;
	$starString = "";
	$starspaceString = "";
	while($i < $score){
		$starString .= $starText_val;
		$starspaceString .= $starText_val . " ";
		$i++;
	}
	if($half === true){
		$starString .= $halfStarText_val;
		$starspaceString .= $halfStarText_val . " ";
	}
	$i = 0;
	while($i < $emptyStars){
		$starString .= $emptyStarText_val;
		$starspaceString .= $emptyStarText_val . " ";
		$i++;
	}
	$starspaceString = trim($starspaceString);
	
	$searchArr = array("%score%","%total%","%stars%","%starsspace%");
	$replaceArr = array($actualScore, $actualTotal, $starString, $starspaceString);
	$tempstring = str_replace($searchArr,$replaceArr,$formatstring);
	
	return $tempstring;
}

function mombly_rating_prepareImages($score, $total){
	global $mombly_rating_dir;
	$maxStars = get_option(mombly_rating_vars::$maxStars_name);
	$starFilename_val = get_option( mombly_rating_vars::$starFilename_name );
	$halfStarFilename_val = get_option( mombly_rating_vars::$halfStarFilename_name );
	$emptyStarFilename_val = get_option( mombly_rating_vars::$emptyStarFilename_name );
		
	$half = false;
	$emptyStars = 0;
	$actualScore = $score;
	$actualTotal = $total;
	
	if($total > $maxStars){
		$score = round($score/$total*$maxStars*10);
		$scoreDecimal = $score % 10;
		$score -= $scoreDecimal;
		$score = $score / 10;
		if(($scoreDecimal >= 3) and ($scoreDecimal <= 7)){
			$half = true;
		}
		else if($scoreDecimal > 7){
			$score++;
		}
		$total = $maxStars;
		$emptyStars = $maxStars - $score;
	}
	else{
		$score = $score*10;
		$scoreDecimal = $score % 10;
		$score -= $scoreDecimal;
		$score = $score / 10;
		if(($scoreDecimal >= 3) and ($scoreDecimal <= 7)){
			$half = true;
		}
		else if($scoreDecimal > 7){
			$score++;
		}
		$emptyStars = $total - $score;
	}
		
	if($half === true) $emptyStars --;
	
	$i = 0;
	$imageString = "";
	while($i < $score){
		$imageString .= '<img src="'.$mombly_rating_dir . '/images/' .$starFilename_val. '">';
		$i++;
	}
	if($half === true){
		$imageString .= '<img src="'.$mombly_rating_dir . '/images/' .$halfStarFilename_val. '">';
	}
	$i = 0;
	while($i < $emptyStars){
		$imageString .= '<img src="'.$mombly_rating_dir . '/images/' .$emptyStarFilename_val. '">';
		$i++;
	}
		
	return $imageString;
}

function mombly_rating_addRating($content){
	$useImagesRSS_val = get_option( mombly_rating_vars::$useImagesRSS_name );
	$RSSTextFormat_val = get_option( mombly_rating_vars::$RSSTextFormat_name );
	$useImagesContent_val = get_option( mombly_rating_vars::$useImagesContent_name );
	$contentTextFormat_val = get_option( mombly_rating_vars::$contentTextFormat_name );
	$appendScore_val = get_option( mombly_rating_vars::$appendScore_name );
	
	$matches = array();
	/* if(!preg_match  ( "/\[Rating:(\d+\.?\d*)\/(\d+\.?\d*)\]/"  , $content, &$match )){
		return $content;
	} */
	$matchint = preg_match_all("/\[Rating:(\d+\.?\d*)\/(\d+\.?\d*)\]/", $content, $matches, PREG_SET_ORDER);
	if($matchint==0){
		
		return $content;
	}
	$tempstr = $content;
	foreach ($matches as $match) {
		$score = $match[1];
		$total = $match[2];
		
		$ratingString = "";
		if(is_feed()){
			if($useImagesRSS_val == 1){
				//$ratingString = mombly_rating_prepareImages($score, $total);
			}
			else{
				$ratingString = mombly_rating_prepareString($RSSTextFormat_val, $score, $total);
			}
		}
		else{
			if($useImagesContent_val == 1){
				$ratingString = mombly_rating_prepareImages($score, $total);
				if($appendScore_val == 1){
					$ratingString .= " (".$score."/".$total.")";
				}
			}
			else{
				$ratingString = mombly_rating_prepareString($contentTextFormat_val, $score, $total);
			}
		}
		$tempstr = preg_replace("/\[Rating:".$score."\/".$total."\]/", $ratingString, $tempstr);
	}
	
	
	return $tempstr;
}

function mombly_rating_install(){

	add_option( mombly_rating_vars::$useImagesContent_name, "1" );
	add_option( mombly_rating_vars::$useImagesRSS_name, "0"	);
	add_option( mombly_rating_vars::$contentTextFormat_name, "%stars% (%score%/%total%)" );
	add_option( mombly_rating_vars::$RSSTextFormat_name, "%stars% (%score%/%total%)" );
	add_option( mombly_rating_vars::$starText_name, "*" );
	add_option( mombly_rating_vars::$halfStarText_name, "&frac12;" );
	add_option( mombly_rating_vars::$emptyStarText_name, "~" );
	add_option( mombly_rating_vars::$maxStars_name, "10" );
	add_option( mombly_rating_vars::$appendScore_name, "1" );
	add_option( mombly_rating_vars::$starFilename_name, "star.png" );
	add_option( mombly_rating_vars::$halfStarFilename_name, "halfstar.png" );
	add_option( mombly_rating_vars::$emptyStarFilename_name, "emptystar.png" );
}

function mombly_rating_uninstall(){
	
}
?>