<?php
/*
Plugin Name: CSFD Last Seen
Plugin URI: http://wordpress.org/extend/plugins/csfd-last-seen
Description: Adds a widget, which shows the last X movies rated on CSFD.cz (Czech-Slovak movie database).
Version: 1.8.2
Author: Josef Štěpánek
Author URI: http://josefstepanek.cz


Copyright 2009	Josef Štěpánek	(email : info@josefstepanek.cz)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	 02110-1301	 USA
*/


// Default settings
$instance_default = array(
	'title' => __('Naposledy jsem viděl', 'csfdLastSeen'),
	'rating_url' => 'https://www.csfd.cz/uzivatel/29153-joste/',
	'rating_num' => 10,
	'update_int' => 2,
	'display_color' => true,
	'display_year' => true,
	'rel_nofollow' => false,
	'csfd_data' => '<p>Probíhá první nastavování pluginu. Zkuste stránku aktualizovat.</p>',
	'id' => '__i__'
);


class csfdLastSeen extends WP_Widget {


	public function __construct() {
		$widget_ops = array('description' => __( 'Widget, který zobrazuje vaše poslední ohodnocené filmy na CSFD.cz' ) );
		$control_ops = array('width' => 300);
		parent::__construct('csfdLastSeen', __('CSFD Last Seen'), $widget_ops, $control_ops);
	}


	public function widget($args, $instance) {
		extract($args,EXTR_SKIP);

		$data = get_transient( $instance['id'] );

		// Update data from CSFD.cz
		if( $data === false ) {

			$url = $instance['rating_url'].'/hodnoceni/';
			$url = str_replace('//hodnoceni','/hodnoceni',$url);
			$url = str_replace('//','//www.',str_replace('//www.','//',$url));
			$url = str_replace('http:','https:',$url);

			$ch = curl_init();

			if($ch) {

				$timeout = 5;
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$csfd_data = curl_exec($ch);
				curl_close($ch);

				// data parsing
				$csfd_data_start = strpos( $csfd_data, '<tbody>' );
				$csfd_data_end = strpos( $csfd_data, '</tbody>' );
				$csfd_data = substr( $csfd_data, $csfd_data_start, $csfd_data_end - $csfd_data_start );
				$csfd_data = str_replace( 'href="/', 'href="//csfd.cz/', $csfd_data );
				$csfd_data = str_replace( '	 ','', $csfd_data );
				$csfd_data = explode("\n", $csfd_data);


				$j=0;
				for($i=0;$i<count($csfd_data);$i++) {
					if ($csfd_data[$i] == '') continue;
					if ($j>$instance['rating_num']) continue;
					if ($i%6 == 0) $j++;
					$csfd_data_parsed .= $csfd_data[$i];
				}

				// clean html code a little bit
				$csfd_data_parsed .= '<style type="text/css">table#csfd, table#csfd td { border: none; border-collapse: collapse; }</style>';
				$csfd_data_parsed = str_replace('<a', '<a target="_blank" title="Otevřít detail filmu na ČSFD (v novém okně)"', $csfd_data_parsed);
				$csfd_data_parsed = preg_replace("/<td>.{10}<\/td>/", "", $csfd_data_parsed); // remove date
				$csfd_data_parsed = str_replace('class="odd"', '', $csfd_data_parsed);
				$csfd_data_parsed = '<table id="csfd" data-last-update="'.date('j. n. Y, G:i:s').'" data-source="'.$url.'">'.$csfd_data_parsed.'</table>';
				$csfd_data_parsed = str_replace('<tr></table>','</table>',$csfd_data_parsed);

				// parsing according to options
				if( $instance['display_color'] == false ) {
					$csfd_data_parsed = preg_replace("/ class=\"film.{3}\"/", '', $csfd_data_parsed);
				} else {
					$csfd_data_parsed = preg_replace('/film c([0-9])\"/', 'film c$1" style="background:url(//img.csfd.cz/assets/b1021/images/rating/colors/c$1.gif) 0 2px no-repeat;padding-left:15px;"', $csfd_data_parsed);
				}

				$csfd_data_parsed = str_replace('height="9">','height="9" />',$csfd_data_parsed);
				$csfd_data_parsed = str_replace('alt="*">','alt="*" />',$csfd_data_parsed);

				if( $instance['display_year'] == false ) {
					$csfd_data_parsed = preg_replace("/<span class=\"film-year\">.{6}<\/span>/", '</a></td>', $csfd_data_parsed);
				}
				if( $instance['rel_nofollow'] == true ) {
					$csfd_data_parsed = str_replace('<a', '<a rel="nofollow"', $csfd_data_parsed);
				}
				$csfd_data_parsed = preg_replace("/http:\/\/img\.csfd\.cz/", "//img.csfd.cz", $csfd_data_parsed);

				$url = $instance['rating_url'].'/hodnoceni';
				$url = str_replace('//hodnoceni','/hodnoceni/',$url);
				$csfd_data_parsed .= '<p><a href="'.$url.'" target="_blank" rel="nofollow" title="Zobrazit starší hodnocení na CSFD.cz (v novém okně)">Starší hodnocení na ČSFD »</a></p>';
				$csfd_data_parsed .= '<!-- CSFD Last Seen WordPress Plugin by JosefStepanek.cz -->';
				$csfd_data_parsed .= '<!-- Data naposledy nactena '.date('j. n. Y, G:i:s').' z '.$url.' -->';
				$data = $csfd_data_parsed;

			}

			set_transient( $instance['id'], $data, $instance['update_int']*60*60 );
		}

		echo $before_widget;
		echo $before_title . $instance['title'] . $after_title;
		echo $data;
		echo $after_widget;
	}


	public function update($new_instance, $old_instance) {

		global $instance_default;
		if( !isset($new_instance['title']) ) {
			return false;
		}

		$instance = $old_instance;
		$instance['title'] = wp_specialchars( $new_instance['title'] );
		$instance['rating_url'] = wp_specialchars( $new_instance['rating_url'] );
		$instance['rating_num'] = wp_specialchars( $new_instance['rating_num'] );
		$instance['update_int'] = wp_specialchars( $new_instance['update_int'] );
		$instance['display_color'] = (isset($new_instance['display_color']) ? true : false);
		$instance['display_year'] = (isset($new_instance['display_year']) ? true : false);
		$instance['rel_nofollow'] = (isset($new_instance['rel_nofollow']) ? true : false);
		$instance['csfd_data'] = $instance['csfd_data'];

		$instance['id'] = $this->id;
		delete_transient( $this->id );

		foreach($instance as $opt_name => &$value) { // Set default values to empty options
			if( $value==='' ) {
				$value = $instance_default[$opt_name];
			}
		}

		return $instance;
	}


	public function form($instance) {

		global $instance_default;

		if(!isset($instance['title'])) {
			$instance = $instance_default;
		}

		?>
		<p><label for="<?php echo $this->get_field_id('title') ?>"><?php _e('Nadpis:'); ?></label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" value="<?php echo htmlspecialchars($instance['title'],ENT_QUOTES) ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('rating_url') ?>"><?php _e('Adresa profilu na CSFD.cz:'); ?></label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id('rating_url') ?>" name="<?php echo $this->get_field_name('rating_url') ?>" value="<?php echo htmlspecialchars($instance['rating_url'],ENT_QUOTES) ?>" />
		<br /><small class="setting-description"><em>Např. https://csfd.cz/uzivatel/29153-joste/</em></small></p>

		<p><label for="<?php echo $this->get_field_id('rating_num') ?>"><?php _e('Počet zobrazených filmů: '); ?></label>
		<input size="3" type="text" id="<?php echo $this->get_field_id('rating_num') ?>" name="<?php echo $this->get_field_name('rating_num') ?>" value="<?php echo htmlspecialchars($instance['rating_num'],ENT_QUOTES) ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('update_int') ?>"><?php _e('Aktualizovat po '); ?>
		<input size="3" type="text" id="<?php echo $this->get_field_id('update_int') ?>" name="<?php echo $this->get_field_name('update_int') ?>" value="<?php echo htmlspecialchars($instance['update_int'],ENT_QUOTES) ?>" /> hod.</label></p>

		<p><input type="checkbox" id="<?php echo $this->get_field_id('display_color') ?>" name="<?php echo $this->get_field_name('display_color') ?>"<?php echo ($instance['display_color']==true ? ' checked value="true"' : ' value="false"') ?> />
		<label for="<?php echo $this->get_field_id('display_color') ?>"><?php _e('Zobrazovat barvu filmu'); ?></label>
		<br />
		<input type="checkbox" id="<?php echo $this->get_field_id('display_year') ?>" name="<?php echo $this->get_field_name('display_year') ?>"<?php echo ($instance['display_year']==true ? ' checked value="true"' : ' value="false"') ?> />
		<label for="<?php echo $this->get_field_id('display_year') ?>"><?php _e('Zobrazovat rok vydání filmu'); ?></label>
		<br />
		<input type="checkbox" id="<?php echo $this->get_field_id('rel_nofollow') ?>" name="<?php echo $this->get_field_name('rel_nofollow') ?>"<?php echo ($instance['rel_nofollow']==true ? ' checked value="true"' : ' value="false"') ?> />
		<label for="<?php echo $this->get_field_id('rel_nofollow') ?>"><?php _e('Přidat odkazům <code title="Nastavení pro SEO">rel="nofollow"</code>'); ?></label></p>

		<input type="hidden" id="<?php echo $this->get_field_id('submit') ?>" name="<?php echo $this->get_field_name('submit') ?>" value="1" />
		<?php
	}


}


function csfdLastSeen_init() {
	register_widget('csfdLastSeen');
}
add_action('widgets_init', 'csfdLastSeen_init');



?>