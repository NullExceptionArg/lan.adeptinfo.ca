<?php

namespace App\Repositories;

use App\Model\{Contribution, ContributionCategory};
use Illuminate\Support\Collection;

/**
 * Méthodes pour accéder aux tables de base de donnée liées aux contributions.
 *
 * Interface ContributionRepository
 * @package App\Repositories
 */
interface ContributionRepository
{
    /**
     * Créer une catégorie de contribution.
     *
     * @param int $lanId Id du LAN dans lequel la catégorie existe.
     * @param string $name Nom de la catégorie de contribution.
     * @return int Id de la catégorie de contribution créée.
     */
    public function createCategory(int $lanId, string $name): int;

    /**
     * Créer une contribution à partir du nom du contributeur.
     *
     * @param string $userFullName Nom complet du contributeur.
     * @return int Id de la contribution créée.
     * @param int $contributionCategoryId Id de la catégorie de la contribution
     */
    public function createContributionUserFullName(string $userFullName, int $contributionCategoryId): int;

    /**
     * Créer une contribution à partir d'un utilisateur existant dans l'application.
     *
     * @param int $userId Id du contributeur.
     * @return int Id de la contribution créée.
     * @param int $contributionCategoryId Id de la catégorie de contribution
     */
    public function createContributionUserId(int $userId, int $contributionCategoryId): int;

    /**
     * Supprimer une catégorie de contribution.
     *
     * @param int $contributionCategoryId Id de la catégorie de contribution à supprimer.
     */
    public function deleteCategoryById(int $contributionCategoryId): void;

    /**
     * Supprimer une contribution.
     *
     * @param int $contributionId Id de la contribution à supprimer.
     */
    public function deleteContributionById(int $contributionId): void;

    /**
     * Trouver une catégorie de contribution.
     *
     * @param int $categoryId Id de la catégorie à trouver.
     * @return ContributionCategory|null Catégorie trouvée, ou null rien n'a pas été trouvée.
     */
    public function findCategoryById(int $categoryId): ?ContributionCategory;

    /**
     * Trouver une contribution.
     *
     * @param int $contributionId Id de la contribution à trouver.
     * @return Contribution|null Contribution trouvée, ou null si rien n'a été trouvé.
     */
    public function findContributionById(int $contributionId): ?Contribution;

    /**
     * Obtenir les catégories de contribution d'un LAN.
     *
     * @param int $lanId Id du LAN pour lequel on veut obtenir les contribution.
     * @return Collection Catégories trouvées.
     */
    public function getCategories(int $lanId): Collection;
}
