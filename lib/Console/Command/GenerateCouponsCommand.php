<?php namespace Vankosoft\PaymentBundle\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

#[AsCommand(
    name: 'vankosoft:promotion:generate-coupons',
    description: 'Generates coupons for a given promotion',
    hidden: false
)]
final class GenerateCouponsCommand extends Command
{
    /** @var PromotionRepositoryInterface */
    private $promotionRepository;
    
    /** @var PromotionCouponGeneratorInterface */
    private $couponGenerator;
    
    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        PromotionCouponGeneratorInterface $couponGenerator,
    ) {
        parent::__construct();
        
        $this->promotionRepository  = $promotionRepository;
        $this->couponGenerator      = $couponGenerator;
    }
    
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
'The <info>%command.name%</info Generates coupons for a given promotion.'
EOT
            )
            ->addArgument( 'promotion-code', InputArgument::REQUIRED, 'Code of the promotion' )
            ->addArgument( 'count', InputArgument::REQUIRED, 'Amount of coupons to generate' )
            ->addOption( 'length', 'len', InputOption::VALUE_OPTIONAL, 'Length of the coupon code (default 10)', '10' )
        ;
    }
    
    public function execute( InputInterface $input, OutputInterface $output ): int
    {
        /** @var string $promotionCode */
        $promotionCode = $input->getArgument( 'promotion-code' );
        
        /** @var PromotionInterface|null $promotion */
        $promotion = $this->promotionRepository->findOneBy( ['code' => $promotionCode] );
        
        if ( $promotion === null ) {
            $output->writeln( '<error>No promotion found with this code</error>' );
            
            return Command::FAILURE;
        }
        
        if ( ! $promotion->isCouponBased() ) {
            $output->writeln('<error>This promotion is not coupon based</error>');
            
            return Command::FAILURE;
        }
        
        $instruction = $this->getGeneratorInstructions(
            (int) $input->getArgument( 'count' ),
            (int) $input->getOption( 'length' ),
        );
        
        try {
            $this->couponGenerator->generate( $promotion, $instruction );
        } catch ( \Exception $exception ) {
            $output->writeln( '<error>' . $exception->getMessage() . '</error>' );
            
            return Command::FAILURE;
        }
        
        $output->writeln( '<info>Coupons have been generated</info>' );
        
        return Command::SUCCESS;
    }
    
    public function getGeneratorInstructions( int $count, int $codeLength ): ReadablePromotionCouponGeneratorInstructionInterface
    {
        return new PromotionCouponGeneratorInstruction(
            amount: $count,
            codeLength: $codeLength,
        );
    }
}
