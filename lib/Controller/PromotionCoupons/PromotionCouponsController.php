<?php namespace Vankosoft\PaymentBundle\Controller\PromotionCoupons;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class PromotionCouponsController extends AbstractCrudController
{
    /**
     * @throws NotFoundHttpException
     */
    public function generateAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        
        if (null === $promotionId = $request->attributes->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given.');
        }
        
        if (null === $promotion = $this->container->get('sylius.repository.promotion')->find($promotionId)) {
            throw new NotFoundHttpException('Promotion not found.');
        }
        
        $form = $this->container->get('form.factory')->create(PromotionCouponGeneratorInstructionType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getGenerator()->generate($promotion, $form->getData());
            $this->flashHelper->addSuccessFlash($configuration, 'generate');
            
            return $this->redirectHandler->redirectToResource($configuration, $promotion);
        }
        
        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form));
        }
        
        return $this->render(
            $configuration->getTemplate('generate.html'),
            [
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'promotion' => $promotion,
                'form' => $form->createView(),
            ]
            );
    }
    
    protected function getGenerator(): PromotionCouponGeneratorInterface
    {
        return $this->container->get('sylius.promotion_coupon_generator');
    }
    
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        
    }
}