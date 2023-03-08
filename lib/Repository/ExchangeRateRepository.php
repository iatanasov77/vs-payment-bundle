<?php namespace Vankosoft\PaymentBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

class ExchangeRateRepository extends EntityRepository implements ExchangeRateRepositoryInterface
{
    /**
     * @throws NonUniqueResultException
     */
    public function findOneWithCurrencyPair( string $firstCurrencyCode, string $secondCurrencyCode ): ?ExchangeRateInterface
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();
        
        return $this->createQueryBuilder( 'o' )
            ->addSelect( 'sourceCurrency' )
            ->addSelect( 'targetCurrency' )
            ->innerJoin( 'o.sourceCurrency', 'sourceCurrency' )
            ->innerJoin( 'o.targetCurrency', 'targetCurrency' )
            ->andWhere( $expr->orX(
                'sourceCurrency.code = :firstCurrency AND targetCurrency.code = :secondCurrency',
                'targetCurrency.code = :firstCurrency AND sourceCurrency.code = :secondCurrency'
            ))
            ->setParameter( 'firstCurrency', $firstCurrencyCode )
            ->setParameter( 'secondCurrency', $secondCurrencyCode )
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
