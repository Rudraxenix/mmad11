<?php
namespace Drupal\custom\Plugin\Block;

use Drupal\commerce;
use Drupal\node\Entity\Node;
use Drupal\Core\Block\BlockBase;
use Drupal\commerce\commerce_product;
use Drupal\commerce_product\Entity\Product;

/**
 * Provides a 'Custom' Block.
 *
 * @Block(
 *   id = "productspecificationblock",
 *   admin_label = @Translation("Product Specification Block"),
 *   category = @Translation("productspecificationblock"),
 * )
 */
class ProductSpecificationblock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
  	$output = '';
    if(\Drupal::routeMatch()->getParameter('commerce_product')) {
      $product_id = \Drupal::routeMatch()->getParameter('commerce_product')->id();
      $product = Product::load((int)$product_id);
      if(!empty($product->get('field_product_specification')->getValue())) {
        $specification_nid = $product->get('field_product_specification')->getValue()[0]['target_id'];
        $node = Node::load($specification_nid);
        if(is_object($node)) {
          $dimension_output = $material_output = $extra_output = '';
          if(!empty($node->get('field_neck_profile')->getValue())){
            $field_value = $node->get('field_neck_profile')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Neck Profile:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_frets_to_body')->getValue())){
            $field_value = $node->get('field_frets_to_body')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Frets To The Body:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_scale_length')->getValue())){
            $field_value = $node->get('field_scale_length')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Scale Length:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_neck_radius')->getValue())){
            $field_value = $node->get('field_neck_radius')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Fretboard Radius:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_nut_width')->getValue())){
            $field_value = $node->get('field_nut_width')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Neck width at nut:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_etoe')->getValue())){
            $field_value = $node->get('field_etoe')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">E to E string spacing at nut:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_body_style')->getValue())){
            $field_value = $node->get('field_body_style')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Body Style:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_width_first_bout')->getValue())){
            $field_value = $node->get('field_width_first_bout')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Width at Upper Bout:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_etoesaddle')->getValue())){
            $field_value = $node->get('field_etoesaddle')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">E to E string spacing at saddle:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_width_at_waist')->getValue())){
            $field_value = $node->get('field_width_at_waist')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Width at Waist:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_width_second_bout')->getValue())){
            $field_value = $node->get('field_width_second_bout')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Width at Lower Bout:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_body_depth')->getValue())){
            $field_value = $node->get('field_body_depth')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Body Depth:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_body_length')->getValue())){
            $field_value = $node->get('field_body_length')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Body Length:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_overall_length')->getValue())){
            $field_value = $node->get('field_overall_length')->getValue()[0]['value'];
            $dimension_output .= '<div class="field-item">
              <div class="">Overall Length:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_body_finish')->getValue())){
            $field_value = $node->get('field_body_finish')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Body finish:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_neck_finish')->getValue())){
            $field_value = $node->get('field_neck_finish')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Neck finish:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_fretboard')->getValue())){
            $field_value = $node->get('field_fretboard')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Fretboard and Bridge:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_materials_neck')->getValue())){
            $field_value = $node->get('field_materials_neck')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Neck:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_tuners')->getValue())){
            $field_value = $node->get('field_tuners')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Tuning Machines:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_backsides')->getValue())){
            $field_value = $node->get('field_backsides')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Body Back and Sides:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_top')->getValue())){
            $field_value = $node->get('field_top')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Body Top Wood:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_materials_nut_saddle')->getValue())){
            $field_value = $node->get('field_materials_nut_saddle')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Nut & Saddle:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_bracing')->getValue())){
            $field_value = $node->get('field_bracing')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Bracing:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_strings')->getValue())){
            $field_value = $node->get('field_strings')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Strings:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_bridge_pins')->getValue())){
            $field_value = $node->get('field_bridge_pins')->getValue()[0]['value'];
            $material_output .= '<div class="field-item">
              <div class="">Bridge Pins:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_case')->getValue())){
            $field_value = $node->get('field_case')->getValue()[0]['value'];
            $extra_output .= '<div class="field-item">
              <div class="">Case:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }
          if(!empty($node->get('field_electronics')->getValue())){
            $field_value = $node->get('field_electronics')->getValue()[0]['value'];
            $extra_output .= '<div class="field-item">
              <div class="">Electronics:</div>
              <div class="">'.$field_value.'</div>
            </div>';
          }

          $iframe = '';
          $videos_nids = \Drupal::entityQuery('node')->accessCheck(FALSE)->condition('status', 1)->condition('type', 'newvideopage')->condition('field_guitar', $product_id)->execute();
          if(!empty($videos_nids)) {
            $videos_nodes = \Drupal\node\Entity\Node::loadMultiple($videos_nids);
            foreach($videos_nodes as $video_node) {
              if($video_node->get('field_youtube_video_url')->getValue()) {
                $url = $video_node->get('field_youtube_video_url')->getValue()[0]['value'];
                $iframe.='<iframe width="420" height="315"
                  src='.$url.'>
                </iframe>';
              }
            }
          }

          $output.= '<div class="specification-wrapper">
            <div class="section-title p-5 text-center">
              <h2>Specifications</h2>
            </div>
            <div class="accordion row" id="accordionproductspecification">
              <div class="accordion-item col-md-6">
                <h2 class="accordion-header" id="flush-headingOne">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    Specification
                  </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionproductspecification">
                  <div class="accordion-body">
                    <div class="">
                      <h3>Dimensions</h3>
                      <div class="items-wrapper">
                        <div class="field-items">
                          '.$dimension_output.'
                        </div>
                      </div>
                    </div>
                    <div class="">
                      <h3>Materials</h3>
                      <div class="items-wrapper">
                        <div class="field-items">
                          '.$material_output.'
                        </div>
                      </div>
                    </div>
                    <div class="">
                      <h3>Extras</h3>
                      <div class="items-wrapper">
                        <div class="field-items">
                          '.$extra_output.'
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="accordion-item col-md-6">
                <h2 class="accordion-header" id="flush-headingTwo">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    Video
                  </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionproductspecification">
                  <div class="accordion-body">
                    '.$iframe.'
                  </div>
                </div>
              </div>
            </div>
          </div>';
        }else {
          $output = 'Not a Node';
        }
      }
    }else {
      $output = 'Not a product page';
    }
	  $element = array(
			'#markup' => $output,
			'#cache' => array(
			'max-age' => 0,
		),
		'#allowed_tags' => ['section','h2','select','textarea','table','tr','td','tbody','thead','tbody','th','option','button','label','source','div','img','p','input','form','strong','h4','span','style','h3','i','ul','<','li','a','h1','iframe'],
		);
		return $element;
  }
}