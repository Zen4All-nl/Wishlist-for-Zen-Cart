# WishList v0.8 SQL Load for MySQL databases
-- CREATE Wish list CONFIGURATION GROUP --
SELECT @cid:=configuration_group_id
FROM configuration_group
WHERE configuration_group_title= 'Wish list';
DELETE FROM configuration WHERE configuration_group_id = @cid AND configuration_group_id != 0;
DELETE FROM configuration_group WHERE configuration_group_id = @cid AND configuration_group_id != 0;
INSERT INTO configuration_group VALUES (NULL, 'Wish list', 'Wish list', '1', '1');
SET @cid=last_insert_id();
UPDATE configuration_group SET sort_order = @cid WHERE configuration_group_id = @cid;
INSERT INTO configuration VALUES (NULL, 'Wishlist Module Switch', 'UN_DB_MODULE_WISHLISTS_ENABLED', 'true', 'Set this option true or false to enable or disable the wishlist', @cid, 710, now(), now(), NULL, "zen_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'Wishlist sidebox header link', 'UN_DB_SIDEBOX_LINK_HEADER', 'true', 'Set this option true or false to make the sidebox header a link to the wishlist page.', @cid, 711, now(), now(), NULL, "zen_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'Wishlist allow multiple lists', 'UN_DB_ALLOW_MULTIPLE_WISHLISTS', 'true', 'Set this option true or false to allow for more than 1 wishlist', @cid, 712, now(), now(), NULL, "zen_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'Wishlist display category filter', 'UN_DB_DISPLAY_CATEGORY_FILTER', 'false', 'Set this option true or false to enable a category filter', @cid, 713, now(), now(), NULL, "zen_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'Wishlist default name', 'DEFAULT_WISHLIST_NAME', 'Default', 'Enter the name you want to be assigned to the initial wishlist.', @cid, 714, now(), now(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'Wishlist show list after product addition', 'DISPLAY_WISHLIST', 'false', 'Set this option true or false to show the wishlist after a product was added to the wishlist', @cid, 715, now(), now(), NULL, "zen_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'Wishlist display max items in extended view', 'UN_MAX_DISPLAY_EXTENDED', '10', 'Enter the maximum amount of products you want to show in extended view.<br />default = 10', @cid, 716, now(), now(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'Wishlist display max items in compact view', 'UN_MAX_DISPLAY_COMPACT', '20', 'Enter the maximum amount of products you want to show in extended view.<br />default = 20', @cid, 717, now(), now(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'Wishlist default view Switch', 'UN_DEFAULT_LIST_VIEW', 'extended', 'Set the default view of the list to compact or extended view', @cid, 718, now(), now(), NULL, "zen_cfg_select_option(array('compact', 'extended'),");
INSERT INTO configuration VALUES (NULL, 'Wishlist allow multiple products to cart', 'UN_DB_ALLOW_MULTIPLE_PRODUCTS_CART_COMPACT', 'false', 'Set this option true or false to allow multiple products to be moved in the cart via checkboxes in compact view', @cid, 719, now(), now(), NULL, "zen_cfg_select_option(array('true', 'false'),");

# Register the configuration page for Admin Access Control
INSERT IGNORE INTO admin_pages (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES ('configZCAWishListModule','BOX_CONFIGURATION_ZCA_WISHLIST','FILENAME_CONFIGURATION',CONCAT('gID=',@cid),'configuration','Y',@cid);


# --------------------------------------------------------
#
# Table structure for table un_wishlists
#
# avoid data loss when reinstall (this will give a table exists error)
# if you want the table to be removed, then reinstalled; uncomment line below
#DROP TABLE IF EXISTS `un_wishlists`;
CREATE TABLE IF NOT EXISTS `un_wishlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `default_status` int(1) NOT NULL,
  `public_status` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

# --------------------------------------------------------
#
#April 2011 v0.8 added column
#added column for attributes, if you need to just add the column to an existing table use:
#alter table un_products_to_wishlists add column attributes VARCHAR(255);
#in the sql patch tool in zen cart
#
# Table structure for table un_products_to_wishlists
#

# avoid data loss when reinstall (this will give a table exists error)
# if you want the table to be removed, then reinstalled; uncomment line below
#DROP TABLE IF EXISTS `un_products_to_wishlists`;
CREATE TABLE IF NOT EXISTS `un_products_to_wishlists` (
  `products_id` int(11) NOT NULL,
  `un_wishlists_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `quantity` int(2) NOT NULL,
  `priority` int(1) NOT NULL,
  `comment` varchar(255) CHARACTER SET utf8 NOT NULL,
  `attributes` varchar(255) CHARACTER SET utf8 NOT NULL,
	 PRIMARY KEY  (`products_id`,`un_wishlists_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;