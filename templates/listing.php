<table class="wlclisting">
  <thead>
    <tr>
      <th><?php esc_html_e( 'First Name', 'wlcform' ); ?></th>
      <th><?php esc_html_e( 'Last Name', 'wlcform' ); ?></th>
      <th><?php esc_html_e( 'Email', 'wlcform' ); ?></th>
      <th><?php esc_html_e( 'Subject', 'wlcform' ); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($first_page as $entry) {
      echo App\WLCFORM\include_template('listing-entry', array('entry' => $entry));
    }
    ?>
  </tbody>
</table>
<?php
  $total_pages = ceil($total_entries / 10);
  $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
  $show_first_ellipsis = $show_last_ellipsis = true;
?>
<div class="wlclisting__pagination" data-total-pages="<?php echo $total_pages; ?>">
  <?php  
    for ($i = 1; $i <= $total_pages; $i++) {
      if ($i == 1 || $i == $total_pages || $i == $current_page || abs($i - $current_page) < 2) {
        $class = ($i == $current_page) ? 'current' : '';
        echo '<a href="#" class="wlclisting__pagination-page ' . $class . '" data-page="' . $i . '">' . $i . '</a>';
        $show_first_ellipsis = ($i > 2 && $i != $current_page + 1) ? true : false;
        $show_last_ellipsis = ($i < $total_pages - 1 && $i != $current_page - 1) ? true : false;
      } elseif ($show_first_ellipsis && $i < $current_page) {
        echo '<span class="wlclisting__pagination-ellipsis">...</span>';
        $show_first_ellipsis = false;
      } elseif ($show_last_ellipsis && $i > $current_page) {
        echo '<span class="wlclisting__pagination-ellipsis">...</span>';
        $show_last_ellipsis = false;
      }
    }
  ?>
</div>
<div class="wlclisting__entry-details"></div>
