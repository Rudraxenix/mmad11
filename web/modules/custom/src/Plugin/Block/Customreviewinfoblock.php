<?php

namespace Drupal\custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\commerce\commerce_product;
use Drupal\commerce;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;



/**
 * Provides a 'Custom' Block.
 *
 * @Block(
 *   id = "Customreviewinfoblock",
 *   admin_label = @Translation("Customer review Info Block"),
 *   category = @Translation("Customreviewinfoblock"),
 * )
 */
class Customreviewinfoblock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
  	$output = $empty_star = $filled_star ='';
    $product_id = \Drupal::routeMatch()->getParameter('commerce_product')->id(); 
  	$query = \Drupal::entityQuery('node')->accessCheck(FALSE)->condition('field_review_status', '1')->condition('type', 'mmg_review')->condition('field_reviewed_products',$product_id);
  	$nids = $query->execute();
    $output.='<div class="reviews-outer-wrapper">
      <div class="section-title p-5 text-center">
        <h2>Reviews</h2>
      </div>
      <table class="table-responsive table table-bordered">';
  	foreach ($nids as $nid) {
      $filled_star = $empty_star = '';
  		$node = \Drupal\node\Entity\Node::load($nid);
      $star_rating = $node->get('field_star_star_rating')->getValue()[0]['value'];
      $first_name = $node->get('field_review_firstname')->getValue()[0]['value'];
      if(!empty($node->get('field_mmg_review')->getValue())) {
        $review_text = $node->get('field_mmg_review')->getValue()[0]['value'];
      }else {
        $review_text = '';
      }
      $i=1;
      $j=$star_rating;
      for($i=1;$i<=$star_rating;$i++) {
        $filled_star .= '<span class="filled"><svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#ffa41c">
          <g id="SVGRepo_bgCarrier" stroke-width="0"/>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
          <g id="SVGRepo_iconCarrier"> <g> <path fill="#ffa41c" d="M31.998,2.478c0.279,0,0.463,0.509,0.463,0.509l8.806,18.759l20.729,3.167l-14.999,15.38l3.541,21.701 l-18.54-10.254l-18.54,10.254l3.541-21.701L2,24.912l20.729-3.167l8.798-18.743C31.527,3.002,31.719,2.478,31.998,2.478 M31.998,0 c-0.775,0-1.48,0.448-1.811,1.15l-8.815,18.778L1.698,22.935c-0.741,0.113-1.356,0.632-1.595,1.343 c-0.238,0.71-0.059,1.494,0.465,2.031l14.294,14.657L11.484,61.67c-0.124,0.756,0.195,1.517,0.822,1.957 c0.344,0.243,0.747,0.366,1.151,0.366c0.332,0,0.666-0.084,0.968-0.25l17.572-9.719l17.572,9.719 c0.302,0.166,0.636,0.25,0.968,0.25c0.404,0,0.808-0.123,1.151-0.366c0.627-0.44,0.946-1.201,0.822-1.957l-3.378-20.704 l14.294-14.657c0.523-0.537,0.703-1.321,0.465-2.031c-0.238-0.711-0.854-1.229-1.595-1.343l-19.674-3.006L33.809,1.15 C33.479,0.448,32.773,0,31.998,0L31.998,0z"/> <path fill="#ffa41c" d="M31.998,2.478c0.279,0,0.463,0.509,0.463,0.509l8.806,18.759l20.729,3.167l-14.999,15.38l3.541,21.701 l-18.54-10.254l-18.54,10.254l3.541-21.701L2,24.912l20.729-3.167l8.798-18.743C31.527,3.002,31.719,2.478,31.998,2.478"/> </g> </g>
        </svg></span>';
      }
      for($j;$j<5;$j++){
        $empty_star .= '<span class="empty"><svg version="1.0" id="Layerempty" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#000000">
          <g id="SVGRepo_bgCarrier" stroke-width="0"/>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
          <g id="SVGRepo_iconCarrier"> <g> <path fill="#394240" d="M31.998,2.478c0.279,0,0.463,0.509,0.463,0.509l8.806,18.759l20.729,3.167l-14.999,15.38l3.541,21.701 l-18.54-10.254l-18.54,10.254l3.541-21.701L2,24.912l20.729-3.167l8.798-18.743C31.527,3.002,31.719,2.478,31.998,2.478 M31.998,0 c-0.775,0-1.48,0.448-1.811,1.15l-8.815,18.778L1.698,22.935c-0.741,0.113-1.356,0.632-1.595,1.343 c-0.238,0.71-0.059,1.494,0.465,2.031l14.294,14.657L11.484,61.67c-0.124,0.756,0.195,1.517,0.822,1.957 c0.344,0.243,0.747,0.366,1.151,0.366c0.332,0,0.666-0.084,0.968-0.25l17.572-9.719l17.572,9.719 c0.302,0.166,0.636,0.25,0.968,0.25c0.404,0,0.808-0.123,1.151-0.366c0.627-0.44,0.946-1.201,0.822-1.957l-3.378-20.704 l14.294-14.657c0.523-0.537,0.703-1.321,0.465-2.031c-0.238-0.711-0.854-1.229-1.595-1.343l-19.674-3.006L33.809,1.15 C33.479,0.448,32.773,0,31.998,0L31.998,0z"/> <path fill="#ffffff" d="M31.998,2.478c0.279,0,0.463,0.509,0.463,0.509l8.806,18.759l20.729,3.167l-14.999,15.38l3.541,21.701 l-18.54-10.254l-18.54,10.254l3.541-21.701L2,24.912l20.729-3.167l8.798-18.743C31.527,3.002,31.719,2.478,31.998,2.478"/> </g> </g>
        </svg></span>';
      }
      $review_number = '<div class="rating-icons">
        '.$filled_star.'
        '.$empty_star.'
      </div>';
      $output.='<tr data-nid="'.$nid.'">
        <td width="130px">'.$review_number.'</td>
        <td><strong>'.$first_name.'</strong>
        <p>'.$review_text.'</p></td>
      </tr>';
  	}
    $output.='</table></div>';
	  $element = array(
			'#markup' => $output,
			'#cache' => array(
			'max-age' => 0,
		),
		'#allowed_tags' => ['section','h2','select','textarea','table','tr','td','tbody','thead','tbody','th','option','button','label','source','div','hr','img','p','input','form','strong','h4','span','style','h3','i','svg','g','path','ul','<','li','a','h1'],
		);
		return $element;
  }
}