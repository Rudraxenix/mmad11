<?php

namespace Drupal\custom\Resolver;

use Drupal\commerce\Context;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_price\Resolver\PriceResolverInterface;
use Drupal\commerce_price\Price;
use \Drupal\user\Entity\User;
use \Drupal\smart_ip\SmartIp;


/**
 * Returns the price based on the current user being a VIP user.
 */
class CustomPriceResolver implements PriceResolverInterface {
  /** @var \Drupal\smart_ip\SmartIpLocation $location */
  /**
   * {@inheritdoc}
   */
  public function resolve(PurchasableEntityInterface $entity, $quantity, Context $context) {
    \Drupal::service('page_cache_kill_switch')->trigger();
    // $geo_locator = SmartIp::query();
    $geo_locator = \Drupal::service('smart_ip.smart_ip_location');
    // print_r($geo_locator);
    // exit();
  	if($geo_locator->get('countryCode') == 'US') {
      if($entity->hasField('field_commerce_price_usd')){
        $us_price = $entity->get('field_commerce_price_usd');
        if(!empty($us_price->number)){
          return new Price($us_price->number, $us_price->currency_code);
        }else{
          return $entity->getPrice();  
        }
      }else{
        return $entity->getPrice();  
      }
    }elseif($geo_locator->get('countryCode') == 'FR') {
      if($entity->hasField('field_price_in_euro')){
        $eu_price = $entity->get('field_price_in_euro');
        if(!empty($eu_price->number)){
          return new Price($eu_price->number, $eu_price->currency_code);
        }else{
          return $entity->getPrice();
        }
      }else{
        return $entity->getPrice();  
      }
    }else {
      return $entity->getPrice();
    }
  }
}