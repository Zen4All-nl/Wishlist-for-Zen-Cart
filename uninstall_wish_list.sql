-- Wish list delete statement admin options
SET @t4=0;
SELECT (@t4:=configuration_group_id) as t4 
FROM configuration_group
WHERE configuration_group_title = 'Wish list';
DELETE FROM configuration WHERE configuration_group_id = @t4;
DELETE FROM configuration_group WHERE configuration_group_id = @t4;

--if you want to delete the entire module you can delete the tables that contain
--the information about the wishlists and links to your customers.
--this should never be needed, but if you want to, remove the # from the 
--lines below to remove the tables.
--drop tables
#DROP TABLE IF EXISTS `un_wishlists`;
#DROP TABLE IF EXISTS `un_products_to_wishlists`;