<?php

namespace App\Services\Implementation;

use App\Http\Resources\Contribution\ContributionCategoryResource;
use App\Http\Resources\Contribution\ContributionResource;
use App\Http\Resources\Contribution\GetContributionsResource;
use App\Model\ContributionCategory;
use App\Repositories\Implementation\ContributionRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\ContributionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContributionServiceImpl implements ContributionService
{
    protected $contributionRepository;
    protected $userRepository;

    /**
     * ContributionServiceImpl constructor.
     *
     * @param ContributionRepositoryImpl $contributionRepository
     * @param UserRepositoryImpl         $userRepository
     */
    public function __construct(
        ContributionRepositoryImpl $contributionRepository,
        UserRepositoryImpl $userRepository
    ) {
        $this->contributionRepository = $contributionRepository;
        $this->userRepository = $userRepository;
    }

    public function createCategory(int $lanId, string $name): ContributionCategory
    {
        // Créer la catégorie
        $contributionId = $this->contributionRepository->createCategory($lanId, $name);

        // Retourner la catégorie créée
        return $this->contributionRepository->findCategoryById($contributionId);
    }

    public function createContribution(
        int $contributionCategoryId,
        ?string $userFullName,
        ?string $email
    ): ContributionResource {
        $contributionId = null;

        // Si c'est le nom complet du contributeur qui est utilisé
        if (!is_null($userFullName)) {
            // Créer la contribution avec le nom complet du contributeur
            $contributionId = $this->contributionRepository->createContributionUserFullName($userFullName, $contributionCategoryId);
        } // Si c'est le courriel du contributeur qui est utilisé
        else {
            // Trouver l'utilisateur correspondant au courriel
            $user = $this->userRepository->findByEmail($email);

            // Créer la contribution avec l'id de utilisateur du courriel
            $contributionId = $this->contributionRepository->createContributionUserId($user->id, $contributionCategoryId);
        }

        // Trouver la contribution créée
        $contribution = $this->contributionRepository->findContributionById($contributionId);

        // Retourner l'id et le nom complet de la contribution
        return new ContributionResource($contribution);
    }

    public function deleteCategory(int $contributionCategoryId): ContributionCategory
    {
        // Trouver la catégorie
        $contributionCategory = $this->contributionRepository->findCategoryById($contributionCategoryId);

        // Supprimer la catégorie
        $this->contributionRepository->deleteCategoryById($contributionCategoryId);

        // Retourner la catégorie supprimée
        return $contributionCategory;
    }

    public function deleteContribution(int $contributionId): ContributionResource
    {
        // Trouver la contribution
        $contribution = $this->contributionRepository->findContributionById($contributionId);

        // Supprimer la contribution
        $this->contributionRepository->deleteContributionById($contributionId);

        // Retourner la contribution supprimée
        return new ContributionResource($contribution);
    }

    public function getCategories(int $lanId): AnonymousResourceCollection
    {
        return ContributionCategoryResource::collection(
            $this->contributionRepository->getCategories($lanId)
        );
    }

    public function getContributions(int $lanId): AnonymousResourceCollection
    {
        return GetContributionsResource::collection(
            $this->contributionRepository->getCategories($lanId)
        );
    }
}
