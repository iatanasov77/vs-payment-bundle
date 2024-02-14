<?php namespace Vankosoft\PaymentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterPromotionActionsPass implements CompilerPassInterface
{
    public function process( ContainerBuilder $container ): void
    {
        if (
            ! $container->has( 'vs_payment.registry_promotion_action' ) ||
            ! $container->has( 'vs_payment.form_registry.promotion_action' )
        ) {
            return;
        }
        
        $promotionActionRegistry            = $container->getDefinition( 'vs_payment.registry_promotion_action' );
        $promotionActionFormTypeRegistry    = $container->getDefinition( 'vs_payment.form_registry.promotion_action' );
        
        $promotionActionTypeToLabelMap = [];
        foreach ( $container->findTaggedServiceIds( 'vs_payment.promotion_action' ) as $id => $attributes ) {
            foreach ( $attributes as $attribute ) {
                if ( ! isset($attribute['type'], $attribute['label'], $attribute['form_type'] ) ) {
                    throw new \InvalidArgumentException( 'Tagged promotion action `' . $id . '` needs to have `type`, `form_type` and `label` attributes.' );
                }
                
                $promotionActionTypeToLabelMap[$attribute['type']]  = $attribute['label'];
                $promotionActionRegistry->addMethodCall( 'register', [$attribute['type'], new Reference( $id )] );
                $promotionActionFormTypeRegistry->addMethodCall( 'add', [$attribute['type'], 'default', $attribute['form_type']] );
            }
        }
        
        $container->setParameter( 'vs_payment.promotion_actions', $promotionActionTypeToLabelMap );
    }
}