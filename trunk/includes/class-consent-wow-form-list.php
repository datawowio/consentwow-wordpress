<?php
/**
 * Consent Wow Plugin
 *
 * @package           consent-wow-plugin
 * @author            Consent Wow
 * @copyright         2022 Consent Wow
 * @license           GPL-3.0-or-later
 */

/**
 * A class to stores and fetches form list
 */
class Consent_Wow_Form_List {
  public $forms;
  private $option_name = 'consentwow_forms';
  private $option_next_id_name = 'consentwow_forms_next_id';

  public function __construct() {
    $this->forms = get_option( $this->option_name );
  }

  /**
   * Add a new form to the list.
   *
   * @param object $form A form to be added to the list.
   */
  public function add( $form ) {
    $id = get_option( $this->option_next_id_name );
    $form['id'] = $id;
    array_push( $this->forms, $form );
    update_option( $this->option_name, $this->forms );
    update_option( $this->option_next_id_name, $id + 1 );
  }

  /**
   * Update an existing form in the list.
   *
   * @param string $id   An ID of the form to be updated.
   * @param object $form A form to be added to the list.
   */
  public function update( $id, $form ) {
    $index = array_search( $id, array_column( $this->forms, 'id' ) );
		$this->forms = array_replace( $this->forms, array( $index => $form ) );
    update_option( $this->option_name, $this->forms );
  }

  /**
   * Delete a specific form from the list.
   *
   * @param string $id An ID of the form to be deleted.
   */
  public function delete( $id ) {
    $index = array_search( $id, array_column( $this->forms, 'id' ) );
    array_splice( $this->forms, $index, 1 );
    update_option( $this->option_name, $this->forms );
  }

  /**
   * Delete specific forms from the list.
   *
   * @param array $ids A list of ID of the form to be deleted.
   */
  public function delete_many( $ids ) {
    $this->forms = array_filter(
      $this->forms,
      function ( $item ) use ( $ids ) {
        return ! in_array( $item['id'], $ids );
      }
    );

    update_option( $this->option_name, $this->forms );
  }

  /**
   * Find and return the next ID from the list.
   *
   * @return string ID.
   */
  public function next_id() {
    $max_id = max( array_column( $this->forms, 'id' ) );
    return $max_id + 1;
  }

  /**
   * Find and return a form that has the same ID with the param.
   *
   * @param string $id An ID to be searched.
   *
   * @return object A form with the same ID or a null.
   */
  public function find( $id ) {
    $index = array_search( $id, array_column( $this->forms, 'id' ) );
    return $this->forms[ $index ];
  }
}
