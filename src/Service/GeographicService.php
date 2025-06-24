<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service pour la gestion géographique
 * 
 * AMÉLIORATIONS APPORTÉES (Yasmina + Assistant) :
 * - Intégration de la base des communes françaises
 * - Recherche géographique avancée
 * - Filtrage par région, département, ville
 * - Autocomplétion des localités
 */
class GeographicService
{
    private array $communes = [];
    private array $departments = [];
    private array $regions = [];

    public function __construct(private ParameterBagInterface $parameterBag)
    {
        $this->loadCommunesData();
    }

    /**
     * Charge les données des communes depuis le fichier CSV
     */
    private function loadCommunesData(): void
    {
        $csvPath = $this->parameterBag->get('kernel.projectDir') . '/communes-francaises.csv';

        if (!file_exists($csvPath)) {
            return;
        }

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            return;
        }

        // Ignorer l'en-tête
        fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) >= 4) {
                $commune = [
                    'name' => $data[0],
                    'department_code' => $data[1],
                    'department_name' => $data[2],
                    'region' => $data[3]
                ];

                $this->communes[] = $commune;

                // Indexer les départements
                if (!isset($this->departments[$data[1]])) {
                    $this->departments[$data[1]] = [
                        'code' => $data[1],
                        'name' => $data[2],
                        'region' => $data[3]
                    ];
                }

                // Indexer les régions
                if (!isset($this->regions[$data[3]])) {
                    $this->regions[$data[3]] = $data[3];
                }
            }
        }

        fclose($handle);
    }

    /**
     * Recherche des communes par nom
     */
    public function searchCommunes(string $query, int $limit = 10): array
    {
        $results = [];
        $query = strtolower($query);

        foreach ($this->communes as $commune) {
            if (strpos(strtolower($commune['name']), $query) !== false) {
                $results[] = $commune;
                if (count($results) >= $limit) {
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * Récupère tous les départements
     */
    public function getDepartments(): array
    {
        return array_values($this->departments);
    }

    /**
     * Récupère toutes les régions
     */
    public function getRegions(): array
    {
        return array_values($this->regions);
    }

    /**
     * Récupère les communes d'un département
     */
    public function getCommunesByDepartment(string $departmentCode): array
    {
        return array_filter($this->communes, function ($commune) use ($departmentCode) {
            return $commune['department_code'] === $departmentCode;
        });
    }

    /**
     * Récupère les communes d'une région
     */
    public function getCommunesByRegion(string $region): array
    {
        return array_filter($this->communes, function ($commune) use ($region) {
            return $commune['region'] === $region;
        });
    }

    /**
     * Recherche géographique avancée
     */
    public function advancedGeographicSearch(string $query): array
    {
        $results = [
            'communes' => [],
            'departments' => [],
            'regions' => []
        ];

        $query = strtolower($query);

        // Recherche dans les communes
        foreach ($this->communes as $commune) {
            if (strpos(strtolower($commune['name']), $query) !== false) {
                $results['communes'][] = $commune;
            }
        }

        // Recherche dans les départements
        foreach ($this->departments as $dept) {
            if (
                strpos(strtolower($dept['name']), $query) !== false ||
                strpos(strtolower($dept['code']), $query) !== false
            ) {
                $results['departments'][] = $dept;
            }
        }

        // Recherche dans les régions
        foreach ($this->regions as $region) {
            if (strpos(strtolower($region), $query) !== false) {
                $results['regions'][] = $region;
            }
        }

        return $results;
    }
}
