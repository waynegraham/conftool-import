<?php
/**
* Plugin Name:     ConfTool Wordpress Import
* Plugin URI:      https://github.com/waynegraham/conftool-wordpress-import
* Description:     Import Conftool for ShowTheme custom posts
* Author:          Council on Library and Information Resources
* Author URI:      https://www.clir.org/
* Text Domain:     conftool-import
* Domain Path:     /languages
* Version:         0.1.0
*
* @package         Conftool_Import
*/

add_action('admin_menu', 'conftool_admin_menu');

function conftool_admin_menu()
{
  add_menu_page( 'ConfTool Import Tool', 'ConfTool Import', 'manage_options', 'conftool/conftool-admin-page.php', 'conftool_admin_page', 'dashicons-hammer', 6  );

}

function clean_posts($post_type)
{
  echo "Cleaning up stale {$post_type}";
}

function handle_csv($file)
{
  $row = 1;
  if(($handle = fopen($file, 'r'))!== FALSE ) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

      // delete all Speaker post types
      clean_posts('Speaker');
      // delete all Sessions post types
      clean_posts('Sessions');
      // delete all Location post types
      clean_posts('Location');



      $num = count($data);
      echo "<p> $num fields in line $row: <br /></p>\n";




      $row++;
      // for ($c=0; $c < $num; $c++) {
        $name =  $row[2] . " " . $row[1];
        echo $name;
      // }
    }
    fclose($handle);
    echo 'Cleaning up';
    unlink($file);
  }
}

function handle_excel($file)
{
  require 'vendor/autoload.php';
  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
  $spreadsheet = $reader->load($file);
  $spreadsheet->setActiveSheetIndex(0); // get the first sheet
  $worksheet = $spreadsheet->getActiveSheet();
  foreach($worksheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
    var_dump($row);
    // foreach($cellIterator as $cell) {
    //   echo $cell->getValue() . "\n";
    // }
  }




  var_dump($spreadsheet);

  // delete all Speaker post types
  clean_posts('Speaker');
  // delete all Sessions post types
  clean_posts('Sessions');
  // delete all Location post types
  clean_posts('Location');

  // unlink $file;
}

function conftool_admin_page()
{

  if($_FILES['my_image_upload']['tmp_name']){
    $file_path = $_FILES['my_image_upload']['tmp_name'];
    handle_excel($file_path);
    // handle_csv($file_path);
  }

  echo '<form id="featured_upload" method="post" action="#" enctype="multipart/form-data">';
  echo '<input type="file" name="my_image_upload" id="my_image_upload"  multiple="false" />';
  echo wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' );
  echo '<input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" value="Upload" />';
  echo '</form>';

}
