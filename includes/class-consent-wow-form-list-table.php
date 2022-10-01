<?php
/**
 * Consent Wow Script Loader
 *
 * @package consent-wow-script-loader
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
      'id'           => __( 'ID', 'consentwow' ),
      'form_name'    => __( 'Name', 'consentwow' ),
      'form_id'      => __( 'Form ID', 'consentwow' ),
      'status'       => __( 'Status', 'consentwow' ),
      'updated_date' => __( 'Updated Date', 'consentwow' ),
      'action'       => __( 'Action', 'consentwow' ),
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
    $link = "<a href='{$edit_form_url}'>Edit</a>";

    return array( 'edit' => $link );
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
        return 'Online';
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
      'status'       => array( 'status', true ),
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
    $orderby = 'id';
    $order = 'asc';

    if( ! empty( $_GET['orderby'] ) ) {
        $orderby = $_GET['orderby'];
    }

    if( ! empty( $_GET['order'] ) ) {
        $order = $_GET['order'];
    }

    $result = strcmp( $a[$orderby], $b[$orderby] );

    if ( $order === 'asc' ) {
        return $result;
    } else {
      return -$result;
    }
  }
}
