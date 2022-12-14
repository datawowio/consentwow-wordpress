<?php
/**
 * Consent Wow Consent Solution
 *
 * @package           consent-wow-consent-solution
 * @author            Consent Wow
 * @copyright         2022 Consent Wow
 * @license           GPL-3.0-or-later
 */

/**
 * A class to display form list table
 */
class Consent_Wow_Form_List_Table extends WP_List_Table {
  /**
   * Prepare the items for the table to process
   *
   * @return Void
   */
  public function prepare_items() {
    $columns  = $this->get_columns();
    $hidden   = array();
    $sortable = $this->get_sortable_columns();
		$primary  = 'id';
    $this->_column_headers = array( $columns, $hidden, $sortable, $primary );

    $data = $this->table_data();

    usort( $data, array( &$this, 'sort_data' ) );

    $per_page = $this->get_items_per_page( 'consentwow_forms_per_page', 20 );
    $current_page = $this->get_pagenum();
    $total_items = count( $data );

    $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
    $this->set_pagination_args(
      array(
        'total_items' => $total_items,
        'per_page'    => $per_page,
      )
    );

    $this->items  = $data;
  }

  /**
   * Defines the columns.
   *
   * @return Array
   */
  public function get_columns() {
    $columns = array(
      'cb'           => '<input type="checkbox" />',
      'id'           => __( 'ID', 'consent-wow-consent-solution' ),
      'form_name'    => __( 'Name', 'consent-wow-consent-solution' ),
      'form_id'      => __( 'Form ID', 'consent-wow-consent-solution' ),
      'status'       => __( 'Status', 'consent-wow-consent-solution' ),
      'updated_date' => __( 'Updated Date', 'consent-wow-consent-solution' ),
      'action'       => __( 'Action', 'consent-wow-consent-solution' ),
    );

    return $columns;
  }

  /**
   * Get the table data
   *
   * @return Array
   */
  private function table_data() {
    $data = get_option( 'consentwow_forms' );

    return $data;
  }

  /**
   * Generate action list for given form
   *
   * @param String $form_id
   */
  private function row_action( $form_id ) {
    $edit_form_url = admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_EDIT_SLUG . '&id=' . $form_id );
    $edit_form_link = "<a href='{$edit_form_url}'>Edit</a>";

    $delete_form_url = admin_url( 'admin.php?action=consentwow_form_delete&id=' . $form_id );
    $delete_form_link = "<a href='{$delete_form_url}'>Delete</a>";

    return array( 'edit' => $edit_form_link, 'delete' => $delete_form_link );
  }

  /**
   * Define what data to show on each column of the table
   *
   * @param  Array  $item
   * @param  String $column_name
   *
   * @return Mixed
   */
  public function column_default( $item, $column_name ) {
    switch( $column_name ) {
      case 'id':
      case 'form_name':
      case 'form_id':
        return $item[ $column_name ];
      case 'updated_date':
        $date = new DateTime(null, new DateTimeZone('Asia/Bangkok') );
        $date = date_timestamp_set($date, $item[ $column_name ]);
        return $date->format('d/m/Y H:i:s O');
      case 'status':
        $consent_purposes = get_transient( 'consentwow_consent_purposes' );
        if ( ! is_array( $consent_purposes ) ) {
          return 'Unchecked';
        }

        $is_array_condition = isset( $item['consents'] ) && is_array( $item['consents'] );
        $is_subset_condition = ! array_diff(
          array_column( $item['consents'], 'consent_id' ),
          array_column( $consent_purposes, 'consent_id' ),
        );

        if ( $is_array_condition && $is_subset_condition ) {
          return 'Passed';
        } else {
          return 'Error';
        }
      case 'action':
        return $this->row_actions( $this->row_action( $item[ 'id' ] ), true );
      default:
        return print_r( $item, true );
    }
  }

  /**
   * Define what column can be sorted.
   *
   * @return Array
   */
  public function get_sortable_columns() {
    $sortable = array(
      'id'           => array( 'id', true ),
      'form_name'    => array( 'form_name', true ),
      'form_id'      => array( 'form_id', true ),
      'updated_date' => array( 'updated_date', true ),
    );

    return $sortable;
  }

  /**
   * Sort data by the query params order and orderby
   *
   * @return Mixed
   */
  private function sort_data( $a, $b ) {
    if ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) ) {
      $orderby = sanitize_text_field( $_GET['orderby'] );
    } else {
      $orderby = 'id';
    }

    if ( isset( $_GET['order'] ) && in_array( $_GET['order'], array( 'asc', 'desc' ) ) ) {
      $order = sanitize_text_field( $_GET['order'] );
    } else {
      $order = 'asc';
    }

    $result = strcmp( $a[$orderby], $b[$orderby] );

    return ( $order === 'asc' ) ? $result : - $result;
  }

  /**
   * Get bulk actions to display on the table.
   *
   * @return object Available actions.
   *
   */
  function get_bulk_actions() {
    $actions = array(
      'delete_all' => __( 'Delete All', 'consent-wow-consent-solution' ),
    );
    return $actions;
  }

  /**
   * Custom column for bulk action feature.
   *
   * @return string Checkbox input element.
   *
   */
  function column_cb($item) {
    return '<input type="checkbox" name="consentwow_forms[]" value="' . $item['id'] . '" />';
  }
}
