<?php namespace Vankosoft\PaymentBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

class PromotionRepository extends EntityRepository implements PromotionRepositoryInterface
{
    public function findActive(): array
    {
        return $this->filterByActive( $this->createQueryBuilder( 'o' ) )
            ->addOrderBy( 'o.priority', 'desc' )
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByName( string $name ): array
    {
        return $this->findBy( ['name' => $name] );
    }
    
    protected function filterByActive( QueryBuilder $queryBuilder, ?\DateTimeInterface $date = null ): QueryBuilder
    {
        return $queryBuilder
            ->andWhere( 'o.startsAt IS NULL OR o.startsAt < :date' )
            ->andWhere( 'o.endsAt IS NULL OR o.endsAt > :date' )
            ->setParameter( 'date', $date ?: new \DateTime() )
        ;
    }
}