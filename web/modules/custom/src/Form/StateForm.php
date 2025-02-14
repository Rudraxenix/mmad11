<?php
namespace Drupal\custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Url;

class StateForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'cuatom_state_form';
  }
  
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    //Get parameter from url to load an order	
    $path = \Drupal::request()->getpathInfo();
    $arg  = explode('/',$path);
    // Display the results.
    $order_num = $arg[3];
    $state = $form_state->getValue('order_state_name');
    $order = \Drupal::entityTypeManager()->getStorage('commerce_order')->load($order_num);
	  $default ="";
    if(!empty($order->getState()->value)){
      $default = $order->getState()->value;
    }
    $values = array('draft' => t('Draft'),
      'preordered' => t('Pre-ordered'),
      'cart' => t('Shopping cart'),
      'preordered_failed' => t('Pre-order processing failed'),
      'checkout_checkout' => t('Checkout Checkout'),
      'checkout_shipping' => t('Checkout Shipping'),
      'checkout_review' => t('Checkout Review'),
      'checkout_payment' => t('Checkout Payment'),
      'checkout_complete' => t('Checkout Complete'),
      'pending' => t('Pending'),
      'paid_in_full' => t('Paid In Full'),
      'in_setup' => t('In Setup'),
      'setup_complete' => t('Setup Complete'),
      'processing' => t('Processing'),
      'shipped' => t('Shipped'),
      'prepaid_opeongo' => t('Prepaid Opeongo'),
      'prepaid_ian_tyson' => t('Prepaid Ian Tyson'),
      'prepaid_tofino' => t('Prepaid Tofino'),
      'prepaid_dionisio' => t('Prepaid Dionisio'),
      'prepaid_algonquin_12' => t('Prepaid Algonquin 12'),
      'prepaid_rb_dread' => t('Prepaid January RB Dread'),
      'january_prepaid_baby_boat' => t('January prepaid Baby Boat'),
      'preordered_success' => t('Pre-order paid'),
      'prepaid_grand_manan' => t('Prepaid Grand Manan'),
      'fulfillment' => t('Fulfillment'),
      'completed' => t('Completed'),
      'canceled' => t('Canceled'),
      'refunded' => t('Refunded'),
    );
    $form['order_state_name'] = array(
      '#title' => t('Order States'),
      '#type' => 'select',
      '#description' => 'Choose order state & save to change order state',
      '#options' => $values,
      '#default_value' => $default
    );
    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];
    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $path = \Drupal::request()->getpathInfo();
    $arg  = explode('/',$path);
  	$order_num = $arg[3];
    $state = $form_state->getValue('order_state_name');
    $order = \Drupal::entityTypeManager()->getStorage('commerce_order')->load($order_num);
    //$order->getState()->applyTransitionById('fulfill');
    $order->set('state', $state);
    $order->save();
    if($state == 'shipped'){
      $db = \Drupal::database();
      $query = $db->query("SELECT order_id FROM custom_review_table WHERE order_id='$order_num'");
      $result_query = $query->fetchAll();
      if(!empty($result_query)){
        $base_path = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'custom';
        $key = 'review_custom_mail';
        // $to = 'piyush@xenixsoft.com';
        $to = $order->get('mail')->getValue()[0]['value'];
        $review_string=md5($order_num);
        $link=$base_path."order/review/".$review_string;
        $link = '<a href="'.$link.'">Review click here</a>';
        $body='Your order has been successfully delivered review product here '.$link.'';
        $params['subject'] = 'Review product';
        $params['message'] = $body;
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $send = true;
        $result = $mailManager->mail($module, $key, $to,$langcode, $params, NULL, $send);
        if($result['result'] == true){
          \Drupal::messenger()->addStatus(t("Mail sent"));
          $new = $db->insert('custom_review_table')->fields(
            array(
              'order_id'=>$order_id,
              'review_string' => $review_string,
              'status'=>'0'
            )
          )->execute();
        }
      }
    }
    $messenger = \Drupal::messenger();
    $messenger->addMessage('State Change Succesfully');
    $path = '/admin/commerce/orders';
    $url = Url::fromUserInput($path);
    $form_state->setRedirectUrl($url);
  } 
}