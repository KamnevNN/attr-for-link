<?php

class AFL_Admin
{

	public function __construct()
	{
		add_action('admin_menu', array($this, 'add_page'));
		add_action('admin_init', array($this, 'plugin_setting'));
	}

	public function add_page()
	{
		add_options_page('Настройки AFL', 'Afl', 'manage_options', 'afl_setting', array($this, 'setting_output'));
	}

	public function setting_output()
	{
?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>

			<form action="options.php" method="POST">
				<?php
				settings_fields('afl_options');
				do_settings_sections('afl_page');
				submit_button();
				?>
			</form>
		</div>
<?php
	}

	public function plugin_setting()
	{
		register_setting('afl_options', 'afl_options', array($this, 'sanitize_callback'));

		add_settings_section('section_afl', 'Основные настройки', '', 'afl_page');

		add_settings_field('afl_post_type', 'Название опции', array($this, 'field_afl_post_type'), 'afl_page', 'section_afl');

		$field_afl_post_type = array(
			'type'      => 'checkbox',
			'id'        => 'field_afl_post_type',
			'desc'      => 'К каким типам постов применять плагин',
			'vals'		=> get_post_types()
		);
		add_settings_field('afl_post_type', 'Типы постов: ', array($this, 'option_display_settings'), 'afl_page', 'section_afl', $field_afl_post_type);

		$field_afl_attrs = array(
			'type'      => 'checkbox',
			'id'        => 'field_afl_attrs',
			'desc'      => 'Какие атрибуты применить к внешним ссылкам',
			'vals'		=> array('nofollow' => 'rel=nofollow', 'blank' => 'target=_blank')
		);
		add_settings_field('afl_attrs', 'Применить атрибуты: ', array($this, 'option_display_settings'), 'afl_page', 'section_afl', $field_afl_attrs);
	}

	public function option_display_settings($args)
	{
		extract($args);

		$option_name = 'afl_options';

		$o = get_option($option_name);

		switch ($type) {
			case 'text':
				$o[$id] = esc_attr(stripslashes($o[$id]));
				echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
				break;
			case 'textarea':
				$o[$id] = esc_attr(stripslashes($o[$id]));
				echo "<textarea class='code large-text' cols='50' rows='10' type='text' id='$id' name='" . $option_name . "[$id]'>$o[$id]</textarea>";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
				break;
			case 'checkbox':
				/*
				$checked = ($o[$id] == 'on') ? " checked='checked'" :  '';
				echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked /> ";
				echo ($desc != '') ? $desc : "";
				echo "</label>";
				*/
				echo "<fieldset>";
				foreach ($vals as $v => $l) {
					$checked = '';
					if (!empty($o[$id])) $checked = (in_array($v, $o[$id])) ? "checked='checked'" : '';
					echo "<label><input type='checkbox' name='" . $option_name . "[$id][]' value='$v' $checked />$l</label><br />";
				}
				echo "</fieldset>";
				break;
			case 'select':
				echo "<select id='$id' name='" . $option_name . "[$id]'>";
				foreach ($vals as $v => $l) {
					$selected = ($o[$id] == $v) ? "selected='selected'" : '';
					echo "<option value='$v' $selected>$l</option>";
				}
				echo ($desc != '') ? $desc : "";
				echo "</select>";
				break;
			case 'radio':
				echo "<fieldset>";
				foreach ($vals as $v => $l) {
					$checked = ($o[$id] == $v) ? "checked='checked'" : '';
					echo "<label><input type='radio' name='" . $option_name . "[$id]' value='$v' $checked />$l</label><br />";
				}
				echo "</fieldset>";
				break;
		}
	}

	public function sanitize_callback($options)
	{

		return $options;
	}
}
