<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

trait ReviewableTrait
{
    /**
     * Add Orm Mapping for this field where is used this trait
     * 
     * @var Collection|ReviewInterface[]
     */
    protected $reviews;
    
    /**
     * @var float
     *
     * @ORM\Column(name="averageRating", column="average_rating", type="float", options={"default":"0"})
     */
    protected $averageRating = 0.0;
    
    /**
     * @return string
     */
    abstract public function getName(): ?string;
    
    /**
     * @return Collection|ReviewInterface[]
     *
     * @psalm-return Collection<array-key, ReviewInterface>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }
    
    public function addReview( ReviewInterface $review ): void
    {
        if ( ! $this->reviews->contains( $review ) ) {
            $this->reviews[] = $review;
        }
    }
    
    public function removeReview( ReviewInterface $review ): void
    {
        if ( $this->reviews->contains( $review ) ) {
            $this->reviews->removeElement( $review );
        }
    }
    
    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }
    
    public function setAverageRating( float $averageRating ): void
    {
        $this->averageRating    = $averageRating;
    }
}