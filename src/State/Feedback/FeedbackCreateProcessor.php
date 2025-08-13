<?php

namespace App\State\Feedback;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Feedback;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


final class FeedbackCreateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Feedback) {
            $user = $this->security->getUser();
            if (!$user) {
                throw new \RuntimeException('Authentication required');
            }

            $data->setUser($user);

            if (method_exists($user, 'getEmail')) {
                $data->setEmail((string) $user->getEmail());
            }

            // 🟢 createdAt par défaut si manquant
            if (null === $data->getCreatedAt()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }

            // status par défaut
            $data->setStatus($data->getStatus() ?: 'new');
        }


        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
