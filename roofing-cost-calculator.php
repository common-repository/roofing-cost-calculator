<?php

/*

Plugin Name: Roofing Calculator Widget

Plugin URI: http://www.vbpinfotech.com

Description: (399 MAX CHARACTERS), SHOWS ON DESCRIPTION AFTER YOU CLICK FROM PLUGIN DIRECTORY and WHEN SOMEONE SEARCHES THROUGH WORDPRESS) Mortgage Loan Calculator is a sidebar Widget that calculates your principal and interest mortgage loan payment.  You insert the loan amount and interest rate (pop up tells you to insert a number if you put anything other than a number in), choose the length of the loan from a drop-down, and press the calculate button.  Your payment information is displaye

Version: 1.0   

Author: Domain vbpinfotech

Author URI: http://www.vbpinfotech.com

*/

add_action("widgets_init", array('Roofing_Calculator_Widget', 'register'));

register_activation_hook( __FILE__, array('Roofing_Calculator_Widget', 'activate'));

register_deactivation_hook( __FILE__, array('Roofing_Calculator_Widget', 'deactivate'));





add_action('init', 'add_rcw_javascript');



function add_rcw_javascript()

{

   if ( is_admin() )

   {

      wp_enqueue_script ('calc-colorpicker', WP_PLUGIN_URL . '/roofing-calculator-widget/js/plugins/colorpicker/colorpicker.js', array('jquery'));

      wp_enqueue_script ('calcs', WP_PLUGIN_URL . '/roofing-calculator-widget/js/calcs.js', array('jquery'));

      wp_enqueue_style('colorpicker-styles', WP_PLUGIN_URL . '/roofing-calculator-widget/js/plugins/colorpicker/css/colorpicker.css');

      wp_enqueue_style('calc-styles', WP_PLUGIN_URL . '/roofing-calculator-widget/css/calcs.css');

   }

   else

   {

      wp_enqueue_script ('jquery');

   }

}



class Roofing_Calculator_Widget {

   function activate()

   {

      $data = array( 'title' => 'Roofing Calculator', 'allowLink'=>'yes');

      if ( !get_option('Roofing_Calculator_Widget')){

         add_option('Roofing_Calculator_Widget' , $data);

      } else {

        update_option('Roofing_Calculator_Widget' , $data);

      }

   }



   function deactivate(){

      delete_option('Roofing_Calculator_Widget');

   }



   function control(){

      $data = get_option('Roofing_Calculator_Widget');

   ?>

     <p><label>Title</label><input name="title" class="widefat" type="text" value="<?php echo $data['title']; ?>" /></p>





     <div class="colorHolder"><div class="colorSelector" id="bgColorSelector"><div id="widgetBackground" style="background-color: <?php echo $data['bgcolor']; ?>"></div></div> <span>Widget Start Background Color</span> </div>

     <div class="colorHolder"><div class="colorSelector" id="bgEndColorSelector"><div id="widgetEndBackground" style="background-color: <?php echo $data['bgendcolor']; ?>"></div></div> <span>Widget End Background Color</span> </div>

     <div class="colorHolder"><div class="colorSelector" id="textColorSelector"><div id="widgetText" style="background-color: <?php echo $data['textcolor']; ?>"></div></div> <span>Widget Text Color</span></div>

     <input name="bgcolor" type="hidden" value="<?php echo $data['bgcolor']; ?>" /></label>

     <input name="bgendcolor" type="hidden" value="<?php echo $data['bgendcolor']; ?>" />

     <input name="textcolor" type="hidden" value="<?php echo $data['textcolor']; ?>" />

     <div id="bmiCalcDemo" style="color: <?php echo $data['textcolor']; ?>; border: 1px solid rgba(21, 11, 11, 0.199219); padding: 5px; width: 200px; -moz-border-radius: 12px; -webkit-border-radius: 12px; border-radius: 12px; -moz-box-shadow: 0px 0px 4px #ffffff; -webkit-box-shadow: 0px 0px 4px #ffffff; box-shadow: 0px 0px 4px #ffffff; background-color: <?php echo $data['bgcolor']; ?>; background-image: -moz-linear-gradient(top, <?php echo $data['bgcolor']; ?>, <?php echo $data['bgendcolor']; ?>); background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, <?php echo $data['bgcolor']; ?>),color-stop(1, <?php echo $data['bgendcolor']; ?>)); filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $data['bgcolor']; ?>', EndColorStr='<?php echo $data['bgendcolor']; ?>'); -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $data['bgcolor']; ?>', EndColorStr='<?php echo $data['bgendcolor']; ?>')\"; text-shadow: 1px 1px 3px #888;">

        Widget Color Preview

     </div>

     <p><label>Allow link to bartonroof.com<input name="allowLink" type="checkbox" value="yes" <?= $data['allowLink'] == 'yes' ? "checked" : "" ?>/></label></p>

     <?php

      if (isset($_POST['title'])){

       $data['title'] = attribute_escape($_POST['title']);

    

       $data['textcolor'] = attribute_escape($_POST['textcolor']);

       $data['bgcolor'] = attribute_escape($_POST['bgcolor']);

       $data['bgendcolor'] = attribute_escape($_POST['bgendcolor']);

     $data['allowLink'] = attribute_escape($_POST['allowLink']);

       update_option('Roofing_Calculator_Widget', $data);

     }

  }

  function widget($args){

           extract( $args );

           $options = get_option('Roofing_Calculator_Widget', $data);

           extract($options);

           ?>

                 <?php echo $before_widget; ?>

<script type="text/javascript">



			jQuery(document).ready(function($){



				var pitches = {

					'4/12': {

						angle: 15,

						keep_roof_price: 1.65,

						remove_roof_price: 2.7

					},

					'6/12': {

						angle: 22.5,

						keep_roof_price: 1.69,

						remove_roof_price: 2.74

					},

					'8/12': {

						angle: 30,

						keep_roof_price: 1.78,

						remove_roof_price: 2.98

					},

					'10/12': {

						angle: 37.5,

						keep_roof_price: 2.17,

						remove_roof_price: 3.67

					},

				};



				var updateEstimate = function(){

					function isNumber(n) {

						return !isNaN(parseFloat(n)) && isFinite(n);

					}

					var length = $('[name=length]').val();

					var width  = $('[name=width]').val();

					var pitch_str = $('[name=pitch]:checked').val();

					var removal_option = $('[name=removal]:checked').val();



					var pitch_angle = pitches[pitch_str]['angle'];

					var pitch_angle_radians = pitch_angle / 57.29577;

					var span = width / Math.cos(pitch_angle_radians);

					var area = span * length;

					

					var price_per_sq = pitches[pitch_str][removal_option+'_roof_price'];



					var price = area * price_per_sq;

					var price_formatted = '$' + price.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

					

					// validate inputs before displaying price

					if (isNumber(price) && price > 0) {

						$('#total, [name=total]').val(price_formatted);

					}

				};



				updateEstimate();

				

				$('[name=width], [name=length], [name=pitch], [name=removal]').bind('change keyup', updateEstimate);



			});

		</script><style type="text/css">

			#roofing-calculator{

				font-family: arial, sans-serif;

			}

			label.radio{

				margin: .2em;

				display: block;

			}

			.label{

				font-weight: bold;

				display: block;

			}

			@media screen and (max-width: 300px){

				body{

					font-size: .8em;

				}

			}

		</style>

      <table style="color: <?= $textcolor ? $textcolor : "#ffffff" ?>; padding: 10px; margin: 0; width: 100%; font-size: 9pt; background-color: <?php echo $bgcolor ? $bgcolor : '#3399CC' ?>; background-image: -moz-linear-gradient(top, <?php echo $bgcolor ? $bgcolor : '#3399CC' ?>, <?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>); background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, <?php echo $bgcolor ? $bgcolor : '#3399CC' ?>),color-stop(1, <?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>)); filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $bgcolor ? $bgcolor : '#3399CC' ?>', EndColorStr='<?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>'); -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $bgcolor ? $bgcolor : '#3399CC' ?>', EndColorStr='<?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>')\"; text-shadow: 1px 1px 3px #888;" id="bmiTable">

         <tbody>

            <tr><td colspan="2" align="center"><?=$args['before_title'];?> <?= $title ?><?=$args['after_title'];?>

        

        <div id="roofing-calculator">

			<p>

				<label>

					<span class="label">Length (in feet)</span>

					<input type="text" value="25" style="width: 5em; text-align: right;" name="length">

				</label>

			</p>

			<p>

				<label>

					<span class="label">Width (in feet)</span>

					<input type="text" value="30" style="width: 5em; text-align: right;" name="width">

				</label>

			</p>

			<p>

				<span class="label">Pitch</span>

				<input type="radio" id="radio_pitch_1" value="4/12" checked="checked" name="pitch">

		<label for="radio_pitch_1">4/12 (15°)</label><br><input type="radio" id="radio_pitch_2" value="6/12" name="pitch">

		<label for="radio_pitch_2">6/12 (22.5°)</label><br><input type="radio" id="radio_pitch_3" value="8/12" name="pitch">

		<label for="radio_pitch_3">8/12 (30°)</label><br><input type="radio" id="radio_pitch_4" value="10/12" name="pitch">

		<label for="radio_pitch_4">10/12 (37.5°)</label><br>			</p>

			<p>

				<span class="label">Roof Removal</span>

				<input type="radio" id="radio_removal_1" value="keep" checked="checked" name="removal">

		<label for="radio_removal_1">Shingle over existing roof (Must have only one layer existing)</label><br><input type="radio" id="radio_removal_2" value="remove" name="removal">

		<label for="radio_removal_2">Tear off existing roof</label><br>			</p>

			<p>

				<span class="label">Total Estimate</span>

				<input type="text" style="width: 5em; font-size: 150%; text-align: right;" id="total" disabled="disabled">

				<input type="hidden" name="total" value="$2,051">

			</p>  <? if($allowLink == 'yes') { ?>
            <a href="http://bartonroof.com/roofing-calculator" target="_blank" >Need A Roofing Calculator?</a>
  <? } ?>
		</div>

        

        </td></tr>

        

      </tbody></table>

      

      

      

                 <?php echo $after_widget; ?>

           <?php

	}



  function register(){

    register_sidebar_widget('Roofing_Calculator', array('Roofing_Calculator_Widget', 'widget'));

    register_widget_control('Roofing_Calculator', array('Roofing_Calculator_Widget', 'control'));

  }

}

?>