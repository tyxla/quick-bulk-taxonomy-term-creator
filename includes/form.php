<?php
/**
 * Class for setting up the settings page with the main form.
 */
class QBTTC_Form {

	/**
	 * Constructor.
	 *	
	 * Initializes the admin form functionality.
	 *
	 * @access public
	 */
	public function __construct() {
		// hook the main plugin page
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );

		// register settings fields & sections
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// handle the form submission
		add_action( 'add_option_qbttc_terms', array( $this, 'handle' ), 10, 2 );
		add_action( 'update_option_qbttc_terms', array( $this, 'handle' ), 10, 2 );

		// display the plugin's notices
		add_action( 'admin_notices', array($this, 'notices') );
	}

	/**
	 * Get the title of the submenu item page.
	 *
	 * @access public
	 *
	 * @return string $menu_title The title of the submenu item.
	 */
	public function get_menu_title() {
		// allow filtering the title of the submenu page
		$menu_title = apply_filters('qbttc_menu_item_title', __('Quick Term Creator', 'qbttc'));

		return $menu_title;
	}

	/**
	 * Get the ID (slug) of the submenu item page.
	 *
	 * @access public
	 *
	 * @return string $menu_id The ID (slug) of the submenu item.
	 */
	public function get_menu_id() {
		return 'qbttc';
	}

	/**
	 * Register the main plugin submenu page.
	 *
	 * @access public
	 */
	public function add_submenu_page() {
		$menu_title = $this->get_menu_title();
		$menu_id = $this->get_menu_id();

		// register the submenu page - child of the Settings parent menu item
		add_submenu_page(
			'tools.php',
			$menu_title,
			$menu_title,
			'manage_categories',
			$menu_id,
			array($this, 'render')
		);

		// register settings section
		add_settings_section(
			$menu_id,
			'',
			'',
			$menu_id
		);

	}

	/**
	 * Get field data. Defines and describes the fields that will be registered.
	 *
	 * @access public
	 *
	 * @return array $fields The fields and their data.
	 */
	public function get_field_data() {
		return array(
			'hierarchy_indent_character' => array(
				'type' => 'text',
				'title' => __('Hierarchy Indent Character', 'qbttc'),
				'default' => '*',
				'help' => __('You can use this character at the beginning of your term to specify hierarchy indentation.', 'qbttc'),
				'required' => true,
			),
			'taxonomy' => array(
				'type' => 'select',
				'title' => __('Taxonomy', 'qbttc'),
				'default' => 'category',
				'help' => __('The taxonomy that you want to bulk insert terms into.', 'qbttc'),
				'options' => QBTTC_Taxonomies::get_taxonomies(),
				'required' => true,
			),
			'terms' => array(
				'type' => 'textarea',
				'title' => __('Terms (one per line)', 'qbttc'),
				'default' => '',
				'help' => 
					__('A hierarchical list of your terms.', 'qbttc') . 
					'<br /><br />' . 

					__('Example 1: This will create 3 terms with the corresponding titles:', 'qbttc') . '<br />' . 
					'<strong style="padding-left: 18px; display: block;">Term 1<br />Term 2<br />Term 3</strong>' . 
 					'<br />' . 

					__('Example 2: This will create 5 terms with the corresponding titles in the corresponding hierarchy:', 'qbttc') . '<br />' . 
					'<strong style="padding-left: 18px; display: block;">Term X<br />* Term X1<br />** Term X1a<br />* Term X2<br />Term Y</strong>' . 
					__('Term X1 is a child of X, while X1a is a child of X1 (considering that the asterisk is used as hierarchy indentation character).', 'qbttc'),
				'required' => true,
			),
		);
	}

	/**
	 * Register the settings sections and fields.
	 *
	 * @access public
	 */
	public function register_settings() {
		// register fields
		$field_data = $this->get_field_data();
		foreach ($field_data as $field_id => $field) {
			$field_object = QBTTC_Field::factory($field['type'], 'qbttc_' . $field_id, $field['title'], $this->get_menu_id(), $this->get_menu_id());
			if (isset($field['options'])) {
				$field_object->set_options($field['options']);
			}
			$this->fields[] = $field_object;
		}
	}

	/**
	 * Render the settings page with the form.
	 *
	 * @access public
	 */
	public function render() {
		global $qbttc;

		// determine the form template
		$template = $qbttc->get_plugin_path() . '/templates/form.php';
		$template = apply_filters('qbttc_main_template', $template);

		// render the form template
		include_once($template);
	}

	/**
	 * Display the errors/notices of this plugin.
	 *
	 * @access public
	 */
	public function notices() {
		settings_errors( 'qbttc' );
	}

	/**
	 * Handle the form submission.
	 * Should be hooked on the update_option of the last form field.
	 *
	 * @param string $placeholder Either an option name or the old option value.
	 * @param string $terms_raw The new terms.
	 * @access public
	 */
	public function handle($placeholder, $terms_raw) {

		// prevent recursion
		remove_action( 'update_option_qbttc_terms', array( $this, 'handle' ) );

		// get the terms
		$terms_raw = get_option('qbttc_terms');

		// generate the terms hierachy
		$hierarchy = new QBTTC_Hierarchy();
		$hierarchy->set_character( get_option('qbttc_hierarchy_indent_character') );
		$hierarchy->set_text( $terms_raw );
		$hierarchy->build();

		// determine taxonomy
		$taxonomy = get_option('qbttc_taxonomy');
		if (!$taxonomy) {
			$taxonomy = 'category';
		}

		// insert the terms hierarchy
		$total_terms = QBTTC_Taxonomies::process_hierarchy($hierarchy->get_hierarchy(), $taxonomy);

		// empty the terms field
		update_option('qbttc_terms', '');

		// add success notice
		$notice = sprintf( _n('1 term inserted.', '%s terms inserted.', $total_terms, 'qbttc'), $total_terms );

		// append existing terms to the notice
		if (QBTTC_Taxonomies::$existing_terms) {
			$total_existing_terms = count(QBTTC_Taxonomies::$existing_terms);
			$notice .= '<br /><br />';
			$notice .= sprintf( _n('The following term exists, so it was not created:', 'The following terms exist, so they were not created:', $total_existing_terms, 'qbttc'), $total_existing_terms );
			$notice .= '<ul style="list-style-type: disc; font-style: italic; padding-left: 32px;">';
			foreach (QBTTC_Taxonomies::$existing_terms as $title) {
				$notice .= '<li>' . $title . '</li>';
			}
			$notice .= '</ul>';
		}

		// display notice
		add_settings_error('qbttc', 'settings_updated', $notice, 'updated ');
	}

}