<?php
namespace Drupal\custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_price\Price;
use \Drupal\file\Entity\File;
use \Drupal\node\Entity\Node;
use \Drupal\Core\File\FileSystemInterface;
use Drupal\physical\Weight;
use Drupal\physical\WeightUnit;
use Drupal\user\Entity\User;
use Drupal\commerce_order\Adjustment;
use Drupal\profile\Entity\Profile;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_payment\Entity\Payment;
use Drupal\commerce_shipping\Entity\Shipment;
use Drupal\commerce_shipping\ShipmentItem;
/**
 * Provides route responses for the Example module.
 */
class CustomController extends ControllerBase {


  /**
   * Returns a Array from csv.
   *
   * @return array
   *   A simple renderable array.
   */
  public function read_csv($file) {
    $mdarray = array();
    $file    = fopen($file, "r");
    while ($line = fgetcsv($file, 100000, ',')) {
      array_push($mdarray, $line);
      }
    fclose($file);
    return $mdarray;
  }

  public function productimport() {
    // $db = \Drupal::database();
    
    /*for product type mackenzie_marr_wear*/
    //$product = 'csv/product_data_export.csv';
    
    /*for product type optional_upgrade*/
    // $product = 'csv/product_optional_export.csv';

    /*for product type product*/
    // $product = 'csv/product_product_export.csv';
    
    // $csv = $this->read_csv($product);

    // $store_id = 1;
    // $store = \Drupal\commerce_store\Entity\Store::load($store_id);
    /*for product type mackenzie_marr_wear*/
    /* foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }
      $body = $value[0];
      $title = $value[1];
      $sku = $value[2];
      $price = $value[3];
      $clothing_image = $value[4];
      $colour = $value[5];
      $pre_order_price = $value[6];
      $enable_pre_order = $value[7];
      $price_in_usd = $value[8];
      $stock = $value[9];
      $description = $value[10];
      $sale_events = $value[11];
      $size = $value[12];
      $status = $value[13];
      $old_pid = $value[14]; 

      $img = basename($clothing_image);
      //$data = file_get_contents($clothing_image);
      $data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$img);
      $fileRepository = \Drupal::service('file.repository');
      $file = $fileRepository->writeData($data, 'public://productimage/'.$img, FileSystemInterface::EXISTS_REPLACE);

      $price = preg_replace('/[^0-9.]+/', '', $price);
      $pre_order_price = preg_replace('/[^0-9.]+/', '', $pre_order_price);
      $price_in_usd = preg_replace('/[^0-9.]+/', '', $price_in_usd);


      if(!empty($colour)){
        $query = $db->query("SELECT new_tid FROM custom_taxonomy_relation WHERE old_tid='$colour'");
        $result = $query->fetchAll();
        if(!empty($result)){
          $colour = $result[0]->new_tid;
        }
      }

      if(!empty($sale_events)){
        $query2 = $db->query("SELECT new_tid FROM custom_taxonomy_relation WHERE old_tid='$sale_events'");
        $result2 = $query2->fetchAll();
        if(!empty($result2)){
          $sale_events = $result2[0]->new_tid;
        }
      }

      if(!empty($size)){
        $query3 = $db->query("SELECT new_tid FROM custom_taxonomy_relation WHERE old_tid='$size'");
        $result3 = $query3->fetchAll();
        if(!empty($result3)){
          $size = $result3[0]->new_tid;
        }
      }


      $variation1 = ProductVariation::create([
        'type' => 'mackenzie_marr_wear',
        'sku' => $sku,
        'title' => $title,
        'field_title_field' => $title,
        'field_description' => $description,
        'field_clothing_image' => $file->id(),
        'field_colours' => $colour,
        'field_sale_events' => $sale_events,
        'field_sizes' => $size,
        'field_commerce_stock' => $stock,
        'field_commerce_preorder_status' => $enable_pre_order,
        'field_commerce_preorder_price' => new Price((float)$pre_order_price, 'CAD'),
        'field_commerce_price_usd' => new Price((float)$price_in_usd, 'USD'),
        'price' => new Price((float)$price, 'CAD'),
        'status' => $status,
      ]);
      $variation1->save();

      $product = Product::create([
        'type' => 'mackenzie_marr_wear',
        'title' => $title,
        'body' => $description,
        'status' => $status,
        'stores' => [$store],
        'variations' => [$variation1],
      ]);
      $product->save();
      $new_vid = $variation1->id();
      $new_pid = $product->id();
      
      $query4 = $db->query("SELECT * FROM custom_product_relation WHERE old_pid='$old_pid'");
      $result4 = $query4->fetchAll();
      if(!empty($result4)){
        $query = $db->update('custom_product_relation')->fields([
          'new_pid' => $new_pid,
          'new_vid' => $new_vid,
        ])->condition('old_pid', $old_pid, '=')->execute();
      }else{
        $query = $db->insert('custom_product_relation')->fields([
          'old_pid' => $old_pid,
          'new_pid' => $new_pid,
          'new_vid' => $new_vid,
        ])->execute();
      }
    }*/

    /*for product type optional_upgrade*/
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $old_pid = $value[0]; 
      $sku = $value[1];
      $title = $value[2];
      $price = $value[3];
      $status = $value[4];
      $sale_events = $value[5];
      $summary = $value[6];
      $price_in_usd = $value[7];
      $enable_pre_order = $value[8];
      $pre_order_price = $value[9];

      $price = preg_replace('/[^0-9.]+/', '', $price);
      $pre_order_price = preg_replace('/[^0-9.]+/', '', $pre_order_price);
      $price_in_usd = preg_replace('/[^0-9.]+/', '', $price_in_usd);

      $query2 = $db->query("SELECT new_tid FROM custom_taxonomy_relation WHERE old_tid='$sale_events'");
      $result2 = $query2->fetchAll();
      if(!empty($result2)){
        $sale_events = $result2[0]->new_tid;
      }

      $variation1 = ProductVariation::create([
        'type' => 'optional_upgrade',
        'sku' => $sku,
        'title' => $title,
        'field_title_field' => $title,
        'field_summary' => $summary,
        'field_sale_events' => $sale_events,
        'field_commerce_preorder_status' => $enable_pre_order,
        'field_commerce_preorder_price' => new Price((float)$pre_order_price, 'CAD'),
        'field_commerce_price_usd' => new Price((float)$price_in_usd, 'USD'),
        'price' => new Price((float)$price, 'CAD'),
        'status' => $status,
      ]);
      $variation1->save();

      $product = Product::create([
        'type' => 'optional_upgrade',
        'title' => $title,
        'body' => $summary,
        'status' => $status,
        'stores' => [$store],
        'variations' => [$variation1],
      ]);
      $product->save();
      $new_vid = $variation1->id();
      $new_pid = $product->id();

      $query4 = $db->query("SELECT * FROM custom_product_relation WHERE old_pid='$old_pid'");
      $result4 = $query4->fetchAll();
      if(!empty($result4)){
        $query = $db->update('custom_product_relation')->fields([
          'new_pid' => $new_pid,
          'new_vid' => $new_vid,
        ])->condition('old_pid', $old_pid, '=')->execute();
      }else{
        $query = $db->insert('custom_product_relation')->fields([
          'old_pid' => $old_pid,
          'new_pid' => $new_pid,
          'new_vid' => $new_vid,
        ])->execute();
      }
    }*/

    /*for product type product*/
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }
      $old_pid = $value[0]; 
      $title = $value[1];
      $sku = $value[2];
      $price = $value[3];
      $price_in_usd = $value[4];
      $price_in_euro = $value[5];
      $sale_price = $value[6];
      $suggested_retail_price = $value[7];
      $status = $value[8];
      $sale_events = $value[9];
      $pickup_option = $value[10];
      $disable_stock_for_product = $value[11];
      $spin = $value[12];
      $price_for_schema = $value[13];
      $image = $value[14];
      $info = $value[15];
      $image2 = $value[16];
      $stock = $value[17];
      $availability = $value[18];
      $enable_pre_order = $value[19];
      $pre_order_price = $value[20];
      $pre_order_availability = $value[21];
      $shipping_dimensions = $value[22];
      $Shipping_weight = $value[23];
      $sub_head = $value[24];
      $short_description = $value[25];
      $main_body = $value[26];
      $on_sale = $value[27];
      $discounted_price = $value[28];

      $price = preg_replace('/[^0-9.]+/', '', $price);
      $price_in_usd = preg_replace('/[^0-9.]+/', '', $price_in_usd);
      $price_in_euro = preg_replace('/[^0-9,.]+/', '', $price_in_euro);
      $sale_price = preg_replace('/[^0-9.]+/', '', $sale_price);
      $suggested_retail_price = preg_replace('/[^0-9.]+/', '', $suggested_retail_price);
      $price_for_schema = preg_replace('/[^0-9.]+/', '', $price_for_schema);
      $pre_order_price = preg_replace('/[^0-9.]+/', '', $pre_order_price);
      $discounted_price = preg_replace('/[^0-9.]+/', '', $discounted_price);

      $Shipping_weight = preg_replace('/[^0-9.]+/', '', $Shipping_weight);

      if(!empty($Shipping_weight)){

      }else{
        $Shipping_weight =0;
      }

      
      $query2 = $db->query("SELECT new_tid FROM custom_taxonomy_relation WHERE old_tid='$sale_events'");
      $result2 = $query2->fetchAll();
      if(!empty($result2)){
        $sale_events = $result2[0]->new_tid;
      }
      
      $query_pickup = $db->query("SELECT new_vid FROM custom_product_relation WHERE old_pid='$pickup_option'");
      $result_pickup = $query_pickup->fetchAll();
      if(!empty($result_pickup)){
        $pickup_option = $result_pickup[0]->new_vid;
      }

      $fileRepository = \Drupal::service('file.repository');
      $file1 = $image_fid = array();
      $image_arr = explode(",",$image);
      foreach ($image_arr as  $image) {
        $img1 = basename($image);
        //$data = file_get_contents($clothing_image);
        $data1 = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/media/product/images/'.$img1);
        $file1[] = $fileRepository->writeData($data1, 'public://productimage/'.$img1, FileSystemInterface::EXISTS_REPLACE);
      }
      foreach ($file1 as  $fvalue) {
        $image_fid[] = $fvalue->id();
      }
      
      if(empty($image_fid)){
        $image_fid = '';
      }

      $img2 = basename($image2);
      if(!empty($img2)){
        //$data = file_get_contents($clothing_image);
        $data2 = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/media/product/images/'.$img2);
        $file2 = $fileRepository->writeData($data2, 'public://productimage/'.$img2, FileSystemInterface::EXISTS_REPLACE);
        $file2_id = $file2->id();
      }else{
        $file2_id ='';
      }

      if(!empty($price_in_euro)){
        $price_in_euro = str_replace(",",".",$price_in_euro);
      }
      // print_r(date($pre_order_availability));echo "<br>";
      $variation1 = ProductVariation::create([
        'type' => 'product',
        'sku' => $sku,
        'title' => $title,
        'field_title_field' => $title,
        'field_availability' => $availability,
        'field_commerce_stock_override' => $disable_stock_for_product,
        'field_discounted_price' => new Price((float)$discounted_price, 'CAD'),
        'field_commerce_preorder_status' => $enable_pre_order,
        'field_uc_product_image' => $image_fid,
        'field_image3' => $file2_id,
        'price' => new Price((float)$price, 'CAD'),
        'field_info' => $info,
        'field_main_body' => $main_body,
        'field_commerce_saleprice_o' => $on_sale,
        'field_pickup_option' => $pickup_option,
        'field_pre_order_availability' => $pre_order_availability,
        'field_commerce_preorder_price' => new Price((float)$pre_order_price, 'CAD'),
        'field_price_for_schema' => new Price((float)$price_for_schema, 'CAD'),
        'field_price_in_euro' => new Price((float)$price_in_euro, 'EUR'),
        'field_commerce_price_usd' => new Price((float)$price_in_usd, 'USD'),
        'field_sale_events' => $sale_events,
        'field_commerce_saleprice' => new Price((float)$sale_price, 'CAD'),
        'field_shipping_weight' => new Weight($Shipping_weight, WeightUnit::POUND),
        'field_short_description' => $short_description,
        'field_spin' => $spin,
        'field_commerce_stock' => $stock,
        'field_sub_head' => $sub_head,
        'field_msrp' => new Price((float)$suggested_retail_price, 'CAD'),
        'status' => $status,
      ]);
      $variation1->save();

      $product = Product::create([
        'type' => 'product',
        'title' => $title,
        'body' => $main_body,
        'status' => $status,
        'stores' => [$store],
        'variations' => [$variation1],
      ]);
      $product->save();
      $new_vid = $variation1->id();
      $new_pid = $product->id();

      $query4 = $db->query("SELECT * FROM custom_product_relation WHERE old_pid='$old_pid'");
      $result4 = $query4->fetchAll();
      if(!empty($result4)){
        $query = $db->update('custom_product_relation')->fields([
          'new_pid' => $new_pid,
          'new_vid' => $new_vid,
        ])->condition('old_pid', $old_pid, '=')->execute();
      }else{
        $query = $db->insert('custom_product_relation')->fields([
          'old_pid' => $old_pid,
          'new_pid' => $new_pid,
          'new_vid' => $new_vid,
        ])->execute();
      }
    }*/

    $element = array(
      '#markup' => "output",
    );
    return $element;
  }

  public function usercustomimport(){
    /*$order = \Drupal::entityTypeManager()->getStorage('commerce_order')->load(4580);
    $mail =  $order->get('mail')->getValue();
    print_r($mail[0]['value']);
    exit();*/
    //$db = \Drupal::database();
    /*for user */
    //$user_csv = 'csv/user_export_data.csv';

    //$csv = $this->read_csv($user_csv);

    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }
      $username = $value[0];
      $user_mail = $value[1];
      $user_status = $value[2];
      $user_image = $value[3];
      $user_role = $value[4];
      $user_old_id = $value[5];
      $user_reviews = $value[6];
      $user_first_name = $value[7];
      $user_last_name = $value[8];
      $user_telephone = $value[9];
      $user_street_address = $value[10];
      $user_country = $value[11];
      $user_city = $value[12];
      $user_province = $value[13];
      $user_postal = $value[14];

      // User role setting
      if(!empty($user_role)){
        $query = $db->query("SELECT new_user_role FROM custom_user_role_relation WHERE old_user_role_id='$user_role'");
        $result = $query->fetchAll();
        if(!empty($result)){
          if($user_role != 2){
            $user_role = $result[0]->new_user_role;
          }
        }
      }else{
        //$user_role = 'authenticated';
      }

      $user = User::create();
      $user->setUsername($username);
      $user->setPassword($user_mail);
      $user->setEmail($user_mail);
      $user->addRole($user_role);
      $user->set('init', $username);
      $user->set("field_first_name", $user_first_name);
      $user->set("field_last_name", $user_last_name);
      $user->set("field_phone_number", $user_telephone);
      $user->field_address->address_line1 = $user_street_address;
      $user->field_address->country_code = $user_country;
      $user->field_address->locality = $user_city;
      $user->field_address->administrative_area = $user_province;
      $user->field_address->postal_code = $user_postal;
      $user->enforceIsNew();
      if($user_status == 1){
        $user->activate();
      }
      $user->save();
      
      $new_uid = $user->id();
      
      $query4 = $db->query("SELECT * FROM custom_user_relation WHERE old_uid='$user_old_id'");
      $result4 = $query4->fetchAll();
      if(!empty($result4)){
        $query = $db->update('custom_user_relation')->fields([
          'new_uid' => $new_uid,
        ])->condition('old_uid', $user_old_id, '=')->execute();
      }else{
        $query = $db->insert('custom_user_relation')->fields([
          'old_uid' => $user_old_id,
          'new_uid' => $new_uid,
        ])->execute();
      }
      // exit();
    }*/
    $element = array(
      '#markup' => "output",
    );
    return $element;
  }

  public function usercustomprofileimport() {
    $db = \Drupal::database();
    
    /*for Customer profile */
    //$profile_csv = 'csv/custom_customer_profile_feeds.csv';

    //$csv = $this->read_csv($profile_csv);

    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }
      $old_profile_id = $value[0];
      $profile_type = $value[1];
      $profile_status = $value[2];
      $profile_first_name = $value[3];
      $profile_last_name = $value[4];
      $old_uid = $value[5];
      $profile_country = $value[6];
      $profile_city = $value[7];
      $profile_state = $value[8];
      $profile_company = $value[9];
      $profile_postal_code = $value[10];
      $profile_address = $value[11];
      $profile_telephone = $value[12];
      $profile_email = $value[13];
      
      $query1 = $db->query("SELECT * FROM custom_user_relation WHERE old_uid='$old_uid'");
      $result1 = $query1->fetchAll();
      if(!empty($result1)){
        $new_uid = $result1[0]->new_uid;
      }else{
        $new_uid = 0;
      }

      $profile = Profile::create([
        'type' => 'customer',
        'uid' => $new_uid,
        'field_email' => $profile_email,
        'field_telephone' => $profile_telephone,
        'address' => [
          "langcode" => "",
          "country_code" => $profile_country,
          "administrative_area" => $profile_state,
          "locality" => $profile_city,
          "dependent_locality" => null,
          "postal_code" => $profile_postal_code,
          "sorting_code" => null,
          "address_line1" => $profile_address,
          "address_line2" => "",
          "organization" => $profile_company,
          "given_name" => $profile_first_name,
          "family_name" => $profile_last_name,
        ],
        'status' => $profile_status,
      ]);
      $profile->save();

      $query = $db->insert('custom_customer_profile_relation')->fields([
        'old_profile_id' => $old_profile_id,
        'old_uid' => $old_uid,
        'new_profile_id' => $profile->id(),
        'new_uid' => $new_uid,
      ])->execute();
      //exit();
    }*/


    
    $element = array(
      '#markup' => "output",
    );
    return $element;
  }

  public function contentblogimport(){
    $db = \Drupal::database();
    // For review to product relation d10
    // $entity_manager = \Drupal::entityTypeManager();
    // $review_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'mmg_review']);
    // foreach ($review_nodes as $key => $review_node) {
    //   $intro_text = $review_node->get('field_review_intro')->getValue()[0]['value'];
    //   if(!empty($intro_text)) {
    //     $review_node->set('field_review_intro',str_replace("writes:","reviews their Opeongo",$intro_text));
    //     $review_node->save();
    //   }
    // }
    //   // if(isset($review_node->get('field_sku')->getValue())){
    //     $product_sku = $review_node->get('field_sku')->getValue()[0]['value'];
    //     if(!empty($product_sku)){
    //       $query = \Drupal::entityQuery('commerce_product_variation');
    //       $query->accessCheck(FALSE)->condition('sku', $product_sku);
    //       $variation_id = $query->execute();
    //       if(!empty($variation_id)) {
    //         $product_variation = $entity_manager->getStorage('commerce_product_variation')->load((int)reset($variation_id));
    //         if(is_object($product_variation)){
    //           dpm($product_variation);
    //           $product_id = $product_variation->get("product_id")->getValue()[0]["target_id"];
    //           $review_node->field_reviewed_products->target_id = $product_id;
    //           print_r($review_node->id());
    //           echo "<br/>";
              // $review_node->save();
    //         }else {
    //           print_r($review_node->id());
    //           echo "<br/>";
    //         }
    //       }
    //     }
    //   // }
    // }
    // exit();
    /*for Blog content */
    // $blog_csv = 'csv/content_video_page_export.csv';
    // $csv = $this->read_csv($blog_csv);
    
    /*for video page content */
    // $video_page_csv = 'csv/content_video_page_export.csv';
    // $csv = $this->read_csv($video_page_csv);

    /*for story content */
    // $story_csv = 'csv/content_story_export.csv';
    // $csv = $this->read_csv($story_csv);

    /*for Product component content */
    // $product_component_csv = 'csv/content_product_component_export.csv';
    // $csv = $this->read_csv($product_component_csv);

    /*for Basic page content */
    // $basic_page_csv = 'csv/content_basic_export.csv';
    // $csv = $this->read_csv($basic_page_csv);

    /*for Basic page content */
    // $news_article_csv = 'csv/content_news_article_export.csv';
    // $csv = $this->read_csv($news_article_csv);

    /*for Image only content */
    // $image_only_csv = 'csv/content_image_export.csv';
    // $csv = $this->read_csv($image_only_csv);

    /*for Guitar specs content */
    // $guitar_specs_csv = 'csv/content_guitar_specs_export.csv';
    // $csv = $this->read_csv($guitar_specs_csv);

    /*for MMG review content */
    // $mmg_review_csv = 'csv/content_mmgreview_export.csv';
    // $csv = $this->read_csv($mmg_review_csv);

    /*for Product display content */
    // $product_display_csv = 'csv/content_product_display_export.csv';
    // $csv = $this->read_csv($product_display_csv);

    /*for Blog content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $title =  $value[0];
      $sub_title = $value[1];
      $picture = $value[2];
      $body = $value[3];
      $video = $value[4];
      $bg2_image = $value[5];
      $old_nid = $value[6];
      $status = $value[7];

      $img = basename($picture);
      if(!empty($img)){
        $picture_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$img);
        $fileRepository = \Drupal::service('file.repository');
        $picture_file = $fileRepository->writeData($picture_data, 'public://blog/'.$img, FileSystemInterface::EXISTS_REPLACE);
        $img_id = $picture_file->id();
      }else{
        $img_id = '';
      }

      $bg_img = basename($bg2_image);
      if(!empty($bg_img)){
        $bg2_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$bg_img);
        $fileRepository = \Drupal::service('file.repository');
        $bg2_file = $fileRepository->writeData($bg2_data, 'public://blog/'.$bg_img, FileSystemInterface::EXISTS_REPLACE);
        $bg2_id = $bg2_file->id();
      }else{
        $bg2_id = '';
      }

      $video_name = basename($video);
      if(!empty($video_name)){
        $video_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$video_name);
        $fileRepository = \Drupal::service('file.repository');
        $video_file = $fileRepository->writeData($video_data, 'public://blog/'.$video_name, FileSystemInterface::EXISTS_REPLACE);
        $video_id = $video_file->id();
      }else{
        $video_id = '';
      }
      
      $node = Node::create([
        'type'        => 'blog',
        'title'       => $title,
        'field_sub_title' => $sub_title,
        'body' => $body,
        'field_picture' => [
          'target_id' => $img_id,
        ],
        'field_bg2_image' => [
          'target_id' => $bg2_id,
        ],
        'field_video' => [
          'target_id' => $video_id,
        ],
        'status' => $status,
      ]);
      $node->save();

      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/

    /*for video page content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $title = $value[0];
      $body = $value[1];
      $image = $value[2];
      $video = $value[3];
      $iframe = $value[4];
      $old_nid = $value[5];
      $status = $value[6];

      $file1 = $image_fid = array();
      $image_arr = explode(",",$image);
      $fileRepository = \Drupal::service('file.repository');
      foreach ($image_arr as  $image) {
        $img1 = basename($image);
        //$data = file_get_contents($clothing_image);
        $data1 = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$img1);
        $file1[] = $fileRepository->writeData($data1, 'public://blog/'.$img1, FileSystemInterface::EXISTS_REPLACE);
      }
      foreach ($file1 as  $fvalue) {
        $image_fid[] = $fvalue->id();
      }
      
      if(empty($image_fid)){
        $image_fid = '';
      }
      
      $node = Node::create([
        'type'  => 'video_page',
        'title' => $title,
        'body' => $body,
        'field_video_image' => $image_fid,
        'status' => $status,
      ]);
      $node->save();

      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/

    /*for story content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $title = $value[0];
      $body = $value[1];
      $image = $value[2];
      $teaser = $value[3];
      $old_nid = $value[4];
      $status = $value[5];

      $img = basename($image);
      if(!empty($img)){
        $picture_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/images/'.$img);
        $fileRepository = \Drupal::service('file.repository');
        $picture_file = $fileRepository->writeData($picture_data, 'public://blog/'.$img, FileSystemInterface::EXISTS_REPLACE);
        $img_id = $picture_file->id();
      }else{
        $img_id = '';
      }
      
      $node = Node::create([
        'type'  => 'story',
        'title' => $title,
        'body' => $body,
        'field_picture' => $img_id,
        'field_teaser' => $teaser,
        'status' => $status,
      ]);
      $node->save();

      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/

    /*for Product component content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $title = $value[0];
      $body = $value[1];
      $image = $value[2];
      $date_imp = $value[3];
      $old_nid = $value[4];
      $status = $value[5];

      $img = basename($image);
      if(!empty($img)){
        $picture_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$img);
        $fileRepository = \Drupal::service('file.repository');
        $picture_file = $fileRepository->writeData($picture_data, 'public://blog/'.$img, FileSystemInterface::EXISTS_REPLACE);
        $img_id = $picture_file->id();
      }else{
        $img_id = '';
      }
      
      $node = Node::create([
        'type'  => 'product_components',
        'title' => $title,
        'body' => $body,
        'field_component_image' => $img_id,
        'field_first_implemented' => $date_imp,
        'status' => $status,
      ]);
      $node->save();

      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/

    /*for Basic page content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $title = $value[0];
      $body = $value[1];
      $image = $value[2];
      $old_nid = $value[3];
      $status = $value[4];

      $img = basename($image);
      if(!empty($img)){
        $picture_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$img);
        $fileRepository = \Drupal::service('file.repository');
        $picture_file = $fileRepository->writeData($picture_data, 'public://blog/'.$img, FileSystemInterface::EXISTS_REPLACE);
        $img_id = $picture_file->id();
      }else{
        $img_id = '';
      }
      
      $node = Node::create([
        'type'  => 'page',
        'title' => $title,
        'body' => $body,
        'field_picture' => $img_id,
        'status' => $status,
      ]);
      $node->save();

      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/

    /*for News article content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $title = $value[0];
      $body = $value[1];
      $sub_title = $value[2];
      $link = $value[3];
      $image = $value[4];
      $old_nid = $value[5];
      $status = $value[6];

      if(!empty($link)){
        $link_arr = explode("=",$link);
        $link_url = $link_arr[0];
        $link_text = $link_arr[1];
      }else{
        $link_url = '';
        $link_text = '';
      }


      $img = basename($image);
      if(!empty($img)){
        $picture_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$img);
        $fileRepository = \Drupal::service('file.repository');
        $picture_file = $fileRepository->writeData($picture_data, 'public://news/'.$img, FileSystemInterface::EXISTS_REPLACE);
        $img_id = $picture_file->id();
      }else{
        $img_id = '';
      }
      
      $node = Node::create([
        'type'  => 'news_articles',
        'title' => $title,
        'field_sub_title' => $sub_title,
        'body' => $body,
        'field_image' => $img_id,
        'field_link' => [
          'uri'=> $link_url, 
          'title' => $link_text
        ],
        'status' => $status,
      ]);
      $node->save();
      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/

    /*for Image only content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $title = $value[0];
      $image = $value[1];
      $old_nid = $value[3];
      $status = $value[4];

      $img = basename($image);
      if(!empty($img)){
        $picture_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/'.$img);
        $fileRepository = \Drupal::service('file.repository');
        $picture_file = $fileRepository->writeData($picture_data, 'public://news/'.$img, FileSystemInterface::EXISTS_REPLACE);
        $img_id = $picture_file->id();
      }else{
        $img_id = '';
      }
      
      $node = Node::create([
        'type'  => 'image_only',
        'title' => $title,
        'field_image' => $img_id,
        'status' => $status,
      ]);
      $node->save();
      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/

    /*for Guitar specs content */
    // foreach ($csv as $key => $value) {
    //   if ($key == 0) {
    //     continue;
    //   }

    //   $title = $value[0];
    //   $modal = $value[1];
    //   $body_style = $value[2];
    //   $width_at_upper_bout = $value[3];
    //   $body_back_and_sides = $value[4];
    //   $body_depth = $value[5];
    //   $body_finish = $value[6];
    //   $body_length = $value[7];
    //   $body_top_wood = $value[8];
    //   $bracing = $value[9];
    //   $case = $value[10];
    //   $e_to_e_string_spacing_at_nut = $value[11];
    //   $e_to_e_string_spacing_at_saddle = $value[12];
    //   $electronics = $value[13];
    //   $fretboard_and_bridge = $value[14];
    //   $fretboard_radius = $value[15];
    //   $frets_to_the_body = $value[16];
    //   $neck = $value[17];
    //   $neck_finish = $value[18];
    //   $neck_profile = $value[19];
    //   $neck_width_at_nut = $value[20];
    //   $nut_saddle = $value[21];
    //   $scale_length = $value[22];
    //   $strings = $value[23];
    //   $tuning_machines = $value[24];
    //   $width_at_lower_bout = $value[25];
    //   $width_at_waist = $value[26];
    //   $old_nid = $value[27];
    //   $status = $value[28];

    
    //   $node = Node::create([
    //     'type'  => 'guitar_specs',
    //     'title' => $title,
    //     'field_backsides' => $body_back_and_sides,
    //     'field_body_depth' => $body_depth,
    //     'field_body_finish' => $body_finish,
    //     'field_body_length' => $body_length,
    //     'field_body_style' => $body_style,
    //     'field_top' => $body_top_wood,
    //     'field_bracing' => $bracing,
    //     'field_case' => $case,
    //     'field_electronics' => $electronics,
    //     'field_etoe' => $e_to_e_string_spacing_at_nut,
    //     'field_etoesaddle' => $e_to_e_string_spacing_at_saddle,
    //     'field_fretboard' => $fretboard_and_bridge,
    //     'field_neck_radius' => $fretboard_radius,
    //     'field_frets_to_body' => $frets_to_the_body,
    //     'field_model' => $modal,
    //     'field_materials_neck' => $neck,
    //     'field_neck_finish' => $neck_finish,
    //     'field_neck_profile' => $neck_profile,
    //     'field_nut_width' => $neck_width_at_nut,
    //     'field_materials_nut_saddle' => $nut_saddle,
    //     'field_scale_length' => $scale_length,
    //     'field_strings' => $strings,
    //     'field_tuners' => $tuning_machines,
    //     'field_width_second_bout' => $width_at_lower_bout,
    //     'field_width_first_bout' => $width_at_upper_bout,
    //     'field_width_at_waist' => $width_at_waist,
    //     'status' => $status,
    //   ]);
    //   // $node->setOwnerId(0);
    //   $node->save();
    //   // exit();
    //   $query = $db->insert('custom_content_relation')->fields([
    //     'old_nid' => $old_nid,
    //     'new_nid' => $node->id(),
    //   ])->execute();
    // }

    /*for MMG review content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }
      $title = $value[0];
      $review_status = $value[1];
      $reviewed_product = $value[2];
      $sku = $value[3];
      $old_order_number = $value[4];
      $first_name = $value[5];
      $city = $value[6];
      $review_intro = $value[7];
      $review_text = $value[8];
      $star_rating = $value[9];
      $status = $value[10];
      $old_nid = $value[11];

      if(!empty($reviewed_product)){
        $query1 = $db->query("SELECT new_nid FROM custom_content_relation WHERE old_nid='$reviewed_product'");
        $result1 = $query1->fetchAll();
        if(!empty($result1)){
          $reviewed_product = $result1[0]->new_nid;
        }else{
          $reviewed_product = '';
        }
      }
      // print_r("Old order: ".$old_order_number);echo "<br/>";
      if(!empty($old_order_number)){
        $old_order_number = preg_replace('/[^0-9.]+/', '', $old_order_number);
        $query2 = $db->query("SELECT new_order_id FROM custom_order_relation WHERE old_order_id='$old_order_number'");
        $result2 = $query2->fetchAll();
        if(!empty($result2)){
          $order_number = $result2[0]->new_order_id;
        }else{
          $order_number = '';
        }
        // print_r("New order: ".$order_number);echo "<br/>";
      }else{
        $order_number = '';
      }

      if($review_status == 'Approved'){
        $review_status = 1;
      }else{
        $review_status = 0;
      }

      $node = Node::create([
        'type'  => 'mmg_review',
        'title' => $title,
        'field_review_status' => $review_status,
        'field_reviewed_product' => $reviewed_product,
        'field_sku' => $sku,
        'field_guitar_order' => $order_number,
        'field_review_firstname' => $first_name,
        'field_review_city' => $city,
        'field_review_intro' => $review_intro,
        'field_mmg_review' => $review_text,
        'field_star_star_rating' => $star_rating,
        'status' => $status,
      ]);
      $node->save();

      $query1 = $db->query("SELECT new_nid FROM custom_content_relation WHERE old_nid='$old_nid'");
      $result1 = $query1->fetchAll();
      if(!empty($result1)){
        $query = $db->update('custom_content_relation')->fields([
          'new_nid' => $node->id(),
        ])->condition('old_nid', $old_nid, '=')->execute();
      }else{
        $query = $db->insert('custom_content_relation')->fields([
          'old_nid' => $old_nid,
          'new_nid' => $node->id(),
        ])->execute();
      }
    }*/
    //exit();
    /*for Product display content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }
      $title = $value[0];
      $guitar_category = $value[1];
      $guitar_specs = $value[2];
      $body = $value[3];
      $old_nid = $value[4];
      $product_display_image = $value[5];
      $videos = $value[6];
      $image_gallery = $value[7];
      $status = $value[8];
      $product_reference = $value[9];

      if(!empty($guitar_category)){
        $query1 = $db->query("SELECT new_tid FROM custom_taxonomy_relation WHERE old_tid='$guitar_category'");
        $result1 = $query1->fetchAll();
        if(!empty($result1)){
          $guitar_category = $result1[0]->new_tid;
        }else{
          $guitar_category = '';
        }
      }else{
        $guitar_category = '';
      }

      if(!empty($guitar_specs)){
        $query1 = $db->query("SELECT new_nid FROM custom_content_relation WHERE old_nid='$guitar_specs'");
        $result1 = $query1->fetchAll();
        if(!empty($result1)){
          $guitar_specs = $result1[0]->new_nid;
        }else{
          $guitar_specs = '';
        }
      }else{
        $guitar_specs = '';
      }

      $file1 = $display_image_fid = array();
      $image_arr = explode(",",$product_display_image);
      $fileRepository = \Drupal::service('file.repository');
      foreach ($image_arr as  $image) {
        $img1 = basename($image);
        //$data = file_get_contents($clothing_image);
        $data1 = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/media/product/images/'.$img1);
        $file1[] = $fileRepository->writeData($data1, 'public://productimage/'.$img1, FileSystemInterface::EXISTS_REPLACE);
      }
      foreach ($file1 as  $fvalue) {
        $display_image_fid[] = $fvalue->id();
      }
      
      if(empty($display_image_fid)){
        $display_image_fid = '';
      }

      $file2 = $gallery_image_fid = array();
      $image_arr2 = explode(",",$image_gallery);
      $fileRepository = \Drupal::service('file.repository');
      foreach ($image_arr2 as  $image2) {
        $img2 = basename($image);
        //$data = file_get_contents($clothing_image);
        $data2 = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/mmanew/web/oldimages/media/product/images/'.$img2);
        $file2[] = $fileRepository->writeData($data2, 'public://productimage/'.$img2, FileSystemInterface::EXISTS_REPLACE);
      }
      foreach ($file2 as  $fvalue2) {
        $gallery_image_fid[] = $fvalue2->id();
      }
      
      if(empty($gallery_image_fid)){
        $gallery_image_fid = '';
      }

      $mp4_arr = '';
      $videos_arr = [];
      $videos_arr = explode(",",$videos);

      $node = Node::create([
        'type'  => 'product_display',
        'title' => $title,
        'field_product_display_body' => $body,
        'field_guitar_category' => $guitar_category,
        'field_product_display_specs' => $guitar_specs,
        'field_image_gallery' => $gallery_image_fid,
        'field_product_display_image' => $display_image_fid,
        'field_mmg_product' => $product_reference,
        'field_product_display_videos' => $mp4_arr,
        'field_youtube_url' => $videos_arr,
        'status' => $status,
      ]);
      $node->save();

      $query = $db->insert('custom_content_relation')->fields([
        'old_nid' => $old_nid,
        'new_nid' => $node->id(),
      ])->execute();
    }*/
    $element = array(
      '#markup' => "output",
    );
    return $element;
  }

  public function customorderimport(){
    //exit();
    $db = \Drupal::database();
    
    /*for Order import */
    // $order_csv = 'csv/custom_commerce_order_export.csv';
    // $csv = $this->read_csv($order_csv);

    /*for Blog content */
    /*foreach ($csv as $key => $value) {
      if ($key == 0) {
        continue;
      }

      $old_order_id = $value[0];
      $old_order_number = $value[1];
      $old_line_item = $value[2];
      $old_order_status = $value[3];
      $old_order_state = $value[4];
      $old_order_type = $value[5];
      $old_order_total = $value[6];
      $old_uid = $value[7];
      $old_order_mail = $value[8];
      $order_billing_information = $value[9];
      $order_shipping_information = $value[10];
      $old_order_comments = $value[11];
      $old_review_nid = $value[12];
      $old_admin_notes = $value[13];
      $old_order_coupons = $value[14];
      $old_order_discount = $value[15];
      $old_order_pre_purchase = $value[16];
      $old_order_tracking_number = $value[17];


      if(empty($old_order_status)){
        $old_order_status = 'canceled';
      }
      if(empty($old_order_state)){
        $old_order_state = 'canceled';
      }

      if($old_order_status == 'Draft'){
        $old_order_status = 'draft';
      }
      if($old_order_status == 'Pre-ordered'){
        $old_order_status = 'preordered';
      }
      if($old_order_status == 'Shopping cart'){
        $old_order_status = 'cart';
      }
      if($old_order_status == 'Pre-order processing failed'){
        $old_order_status = 'preordered_failed';
      }
      if($old_order_status == 'Checkout: Checkout'){
        $old_order_status = 'checkout_checkout';
      }
      if($old_order_status == 'Checkout: Shipping'){
        $old_order_status = 'checkout_shipping';
      }
      if($old_order_status == 'Checkout: Review'){
        $old_order_status = 'checkout_review';
      }
      if($old_order_status == 'Checkout: Payment'){
        $old_order_status = 'checkout_payment';
      }
      if($old_order_status == 'Checkout: Complete'){
        $old_order_status = 'checkout_complete';
      }
      if($old_order_status == 'Pending'){
        $old_order_status = 'pending';
      }
      if($old_order_status == 'Paid In Full'){
        $old_order_status = 'paid_in_full';
      }
      if($old_order_status == 'In Setup'){
        $old_order_status = 'in_setup';
      }
      if($old_order_status == 'Setup Complete'){
        $old_order_status = 'setup_complete';
      }
      if($old_order_status == 'Processing'){
        $old_order_status = 'processing';
      }
      if($old_order_status == 'Shipped'){
        $old_order_status = 'shipped';
      }
      if($old_order_status == 'Prepaid Opeongo'){
        $old_order_status = 'prepaid_opeongo';
      }
      if($old_order_status == 'Prepaid Ian Tyson'){
        $old_order_status = 'prepaid_ian_tyson';
      }
      if($old_order_status == 'Prepaid Tofino'){
        $old_order_status = 'prepaid_tofino';
      }
      if($old_order_status == 'Prepaid Dionisio'){
        $old_order_status = 'prepaid_dionisio';
      }
      if($old_order_status == 'Prepaid Algonquin 12'){
        $old_order_status = 'prepaid_algonquin_12';
      }
      if($old_order_status == 'Prepaid January RB Dread'){
        $old_order_status = 'prepaid_rb_dread';
      }
      if($old_order_status == 'January prepaid Baby Boat'){
        $old_order_status = 'january_prepaid_baby_boat';
      }
      if($old_order_status == 'Pre-order paid'){
        $old_order_status = 'preordered_success';
      }
      if($old_order_status == 'Prepaid Grand Manan'){
        $old_order_status = 'prepaid_grand_manan';
      }
      if($old_order_status == 'Fulfillment'){
        $old_order_status = 'fulfillment';
      }
      if($old_order_status == 'Completed'){
        $old_order_status = 'completed';
      }
      if($old_order_status == 'Canceled'){
        $old_order_status = 'canceled';
      }
      if($old_order_status == 'Refunded'){
        $old_order_status = 'refunded';
      }
      $product_price = $product_price_cod = $discount_name = $discount_title = $discount_price = $discount_price_cod = $tax_price = $tax_price_cod = $tax_name = $shipping_name = $shipping_price = $shipping_price_cod = '';
      
      $servername='localhost';
      $username='mma';
      $password='^be52z0E';
      $databasename='mma';
      $conn=mysqli_connect($servername,$username,$password,$databasename) or die('conn faild');

      $order_created_query = ("SELECT created FROM commerce_order WHERE order_id='$old_order_id'");
      $order_created_result = $conn->query($order_created_query);
      $order_created_time = mysqli_fetch_all($order_created_result);
      $created_time_order = $order_created_time[0][0];

      $query = ("SELECT commerce_order_total_data FROM field_data_commerce_order_total WHERE entity_id='$old_order_id'");
      $result = $conn->query($query);
      $data_res = mysqli_fetch_all($result);
      $amount_array = unserialize($data_res[0][0]);
        
      foreach ($amount_array['components'] as $value) {
        if($value['name']=='base_price'){
          $product_price = $value['price']['amount']/100;
          $product_price_cod = $value['price']['currency_code'];
        }else{
          if(strpos($value['name'],'discount') !== false){
            $discount_name = $value['price']['data']['discount_name'];
            $discount_title = $value['price']['data']['discount_component_title'];
            $discount_price = $value['price']['amount']/100;
            $discount_price_cod = $value['price']['currency_code'];
          }else{
            if(strpos($value['name'],'tax') !== false){
              $tax_price = $value['price']['amount']/100;
              $tax_price_cod = $value['price']['currency_code'];
              $tax_name = $value['price']['data']['tax_rate']['name'];
            }else{
              $shipping_name = $value['name'];
              $shipping_price = $value['price']['amount']/100;
              $shipping_price_cod = $value['price']['currency_code'];
            }
          }
        }
      }
        
      $shi_b = $dis_b = $tax_b =0;
      $euhi_b = $eshi_b = $edis_b = $etax_b =0;

      $line_item = ("SELECT line_item_id , type FROM commerce_line_item WHERE order_id = '$old_order_id'");
      $line_item_result = $conn->query($line_item);
      $line_item_data = mysqli_fetch_all($line_item_result);

        
      $unit_price_cod = '';
      foreach ($line_item_data as $item_value) {
        $line_item_price = ("SELECT commerce_unit_price_amount , commerce_unit_price_currency_code  FROM field_data_commerce_unit_price WHERE entity_id = '$item_value[0]'");
        $line_item_price_result = $conn->query($line_item_price);
        $line_item_price_data = mysqli_fetch_all($line_item_price_result);
        $unit_price = $line_item_price_data[0][0]/100;
        if(!empty($unit_price_cod)){
          if($unit_price_cod != $line_item_price_data[0][1]){
            $euhi_b = 1;
          }
        }
        $unit_price_cod = $line_item_price_data[0][1];
        if(!empty($shipping_price_cod)){
          $shi_b = 2;
          if($unit_price_cod == $shipping_price_cod){
            $shi_b = 0;
          }else{
            $eshi_b = 1;
          }
        }
        if(!empty($discount_price_cod)){
          $dis_b = 2;
          if($unit_price_cod == $discount_price_cod){
            $dis_b = 0;
          }else{
            $edis_b = 1;
          }
        }
        if(!empty($tax_price_cod)){
          $tax_b = 2;
          if($unit_price_cod == $tax_price_cod){
            $tax_b = 0;
          }else{
            $etax_b = 1;
          }
        }
      }

      if(($eshi_b == 0) && ($edis_b == 0) && ($etax_b == 0) && ($euhi_b == 0)){
        if(!empty($tax_name)){
          $tax_label = $tax_name;
          if($tax_name == 'gst'){
            $tax_label = 'GST';
          }
          if($tax_name == 'nb_pei_and_nf_hst'){
            $tax_label = 'PEI and NF HST';
          }
          if($tax_name == 'nova_scotia_hst'){
            $tax_label = 'NB and NS HST';
          }
          if($tax_name == 'ontario_hst'){
            $tax_label = 'Ontario HST';
          }
          if($tax_name == 'quebec_tvq'){
            $tax_label = 'TVQ';
          }
        }

        if(!empty($shipping_name)){
          if($shipping_name=='flat_rate_canada_post_mail'){
            $shipping_method_id = 5;
            $shipping_label = 'Free Shipping for T-Shirts';
          }
          if($shipping_name=='flat_rate_canada_post_to_us'){
            $shipping_method_id = 4;
            $shipping_label = 'Free Shipping on T-Shirts to US';
          }
          if($shipping_name=='flat_rate_canadian_fedex'){
            $shipping_method_id = 3;
            $shipping_label = 'FedEx delivery within Canada';
          }
          if($shipping_name=='flat_rate_canpar'){
            $shipping_method_id = 2;
            $shipping_label = 'CanPar';
          }
          if($shipping_name=='flat_rate_europe'){
            $shipping_method_id = 1;
            $shipping_label = 'Shipping to Europe (including the UK)';
          }
          if($shipping_name=='flat_rate_free_shipping_us'){
            $shipping_method_id = 6;
            $shipping_label = 'Free shipping USA Today!';
          }
          if($shipping_name=='flat_rate_loft_pickup_ca'){
            $shipping_method_id = 7;
            $shipping_label = 'Pick up at our office in Montreal';
          }
          if($shipping_name=='flat_rate_northern_canada'){
            $shipping_method_id = 8;
            $shipping_label = 'Northern Canada';
          }
          if($shipping_name=='flat_rate_rate_labrador'){
            $shipping_method_id = 9;
            $shipping_label = 'Ground shipping to Labrador and Northern Newfoundland';
          }
          if($shipping_name=='flat_rate_us_ups'){
            $shipping_method_id = 10;
            $shipping_label = 'UPS shipping within Continental USA';
          }
          if($shipping_name=='shipping'){
            $shipping_method_id = 11;
            $shipping_label = 'Shipping';
          }
        }else{
          $shipping_method_id = 0;
        }

        // Profle new
        if(!empty($order_billing_information)){
          $new_bill_profile_id_query = $db->query("SELECT new_profile_id FROM custom_customer_profile_relation WHERE old_profile_id='$order_billing_information'");
          $new_bill_profile_id_result = $new_bill_profile_id_query->fetchAll();
          if(!empty($new_bill_profile_id_result)){
            $new_bill_profile_id = $new_bill_profile_id_result[0]->new_profile_id;
          }else{
            $new_bill_profile_id = 0;
          }
        }else{
          $new_bill_profile_id = 0;
        }
        if(!empty($order_shipping_information)){
          $new_ship_profile_id_query = $db->query("SELECT new_profile_id FROM custom_customer_profile_relation WHERE old_profile_id='$order_shipping_information'");
          $new_ship_profile_id_result = $new_ship_profile_id_query->fetchAll();
          if(!empty($new_ship_profile_id_result)){
            $new_ship_profile_id = $new_ship_profile_id_result[0]->new_profile_id;
          }else{
            $new_ship_profile_id = 0;
          }
        }else{
          $new_ship_profile_id = 0;
        }

        if($new_ship_profile_id == 0){
          $new_ship_profile_id = $new_bill_profile_id;
        }

        // for payment method and gateways
        $payment_method_new='';
        $payment_method_query = ("SELECT payment_method FROM commerce_payment_transaction WHERE order_id='$old_order_id'");
        $payment_method_result = $conn->query($payment_method_query);
        $payment_method = mysqli_fetch_all($payment_method_result);
        if(!empty($payment_method)){
          $payment_method_new = $payment_method[0][0];
        }else{
          // $payment_method_new = 0;
          $payment_method_name = '';
        }

        if($payment_method_new == 'commerce_ccod'){
          $payment_method_name = 'phone_order';
        }
        if($payment_method_new == 'commerce_cheque'){
          $payment_method_name = 'cheque';
        }
        if($payment_method_new == 'paypal_wps'){
          $payment_method_name = 'paypal_wps';
        }
        if($payment_method_new == 'paypal_wpp'){
          $payment_method_name = 'paypal_wpp_credit_card';
        }
        if($payment_method_new == 'paypal'){
          $payment_method_name = 'paypal_wpp_credit_card';
        }
        if($payment_method_new == 'other'){
          $payment_method_name = 'other';
        }

        // For product info
        $line_item = ("SELECT line_item_id , type FROM commerce_line_item WHERE order_id = '$old_order_id'");
        $line_item_result = $conn->query($line_item);
        $line_item_data2 = mysqli_fetch_all($line_item_result);
        $order_item_arr = [];
        foreach ($line_item_data2 as $item_value) {
          $line_item_price = ("SELECT commerce_unit_price_amount , commerce_unit_price_currency_code  FROM field_data_commerce_unit_price WHERE entity_id = '$item_value[0]'");
          $line_item_price_result = $conn->query($line_item_price);
          $line_item_price_data = mysqli_fetch_all($line_item_price_result);
          $unit_price = $line_item_price_data[0][0]/100;
          $unit_price_cod = $line_item_price_data[0][1];
          $line_item_product = ("SELECT commerce_product_product_id  FROM field_data_commerce_product WHERE entity_id = '$item_value[0]'");
          $line_item_product_result = $conn->query($line_item_product);
          $line_item_product_data = mysqli_fetch_all($line_item_product_result);
          $old_product_id = $line_item_product_data[0][0];

          $new_product_id_query = $db->query("SELECT new_vid FROM custom_product_relation WHERE old_pid='$old_product_id'");
          $new_product_id_result = $new_product_id_query->fetchAll();
          $new_vid = 0;
          if(!empty($new_product_id_result)){
            $new_vid = $new_product_id_result[0]->new_vid;
            $entity_manager = \Drupal::entityTypeManager();
            $product_variation = $entity_manager->getStorage('commerce_product_variation')->load((int)$new_vid);
            $product_name = $product_variation->get('title')->getValue()[0]['value'];
          }


          $new_user_id_query = $db->query("SELECT new_uid FROM custom_user_relation WHERE old_uid='$old_uid'");
          $new_user_id_result = $new_user_id_query->fetchAll();
          if(!empty($new_user_id_result)){
            $new_uid = $new_user_id_result[0]->new_uid;
          }else{
            $new_uid = 0;
          }
          if($new_vid !== 0){
            $order_item = OrderItem::create([
              'type' => $item_value[1],
              'purchased_entity' => $new_vid,
              'title' => $product_name,
              'quantity' => 1,
              // Omit these lines to preserve original product price.
              'unit_price' => new Price($unit_price, $unit_price_cod),
              'overridden_unit_price' => TRUE,
            ]);
            $order_item->save();
            $order_item_arr[] = $order_item;
          }
        }
        if(count($order_item_arr)>0){
          $order = Order::create([
            'type' => 'default',
            'mail' =>$old_order_mail,
            'uid' => $new_uid,
            'store_id' => 1,
            'field_admin_notes' => $old_admin_notes,
            'field_order_comments' => $old_order_comments,
            'field_review_nid' => $old_review_nid,
            'order_items' => $order_item_arr,
            'placed' => $created_time_order,
            'payment_gateway' => $payment_method_name,
            'checkout_step' => $old_order_state,
            'state' => $old_order_status,
          ]);

          if($new_bill_profile_id !== 0){
            $bill_profile = \Drupal::entityTypeManager()->getStorage('profile')->load($new_bill_profile_id);
            $order->setBillingProfile($bill_profile);
            $order->save();
          }
            
          if($new_ship_profile_id !== 0){
            if($shipping_method_id !== 0){
              $first_shipment = Shipment::create([
                'type' => 'default',
                'order_id' => $order->id(),
                'title' => $shipping_label,
                'state' => 'ready',
              ]);

              foreach ($order->getItems() as $order_item) {
                $quantity = $order_item->getQuantity();
                $purchased_entity = $order_item->getPurchasedEntity();

                $shipment_item = new ShipmentItem([
                  'order_item_id' => $order_item->id(),
                  'title' => $purchased_entity->label(),
                  'quantity' => $quantity,
                  'weight' => new Weight(1, WeightUnit::GRAM),
                  'declared_value' => $order_item->getTotalPrice(),
                ]);
                $first_shipment->addItem($shipment_item);
              }
              
              $first_shipment->setShippingMethodId($shipping_method_id);
              $first_shipment->setAmount(new price($shipping_price,$shipping_price_cod));
              $first_shipment->save();
              $order->set('shipments', [$first_shipment]);
              $order->save();

              $ship_profile = \Drupal::entityTypeManager()->getStorage('profile')->load($new_ship_profile_id);

              $first_shipment->setShippingProfile($ship_profile);
              $first_shipment->save();
              $order->save();
            }
          }

          if(!empty($tax_price)){
            $order->addAdjustment(new Adjustment([
              'type' => 'tax',
              'label' => $tax_label,
              'amount' => new price($tax_price,$tax_price_cod),
              //'percentage' => (string)$percentage,
              #'source_id' => $source_id,
              'included' => FALSE,
            ]));
          }

          if(!empty($discount_name)){
            $order->addAdjustment(new Adjustment([
              'type' => 'promotion',
              'label' => $discount_title,
              'amount' => new price($discount_price,$discount_price_cod),
              //'percentage' => (string)$percentage,
              #'source_id' => $source_id,
              'included' => FALSE,
            ]));
          }

          if($new_ship_profile_id !== 0){
            if(!empty($shipping_name)){
              $order->addAdjustment(new Adjustment([
                'type' => 'shipping',
                'label' => $shipping_label,
                'amount' => new price($shipping_price,$shipping_price_cod),
                //'percentage' => (string)$percentage,
                #'source_id' => $source_id,
                'included' => FALSE,
              ]));
            }
          }

          $order->recalculateTotalPrice();
          
          $order->save();

          if(!empty($payment_method_name)){
            $payment_gateway = \Drupal::entityTypeManager()->getStorage('commerce_payment_gateway')->load($payment_method_name);

            $payment = Payment::create([
              'state' => 'new',
              'amount' => $order->getTotalPrice(),
              'payment_gateway' => $payment_gateway->id(),
              'order_id' => $order->id(),
              #'remote_id' => $values["data"]["result"]["token"]["card"]["id"],
              'payment_gateway_mode' => $payment_gateway->getPlugin()->getMode(),
              'expires' => 0,
              'uid' => $new_uid,
            ]);

            $payment->save();
            $order->setTotalPaid($order->getTotalPrice());
            $order->save();
          }

          $query_new_order = $db->query("SELECT * FROM custom_order_relation WHERE old_order_id='$old_order_id'");
          $query_new_order_result = $query_new_order->fetchAll();
          if(!empty($query_new_order_result)){
            $query = $db->update('custom_order_relation')->fields([
              'new_order_id' => $order->id(),
            ])->condition('old_order_id', $old_order_id, '=')->execute();
          }else{
            $query = $db->insert('custom_order_relation')->fields([
              'old_order_id' => $old_order_id,
              'new_order_id' => $order->id(),
            ])->execute();
          }
        }else{
          $query_new_order = $db->query("SELECT * FROM custom_order_not_import WHERE old_order_id='$old_order_id'");
          $query_new_order_result = $query_new_order->fetchAll();
          if(!empty($query_new_order_result)){
            $query = $db->update('custom_order_not_import')->fields([
              'old_order_id' => $old_order_id,
            ])->condition('old_order_id', $old_order_id, '=')->execute();
          }else{
            $query = $db->insert('custom_order_not_import')->fields([
              'old_order_id' => $old_order_id,
            ])->execute();
          }
        }
      }else{
        $query_new_order = $db->query("SELECT * FROM custom_order_not_import WHERE old_order_id='$old_order_id'");
        $query_new_order_result = $query_new_order->fetchAll();
        if(!empty($query_new_order_result)){
          $query = $db->update('custom_order_not_import')->fields([
            'old_order_id' => $old_order_id,
          ])->condition('old_order_id', $old_order_id, '=')->execute();
        }else{
          $query = $db->insert('custom_order_not_import')->fields([
            'old_order_id' => $old_order_id,
          ])->execute();
        }
      }
    }*/
    $element = array(
      '#markup' => "output",
    );
    return $element;
  }
}