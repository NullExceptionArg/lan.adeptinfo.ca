<?php

namespace App\Services;

use App\Http\Resources\Contribution\ContributionResource;
use App\Model\ContributionCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Méthodes pour exécuter la logique d'affaire des contributions.
 *
 * Interface ContributionService
 */
interface ContributionService
{
    /**
     * Créer une catégorie de contribution.
     *
     * @param int    $lanId Id du LAN de la catégorie
     * @param string $name  Nom de la catégorie
     *
     * @return ContributionCategory Catégorie de contribution créée
     */
    public function createCategory(int $lanId, string $name): ContributionCategory;

    /**
     * Créer une contribution.
     *
     * @param int         $contributionCategoryId Id de la catégorie de la contribution
     * @param string|null $userFullName           Nom complet de l'utilisateur de la contribution
     * @param string|null $email                  Courriel de l'utilisateur de la contribution
     *
     * @return ContributionResource Contribution créée
     */
    public function createContribution(
        int $contributionCategoryId,
        ?string $userFullName,
        ?string $email
    ): ContributionResource;

    /**
     * Supprimer une catégorie de contribution.
     *
     * @param int $contributionCategoryId Id de la catégorie
     *
     * @return ContributionCategory Catégorie supprimée
     */
    public function deleteCategory(int $contributionCategoryId): ContributionCategory;

    /**
     * Supprimer une contribution.
     *
     * @param int $contributionId Id de la contribution
     *
     * @return ContributionResource Contribution supprimée
     */
    public function deleteContribution(int $contributionId): ContributionResource;

    /**
     * Obtenir les catégories de contribution d'un LAN.
     *
     * @param int $lanId Id du LAN
     *
     * @return AnonymousResourceCollection Catégories de contribution du LAN
     */
    public function getCategories(int $lanId): AnonymousResourceCollection;

    /**
     * Obtenir les contributions d'un LAN.
     *
     * @param int $lanId Id du LAN
     *
     * @return AnonymousResourceCollection Contributions du LAN
     */
    public function getContributions(int $lanId): AnonymousResourceCollection;
}
