<?php
/**
* @file
* @author 
* Contains \Drupal\custom\Controller\CustomCartController.
*/

namespace Drupal\custom\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\commerce\commerce_product;
use Drupal\commerce;
use Drupal\commerce_cart;
use Drupal\commerce_order\Entity\Order;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
* Provides route responses for the Custom module.
*/
class CustomCartController extends ControllerBase {
	/**
	* Returns a simple page.
	*
	* @return array
	* A simple renderable array.
	*/
	/**
	* The cart manager.
	*
	* @var \Drupal\commerce_cart\CartManagerInterface
	*/
	protected $cartManager;
	 
	/**
	* The cart provider.
	*
	* @var \Drupal\commerce_cart\CartProviderInterface
	*/
	protected $cartProvider;
	 
	/**
	* Constructs a new CartController object.
	*
	* @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
	*   The cart provider.
	*/
	public function __construct(CartManagerInterface $cart_manager,CartProviderInterface $cart_provider) {
	 $this->cartManager = $cart_manager;
	 $this->cartProvider = $cart_provider;
	}
		 
	/**
	* {@inheritdoc}
	*/
	public static function create(ContainerInterface $container) {
	 return new static(
	   $container->get('commerce_cart.cart_manager'),
	   $container->get('commerce_cart.cart_provider')
	 );
	}
		
	public function custom_cart_page() {
		if(isset($_POST['current_sid'])){
			$destination = \Drupal::service('path.current')->getPath();
			if($_POST['current_sid'] == 0){
				$productId = $_POST['current_pid'];
				$product_quantity = $_POST['product_quantity'];
		   		$productObj = \Drupal\commerce_product\Entity\Product::load($productId);
				$product_variation_id = $productObj->get('variations')->getValue()[0]['target_id'];
		   		$storeId = $productObj->get('stores')->getValue()[0]['target_id'];
				$variationobj = \Drupal::entityTypeManager()->getStorage('commerce_product_variation')->load($product_variation_id);
				$store = \Drupal::entityTypeManager()->getStorage('commerce_store')->load($storeId);
				$cart = $this->cartProvider->getCart('default', $store);
				if (!$cart) {
					$cart = $this->cartProvider->createCart('default', $store);
				}
				$line_item_type_storage = \Drupal::entityTypeManager()->getStorage('commerce_order_item_type');
				// Process to place order programatically.
		   		$cart_manager = \Drupal::service('commerce_cart.cart_manager');
		   		for($k=0;$k<$product_quantity;$k++){
		   			$line_item = $cart_manager->addEntity($cart, $variationobj);
		   		}	
			}else{
				$current_pid = $_POST['current_pid'];
				$current_sid = $_POST['current_sid'];
				$product_quantity = $_POST['product_quantity'];
				for($i=0;$i<2;$i++){
					$productId = 0;
					if($i==0){
						$productId = $current_pid;
					}else{
						if($i==1){
							$productId = $current_sid;
						}
					}
					if($productId != 0){
				   		$productObj = \Drupal\commerce_product\Entity\Product::load($productId);
						$product_variation_id = $productObj->get('variations')->getValue()[0]['target_id'];
				   		$storeId = $productObj->get('stores')->getValue()[0]['target_id'];
						$variationobj = \Drupal::entityTypeManager()->getStorage('commerce_product_variation')->load($product_variation_id);
						$store = \Drupal::entityTypeManager()->getStorage('commerce_store')->load($storeId);
						$cart = $this->cartProvider->getCart('default', $store);
						if (!$cart) {
							$cart = $this->cartProvider->createCart('default', $store);
						}
						$line_item_type_storage = \Drupal::entityTypeManager()->getStorage('commerce_order_item_type');
						// Process to place order programatically.
				   		$cart_manager = \Drupal::service('commerce_cart.cart_manager');
				   		if($i==0){
					   		for($k=0;$k<$product_quantity;$k++){
					   			$line_item = $cart_manager->addEntity($cart, $variationobj);
					   		}
					   	}
					}
				}
			}
			$response = new RedirectResponse(Url::fromRoute('commerce_cart.page')->toString());
			return $response;
		}
	}
}