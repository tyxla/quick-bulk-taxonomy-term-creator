<?php
/**
 * Handles functionality, related to term taxonomies.
 */
class QBTTC_Taxonomies {

	/**
	 * Will contain any terms that already exist and haven't been created
	 *
	 * @var string
	 */
	public static $existing_terms = array();

	/**
	 * Retrieve the available taxonomies.
	 *
	 * @access public
	 * @static
	 *
	 * @return array $taxonomies Retrieve the available taxonomies.
	 */
	public static function get_taxonomies() {
		$taxonomies = array();

		$all_taxonomies = get_taxonomies(array(), 'objects');
		foreach ($all_taxonomies as $taxonomy_name => $taxonomy_object) {
			$taxonomies[$taxonomy_name] = $taxonomy_object->labels->name;
		}

		return $taxonomies;
	}

	/**
	 * Using an array hierarchy, insert the terms hierarchy.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $hierarchy Hierarchy of terms to insert.
	 * @param string $taxonomy Term type of the terms.
	 * @param int $parent ID of the parent entry.
	 * @return int $total Number of terms that were inserted.
	 */
	public static function process_hierarchy($hierarchy = array(), $taxonomy = 'taxonomy', $parent = 0) {
		$total = 0;
		foreach ($hierarchy as $hierarchy_entry) {
			$id = self::insert($taxonomy, $hierarchy_entry['title'], $parent);
			if ($id) {
				$total++;
			} else {
				QBTTC_Taxonomies::$existing_terms[] = $hierarchy_entry['title'];
			}

			if ( !empty($hierarchy_entry['children']) ) {
				$total += self::process_hierarchy($hierarchy_entry['children'], $taxonomy, $id);
			}
		}

		return $total;
	}

	/**
	 * Insert a term of certain taxonomy with a certain title under a specific parent.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $taxonomy Term taxonomy.
	 * @param string $title Title of the term.
	 * @param int $parent ID of the parent term.
	 * @return int $id The ID of the inserted term.
	 */
	public static function insert($taxonomy, $title, $parent = 0) {
		$term = wp_insert_term(
			$title,
			$taxonomy,
			array(
				'parent' => $parent
			)
		);

		// handling existing terms
		if (is_wp_error($term)) {
			return false;
		}

		return $term['term_id'];
	}

}