<?php
// Includes
require('includes/application_top.php');

// Process action
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (zen_not_null($action)) {
  switch ($action) {
    case 'delete':
      $iID = (int)$_GET['wid'];
      if (isset($iID) && trim($iID) != '') {

        $sql = "DELETE FROM " . UN_TABLE_PRODUCTS_TO_WISHLISTS . "
                WHERE un_wishlists_id = " . $iID;
        $result = $db->Execute($sql);
      }
      if (!$result) {
        $messageStack->add('header', 'Error deleting products from wishlist.');
        return false;
      }

      $sql = "DELETE FROM " . UN_TABLE_WISHLISTS . "
              WHERE id = " . $iID;

      $result = $db->Execute($sql);
      if (!$result) {
        $messageStack->add('header', 'Error deleting wishlist.');
        return false;
      }
      break;
  }
}

// Get records
$sql = "SELECT w.id, w.customers_id, w.created, w.modified, w.name, w.comment, w.default_status, w.public_status,
                   COUNT(p2w.un_wishlists_id) AS items_count, c.customers_email_address, c.customers_firstname, c.customers_lastname
            FROM " . UN_TABLE_WISHLISTS . " w
            LEFT JOIN " . UN_TABLE_PRODUCTS_TO_WISHLISTS . " p2w ON w.id = p2w.un_wishlists_id
            LEFT JOIN " . TABLE_PRODUCTS . " p ON p2w.products_id = p.products_id
            LEFT JOIN " . TABLE_CUSTOMERS . " c ON w.customers_id = c.customers_id
            GROUP BY w.id";
$records = $db->Execute($sql);

if (!$records) {
  $messageStack->add('header', 'Error getting wishlists.');
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
      <h1><?php echo HEADING_TITLE; ?></h1>

      <table class="table table-striped">
        <thead>
          <tr class="dataTableHeadingRow">
            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER; ?></th>
            <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_WISHLIST; ?></th>
            <th class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_COUNT; ?></th>
            <th class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
          </tr>
        </thead>
        <tbody>
            <?php if ($records->RecordCount() > 0) { ?>

            <?php foreach ($records as $record) { ?>
              <tr>
                <td class="dataTableContent"><?php echo un_get_fullname($record['customers_firstname'], $record['customers_lastname'], $record['customers_email_address']); ?></td>
                <td class="dataTableContent"><a href="<?php echo zen_href_link(UN_FILENAME_WISHLIST, 'wid=' . (int)$record['id']); ?>"><?php echo $record['name']; ?></a></td>
                <td class="dataTableContent text-right"><?php echo $record['items_count']; ?></td>
                <td class="dataTableContent text-right">
                  <a href="<?php echo zen_href_link(UN_FILENAME_WISHLISTS, 'wid=' . (int)$record['id'] . '&action=delete'); ?>" onclick="javascript:return confirm('Are you sure you want to Delete this record?')"><?php echo zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE); ?></a>
                </td>
              </tr>
            <?php } ?>

          <?php } else { ?>
            <tr>
              <td class="dataTableContent text-center" colspan="4"><?php echo TEXT_NO_RECORDS; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>