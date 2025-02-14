<?php

namespace Drupal\custom\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Url;
/**
* Class OrderCompleteSubscriber.
*
* @package Drupal\mymodule
*/
class OrderCompleteSubscriber implements EventSubscriberInterface {

  /**
   * This method is called whenever the  commerce_order.place.post_transition event is
   * dispatched.
   *
   * @param WorkflowTransitionEvent $event
   */

  public function orderCompleteHandler(WorkflowTransitionEvent $event) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();
    $order_id = $order->id();
    $db = \Drupal::database();
    $base_path = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'custom';
    $key = 'review_custom_mail';
    // $to = 'piyush@xenixsoft.com';
    $to = $order->getEmail();
    $review_string=md5($order_id);
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
    }else{
      \Drupal::messenger()->addError(t('There is some problem in sending email'));
    }
  }

   /**
    * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['commerce_order.place.post_transition'] =    ['orderCompleteHandler',5];
    return $events;
  }
}