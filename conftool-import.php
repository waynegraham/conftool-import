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

function clean_imported_data()
{
  //clean_posts('exhibitor');
  //clean_posts('poi');
  clean_posts('session');
  clean_posts('speaker');
  //clean_posts('sponsor');
  //clean_posts('ticket');
}

function clean_posts($post_type)
{
  echo "<p>Cleaning up old {$post_type}</p>\n";
  global $wpdb;
  $posts_table = $wpdb->posts;

  $query = "DELETE FROM {$posts_table} WHERE post_type = '{$post_type}';";
  $wpdb->query($query);
}

// function handle_csv($file)
// {
//   $row = 1;
//   if(($handle = fopen($file, 'r'))!== FALSE ) {
//     while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//
//       $num = count($data);
//       echo "<p> $num fields in line $row: <br /></p>\n";
//
//       $row++;
//       // for ($c=0; $c < $num; $c++) {
//         $name =  $row[2] . " " . $row[1];
//         echo $name;
//       // }
//     }
//     fclose($handle);
//     echo 'Cleaning up';
//     unlink($file);
//   }
// }

function slugify($string, $replace = array(), $delimiter = '-') {
  // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
  if (!extension_loaded('iconv')) {
    throw new Exception('iconv module not loaded');
  }
  // Save the old locale and set the new locale to UTF-8
  $oldLocale = setlocale(LC_ALL, '0');
  setlocale(LC_ALL, 'en_US.UTF-8');
  $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
  if (!empty($replace)) {
    $clean = str_replace((array) $replace, ' ', $clean);
  }
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
  $clean = strtolower($clean);
  $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
  $clean = trim($clean, $delimiter);
  // Revert back to the old locale
  setlocale(LC_ALL, $oldLocale);
  return $clean;
}

function generate_speakers($worksheet)
{
  $counter = 0;

  foreach($worksheet as $row) {
    if ($counter > 0 ) { // skip header row
      $name = $row[2] . ' ' . $row[1];

      $get_page = get_page_by_title($name);

      if($get_page == NULL) {
        //@see http://stackoverflow.com/questions/34434440/how-to-create-wordpress-posts-from-external-json-file
        $speaker_post = array(
          'post_title' => $name,
          'post_content' => 'lorem ipsum',
          'post_status' => 'publish',
          'post_type' => 'speaker',
          'post_author' => 1
        );

        echo "<p>Added {$name} as a speaker.</p>";
        $post_id = wp_insert_post($speaker_post);

        // post meta if available
        //add_post_meta( $post_id, 'meta_key', 'meta_value');

      }
    }
    $counter++;
  }

}

function handle_excel($file)
{
  echo "<p>Importing {$file}</p>";
  require 'vendor/autoload.php';
  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
  $spreadsheet = $reader->load($file);
  $spreadsheet->setActiveSheetIndex(0); // get the first sheet
  $worksheet = $spreadsheet->getActiveSheet()->toArray();

  // Create Speakers
  $unique_speakers = generate_speakers($worksheet);




  // var_dump($worksheet);

  //unlink $file; // clean up
}

function conftool_admin_page()
{

  if($_FILES['my_image_upload']['tmp_name']){
    $file_path = $_FILES['my_image_upload']['tmp_name'];
    clean_imported_data();
    handle_excel($file_path);
  }

  echo '<form id="featured_upload" method="post" action="#" enctype="multipart/form-data">';
  echo '<input type="file" name="my_image_upload" id="my_image_upload"  multiple="false" />';
  echo wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' );
  echo '<input id="submit_my_image_upload" name="submit_my_image_upload" type="submit" value="Upload" />';
  echo '</form>';

}
