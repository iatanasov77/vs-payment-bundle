# VankoSoft Symfony Application Extension - Payment Bundle

I. Register/Initialize Payment Bundle
-------------------------------------

Add to config/bundles.php
```php
return [
    ...
    
    Vankosoft\PaymentBundle\VSPaymentBundle::class => ['all' => true],
    Payum\Bundle\PayumBundle\PayumBundle::class => ['all' => true],
];
```

Create VsPayment Config config/packages/vs_payment.yaml
```yaml
vs_payment:
    resources:
        gateway_config:
            classes:
                model: App\Entity\Payment\GatewayConfig
        payment_method:
            classes:
                model: App\Entity\Payment\PaymentMethod
        payment:
            classes:
                model: App\Entity\Payment\Payment

```

II. Testing Payum Functinalities
---------------------------------
Add Payum Configuration If You Want to Test Something
```yaml
payum:
    storages:
        Payum\Core\Model\Payment:
            filesystem:
                storage_dir: '%kernel.project_dir%/var/payum/payments'
                id_property: number
                
        Payum\Core\Model\ArrayObject:
            filesystem:
                storage_dir: '%kernel.project_dir%/var/payum/payments'
                id_property: number

    security:
        token_storage:
            Payum\Core\Model\Token:
                filesystem:
                    storage_dir: '%kernel.project_dir%/var/payum/gateways'
                    id_property: hash
            
    gateways:
        offline:
            factory: offline
```
