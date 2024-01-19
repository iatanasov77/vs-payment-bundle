<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\ContentServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Vankosoft\PaymentBundle\Model\Traits\ReviewableTrait;
use Vankosoft\PaymentBundle\Model\Traits\CommentableTrait;

/**
 * Base Model for Catalog Services
 */
class ContentService implements ContentServiceInterface
{
    use TimestampableTrait;
    use ToggleableTrait;    // About enabled field - $enabled (published)
    use TranslatableTrait;
    use ReviewableTrait;
    use CommentableTrait;
    
    /** @var int */
    protected $id;
    
    /** @var string */
    protected $locale;
    
    /** @var string */
    protected $slug;
    
    /** @var string */
    protected $title;
    
    /** @var string */
    protected $description;
    
    /** @var bool */
    protected $enabled = true;
    
    public function __construct()
    {
        $this->reviews  = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPublished(): ?bool
    {
        return $this->enabled;
    }
    
    public function setPublished( ?bool $published ): self
    {
        $this->enabled = (bool) $published;
        return $this;
    }
    
    public function isPublic(): bool
    {
        return $this->enabled;
    }
    
    public function isPublished(): bool
    {
        return $this->enabled;
    }
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    
    public function setSlug($slug): self
    {
        $this->slug = $slug;
        
        return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title   = $title;
        
        return $this;
    }
    
    /**
     * Implement Abstract Method in ReviewableTrait
     *
     * @return string|NULL
     */
    public function getName(): ?string
    {
        return $this->title;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description  = $description;
        
        return $this;
    }
    
    public function getLocale()
    {
        return $this->currentLocale;
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): self
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    protected function createTranslation(): TranslationInterface
    {
        
    }
}