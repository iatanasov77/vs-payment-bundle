<?php namespace Vankosoft\PaymentBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use App\Entity\ClientIp;
use App\Entity\ClientPayment;

class PaymentRepository extends EntityRepository
{
    public function filterPayments( array $filter ): QueryBuilder
    {
        $qb = $this->createQueryBuilder( 'p' )
            ->orderBy( 'p.updatedAt', 'DESC' )
        ;
        
        foreach ( $filter as $key => $value ) {
            if ( $value === null ) {
                continue;
            }
            
            switch ( $key ) {
                case 'number':
                    $qb->andWhere( 'p.number LIKE :number' )->setParameter( 'number', "%{$value}%" );
                    break;
            }
            
        }
            
        return $qb;
    }
}