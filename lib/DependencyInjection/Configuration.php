<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\PaymentBundle\Model\GatewayConfig;
use Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfigController;
use Vankosoft\PaymentBundle\Form\GatewayConfigForm;

use Vankosoft\PaymentBundle\Model\PaymentMethod;
use Vankosoft\PaymentBundle\Controller\Configuration\PaymentMethodConfigController;
use Vankosoft\PaymentBundle\Form\PaymentMethodForm;

use Vankosoft\PaymentBundle\Model\Payment;
use Vankosoft\PaymentBundle\Controller\OrdersAndPayments\RecievedPaymentsController;
use Vankosoft\PaymentBundle\Repository\PaymentRepository;

use Vankosoft\PaymentBundle\Model\PaymentToken;

use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Repository\OrderRepository;
use Vankosoft\PaymentBundle\Controller\OrdersAndPayments\OrdersController;

use Vankosoft\PaymentBundle\Model\OrderItem;

use Vankosoft\PaymentBundle\Model\Currency;
use Vankosoft\PaymentBundle\Form\CurrencyForm;
use Vankosoft\PaymentBundle\Controller\Configuration\CurrencyController;
use Vankosoft\PaymentBundle\Model\ExchangeRate;
use Vankosoft\PaymentBundle\Repository\ExchangeRateRepository;
use Vankosoft\PaymentBundle\Form\ExchangeRateForm;
use Vankosoft\PaymentBundle\Controller\Configuration\ExchangeRateController;

// use Vankosoft\PaymentBundle\Model\Coupon;
// use Vankosoft\PaymentBundle\Controller\PromotionCoupons\CouponsController;
// use Vankosoft\PaymentBundle\Form\CouponForm;

use Vankosoft\PaymentBundle\Model\Promotion;
use Vankosoft\PaymentBundle\Repository\PromotionRepository;
use Vankosoft\PaymentBundle\Controller\PromotionCoupons\PromotionsController;
use Vankosoft\PaymentBundle\Form\PromotionForm;

use Vankosoft\PaymentBundle\Model\PromotionCoupon;
use Vankosoft\PaymentBundle\Repository\PromotionCouponRepository;
use Vankosoft\PaymentBundle\Controller\PromotionCoupons\PromotionCouponsController;
use Vankosoft\PaymentBundle\Form\PromotionCouponForm;

use Vankosoft\PaymentBundle\Model\PromotionAction;
use Vankosoft\PaymentBundle\Form\Type\PromotionActionType;

use Vankosoft\PaymentBundle\Model\PromotionRule;
use Vankosoft\PaymentBundle\Form\Type\PromotionRuleType;

use Vankosoft\PaymentBundle\Model\Adjustment;
use Vankosoft\PaymentBundle\Model\CustomerGroup;

use Vankosoft\PaymentBundle\Component\Payment\Payment as ComponentPayment;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder    = new TreeBuilder( 'vs_payment' );
        $rootNode       = $treeBuilder->getRootNode();
        
        $rootNode
            ->children()
                ->scalarNode( 'orm_driver' )
                    ->defaultValue( SyliusResourceBundle::DRIVER_DOCTRINE_ORM )->cannotBeEmpty()
                ->end()
                ->scalarNode( 'token_storage' )
                    ->defaultValue( ComponentPayment::TOKEN_STORAGE_DOCTRINE_ORM )->cannotBeEmpty()
                ->end()
                ->arrayNode( 'http_client' )
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode( 'verify_peer' )->defaultTrue()->end()
                        ->booleanNode( 'verify_host' )->defaultTrue()->end()
                ->end()
            ->end()
        ;
        
        $this->addResourcesSection( $rootNode );

        return $treeBuilder;
    }
    
    private function addResourcesSection( ArrayNodeDefinition $node ): void
    {
        $node
            ->children()
                ->arrayNode( 'resources' )
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode( 'gateway_config' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( GatewayConfig::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( GatewayConfigController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( GatewayConfigForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'payment_method' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( PaymentMethod::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( PaymentMethodConfigController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( PaymentMethodForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        
                        /* This should to be resource, Because otherwise I can't take the class name in concrete project
                         * ===============================================================================================
                         */
                        ->arrayNode( 'payment' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Payment::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( PaymentRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( RecievedPaymentsController::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'payment_token' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( PaymentToken::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'order' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Order::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( OrderRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( OrdersController::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'order_item' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( OrderItem::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'currency' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Currency::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( CurrencyController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( CurrencyForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'exchange_rate' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( ExchangeRate::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( ExchangeRateController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( ExchangeRateRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( ExchangeRateForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        
                        
//                         ->arrayNode( 'coupon' )
//                             ->addDefaultsIfNotSet()
//                             ->children()
//                                 ->variableNode( 'options' )->end()
//                                 ->arrayNode( 'classes' )
//                                     ->addDefaultsIfNotSet()
//                                     ->children()
//                                         ->scalarNode( 'model' )->defaultValue( Coupon::class )->cannotBeEmpty()->end()
//                                         ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
//                                         ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
//                                         ->scalarNode( 'controller' )->defaultValue( CouponsController::class )->cannotBeEmpty()->end()
//                                         ->scalarNode( 'form' )->defaultValue( CouponForm::class )->cannotBeEmpty()->end()
//                                     ->end()
//                                 ->end()
//                             ->end()
//                         ->end()
                        
                        ->arrayNode( 'promotion' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Promotion::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( PromotionRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( PromotionsController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( PromotionForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'promotion_coupon' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( PromotionCoupon::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( PromotionCouponRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( PromotionCouponsController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( PromotionCouponForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'promotion_action' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( PromotionAction::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( PromotionActionType::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'promotion_rule' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( PromotionRule::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( PromotionRuleType::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'adjustment' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Adjustment::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'customer_group' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( CustomerGroup::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                    ->end()
                ->end()
            ->end()
        ;
    }
}
