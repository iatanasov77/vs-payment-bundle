<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\PaymentBundle\Model\GatewayConfig;
use Vankosoft\PaymentBundle\Model\Interfaces\GatewayConfigInterface;
use Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfigController;
use Vankosoft\PaymentBundle\Form\GatewayConfigForm;

use Vankosoft\PaymentBundle\Model\PaymentMethod;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentMethodInterface;
use Vankosoft\PaymentBundle\Controller\Configuration\PaymentMethodConfigController;
use Vankosoft\PaymentBundle\Form\PaymentMethodForm;

use Vankosoft\PaymentBundle\Model\Payment;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;
use Vankosoft\PaymentBundle\Controller\PaidSubscriptions\RecievedPaymentsController;

use Vankosoft\PaymentBundle\Model\PaymentToken;

use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Repository\OrderRepository;
use Vankosoft\PaymentBundle\Controller\PaidSubscriptions\OrdersController;

use Vankosoft\PaymentBundle\Model\OrderItem;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;

//use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Vankosoft\PaymentBundle\Model\Currency;
use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;
use Vankosoft\PaymentBundle\Form\CurrencyForm;
use Vankosoft\PaymentBundle\Controller\Configuration\CurrencyController;
use Vankosoft\PaymentBundle\Model\ExchangeRate;
use Vankosoft\PaymentBundle\Repository\ExchangeRateRepository;
use Vankosoft\PaymentBundle\Form\ExchangeRateForm;
use Vankosoft\PaymentBundle\Controller\Configuration\ExchangeRateController;

use Vankosoft\PaymentBundle\Model\Product;
use Vankosoft\PaymentBundle\Model\Interfaces\ProductInterface;
use Vankosoft\PaymentBundle\Form\ProductForm;
use Vankosoft\PaymentBundle\Controller\Catalog\ProductController;
use Vankosoft\PaymentBundle\Model\ProductCategory;
use Vankosoft\PaymentBundle\Model\Interfaces\ProductCategoryInterface;
use Vankosoft\PaymentBundle\Repository\ProductCategoryRepository;
use Vankosoft\PaymentBundle\Form\ProductCategoryForm;
use Vankosoft\PaymentBundle\Controller\Catalog\ProductCategoryController;
use Vankosoft\PaymentBundle\Model\ProductPicture;
use Vankosoft\PaymentBundle\Model\Interfaces\ProductPictureInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanCategoryInterface;
use Vankosoft\PaymentBundle\Model\PricingPlanCategory;
use Vankosoft\PaymentBundle\Repository\PricingPlanCategoryRepository;
use Vankosoft\PaymentBundle\Controller\PricingPlans\PricingPlanCategoryController;
use Vankosoft\PaymentBundle\Form\PricingPlanCategoryForm;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Vankosoft\PaymentBundle\Model\PricingPlan;
use Vankosoft\PaymentBundle\Controller\PricingPlans\PricingPlanController;
use Vankosoft\PaymentBundle\Form\PricingPlanForm;
use Vankosoft\PaymentBundle\Repository\PricingPlansRepository;


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
                                        ->scalarNode( 'interface' )->defaultValue( GatewayConfigInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( PaymentMethodInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( PaymentInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( OrderInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( OrderItemInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( CurrencyInterface::class )->cannotBeEmpty()->end()
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
                                        ->scalarNode( 'interface' )->defaultValue( ExchangeRateInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( ExchangeRateController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( ExchangeRateRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( ExchangeRateForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'product' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Product::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ProductInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( ProductController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( ProductForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'product_picture' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( ProductPicture::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ProductPictureInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( EntityRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'product_category' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( ProductCategory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ProductCategoryInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( ProductCategoryController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( ProductCategoryRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( ProductCategoryForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'pricing_plan_category' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( PricingPlanCategory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( PricingPlanCategoryInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( PricingPlanCategoryRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( PricingPlanCategoryController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( PricingPlanCategoryForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'pricing_plan' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( PricingPlan::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( PricingPlanInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( PricingPlansRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( PricingPlanController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( PricingPlanForm::class )->cannotBeEmpty()->end()
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
