<?php
// Includes
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
require_once(DIR_WS_CLASSES . 'wishlist_class.php');

// Instantiate
$currencies = new currencies();
$oWishlist = new un_wishlist();

// Get wishlist
$iID = (isset($_GET['wid']) ? (int)$_GET['wid'] : '');
if ($iID != '') {
  $sql = "SELECT *
          FROM " . UN_TABLE_WISHLISTS . " w,
               " . TABLE_CUSTOMERS . " c
          WHERE w.id = " . $iID . "
          AND w.customers_id = c.customers_id";

  $wishlist = $db->Execute($sql);
  if (!$wishlist) {
    $messageStack->add('header', 'Error getting wishlist.');
    return false;
  }
} else {
  zen_redirect(UN_FILENAME_WISHLISTS);
  exit;
}

// Process action
$products_id = (isset($_GET['products_id']) ? $_GET['products_id'] : '');
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (zen_not_null($action) && zen_not_null($products_id)) {
  switch ($action) {
    case 'delete':
      $oWishlist->removeProduct($products_id);
  }
}

// Get products in wishlist
if (isset($_GET['cPath']) && $_GET['cPath']) {
  $sql_filter = "AND p2c.categories_id = '" . $_GET['cPath'] . "' ";
  $sql_from = ", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ";
  $sql_where = " AND p.products_id = p2c.products_id ";
} else {
  $sql_filter = '';
  $sql_from = '';
  $sql_where = '';
}

$products_query = "SELECT p2w.products_id,w.customers_id,p2w.created,p2w.quantity,p2w.priority,p2w.comment
                   FROM " . UN_TABLE_WISHLISTS . " w,
                        " . UN_TABLE_PRODUCTS_TO_WISHLISTS . " p2w
                        " . $sql_from . "
                   WHERE w.id = " . $iID . "
                   AND w.id = p2w.un_wishlists_id
                   " . $sql_where . "
                   " . $sql_filter . "
                   ORDER BY p2w.products_id";
$products = $db->Execute($products_query);
if (!$products) {
  $messageStack->add('header', 'Error getting wishlist products.');
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script src="includes/menu.js"></script>
    <script src="includes/general.js"></script>
    <script>
      function init() {
          cssjsmenu('navbar');
          if (document.getElementById) {
              var kill = document.getElementById('hoverJS');
              kill.disabled = true;
          }
      }
    </script>
  </head>
  <body onload="init()">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <div class="container-fluid">
      <h1><?php echo HEADING_TITLE . TEXT_DELIMITER . $wishlist->fields['name']; ?></h1>

      <ul>
        <li><?php echo ENTRY_CUSTOMER; ?><a href="<?php echo zen_href_link(FILENAME_CUSTOMERS, 'cID=' . $wishlist->fields['customers_id']); ?>"><?php echo un_get_fullname($wishlist->fields['customers_firstname'], $wishlist->fields['customers_lastname'], $wishlist->fields['customers_email_address']); ?></a></li>
        <li><?php echo ENTRY_COMMENT . $wishlist->fields['comment']; ?></li>
        <li><?php echo ENTRY_DEFAULT . $wishlist->fields['default_status']; ?></li>
        <li><?php echo ENTRY_PUBLIC . $wishlist->fields['public_status']; ?></li>
      </ul>

      <!-- product listing -->
      <table class="table table-striped">
        <thead>
          <tr class="dataTableHeadingRow">
            <th><?php echo TABLE_HEADING_PRODUCT; ?></th>
            <th class="text-right"><?php echo TABLE_HEADING_PRICE; ?></th>
            <th class="text-center"><?php echo TABLE_HEADING_PRIORITY; ?></th>
            <th><?php echo TABLE_HEADING_COMMENT; ?></th>
            <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
          </tr>
        </thead>
        <tbody>
            <?php
            if ($products->RecordCount() > 0) {
              foreach ($products as $product) {
                 ?>
              <tr>
                <td><a href="<?php echo zen_href_link(FILENAME_CATEGORIES, '&action=new_product' . '&cPath=' . zen_get_product_path($product['products_id']) . '&pID=' . $product['products_id'] . '&product_type=' . zen_get_products_type($product['products_id'])); ?>"><?php echo zen_get_products_name($product['products_id'], $_SESSION['languages_id']); ?></a></td>
                <td class="text-right"><?php echo zen_get_products_actual_price($product['products_price']); ?></td>
                <td class="text-center"></td>
                <td><?php echo $product['comment']; ?></td>
                <td class="text-right"><a href="<?php echo zen_href_link(UN_FILENAME_WISHLIST, 'wid=' . $iID . '&products_id=' . $product['products_id'] . '&action=delete'); ?>" onclick="javascript:return confirm('Are you sure you want to Delete this record?')"><?php echo zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE); ?></a></td>
              </tr>
            <?php } ?>

          <?php } else { ?>
            <tr>
              <td colspan="5"><?php echo TEXT_NO_RECORDS; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <!-- end product listing -->
    </div>
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>