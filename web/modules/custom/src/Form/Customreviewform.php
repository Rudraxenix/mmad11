<?php 
/**
* @file
* Contains \Drupal\custom\Form\Customreviewform
*/
namespace Drupal\custom\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;
use \Drupal\node\Entity\Node;
use Drupal\profile\Entity\Profile;
use Drupal\user\Entity\User;

class Customreviewform extends FormBase {

  public function getFormId() {
    return 'custom_review_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $db = \Drupal::database();
    $base_path = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    $current_path = \Drupal::service('path.current')->getPath();
    $agenda_id= explode("/", $current_path);
    $agenda_id = $agenda_id[3];

    $query = $db->query("SELECT order_id FROM custom_review_table WHERE review_string='$agenda_id' AND status=0");
    $result = $query->fetchAll();
    if(!empty($result)){
      $order_number = $result[0]->order_id;
      $order= \Drupal::entityTypeManager()->getStorage('commerce_order')->load($order_number);
      $user = $order->getCustomer();
      $first_name = $user->field_first_name->value;
      if(empty($first_name)){
        $form['first_name'] = array(
          '#type' => 'textfield',
          '#title' => t('First name'),
          '#required' => TRUE,    
        );
      }else{
        $form['first_name'] = array(
          '#type' => 'hidden',
          '#default_value' => $first_name,
        );
      }

      $form['review_intro'] = array(
        '#type' => 'textfield',
        '#title' => t('Review Intro'),
        '#required' => TRUE,    
      );

      $form['star_rating'] = array(
        '#title' => t('Star Rating'),
        '#type' => 'select',
        '#options' => array(
          '1' => '1',
          '2' => '2',
          '3' => '3',
          '4' => '4',
          '5' => '5'
        ),
        '#required' => TRUE,
      );

      $form['order_name'] = array(
        '#type' => 'hidden',
        '#default_value' => $order_number,
      );

      $form['review_text'] = array(
        '#type' => 'textarea',
        '#title' => t('Review'),
        '#required' => TRUE,    
      );
      
      $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Save'),
        '#button_type' => 'primary',
      );
    }else{
      $form['markup_text'] = array(
        '#type' => 'markup',
        '#markup' => '<div class="no-review">Already fill review for order<a href="'.$base_path.'">Back to home</a></div>',
      );
    }

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $star_rating = $form_state->getValue('star_rating');
    $review_intro = $form_state->getValue('review_intro');
    $review_text = $form_state->getValue('review_text');
    $order_number = $form_state->getValue('order_name');
    $db = \Drupal::database();

    //$order_number = 4580;
    $order= \Drupal::entityTypeManager()->getStorage('commerce_order')->load($order_number);
    foreach ($order->getItems() as $key => $order_item) {
      $product_variation = $order_item->getPurchasedEntity();
      $sku = $product_variation->getSku();
      $product = $product_variation->getProduct();
      $reviewed_product = $product->id();
      break;
    }
    $created_time = date('D, m/d/Y - H:i', $order->getPlacedTime());
    $title = 'Review '.$order_number.' '.$created_time;

    $user = $order->getCustomer();
    $first_name = $user->field_first_name->value;
    if(empty($first_name)){
      $first_name = $form_state->getValue('first_name');
    }

    $city = $order->billing_profile->entity->address->getValue()[0]['locality'];
    $status = 0;
    $review_status = 0;
    $node = Node::create([
      'type'  => 'mmg_review',
      'title' => $title,
      'field_review_status' => $review_status,
      'field_reviewed_products' => $reviewed_product,
      'field_sku' => $sku,
      'field_guitar_order' => $order_number,
      'field_review_firstname' => $first_name,
      'field_review_city' => $city,
      'field_review_intro' => $review_intro,
      'field_mmg_review' => $review_text,
      'field_star_rating' => $star_rating,
      'status' => $status,
    ]);
    $node->save();

    $new = $db->update('custom_review_table')->fields(
      array(
        'status'=>'1'
      )
    )->condition('order_id',$order_number)->execute();

    $url = Url::fromRoute('<front>');
    $form_state->setRedirectUrl($url);
  }
}