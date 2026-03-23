<?php

namespace App\View\Composers;

use App\Models\Patient;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $menuItems = $this->getMenuItems();
        $patientCount = Cache::remember('sidebar.patient_count', now()->addMinutes(5), fn () => Patient::count());

        $view->with('menuItems', $menuItems);
        $view->with('patientCount', $patientCount);
    }

    /**
     * Get the flat menu structure with submenus.
     */
    private function getMenuItems(): array
    {
        $items = [
            [
                'id' => 'dashboard',
                'label' => 'Tableau de bord',
                'icon' => 'tachometer-alt',
                'has_submenu' => false,
                'route' => 'dashboard',
                'badge' => null,
            ],
            [
                'id' => 'patients',
                'icon' => 'users',
                'label' => 'Patients',
                'has_submenu' => true,
                'badge' => 1,
                'submenu' => [
                    ['route' => 'patients.index', 'label' => 'Liste des patients'],
                    ['route' => 'patients.create', 'label' => 'Nouveau patient'],
                    ['route' => 'dossiers.index', 'label' => 'Dossiers médicaux'],
                    ['route' => 'dossiers.archives', 'label' => 'Archives dossiers'],
                ],
            ],
            [
                'id' => 'consultations',
                'icon' => 'stethoscope',
                'label' => 'Consultations',
                'route' => 'consultations.index',
                'has_submenu' => false,
                'badge' => null,
            ],
            [
                'id' => 'planning',
                'icon' => 'calendar-alt',
                'label' => 'Planning',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'agenda.index', 'label' => 'Agenda'],
                    ['route' => 'agenda.waiting_room', 'label' => 'Salle d\'attente'],
                    ['route' => 'rendezvous.index', 'label' => 'Rendez-vous'],
                    ['route' => 'agenda.index', 'label' => 'Planning médecins'],
                ],
            ],
            [
                'id' => 'medecins',
                'icon' => 'user-md',
                'label' => 'Médecins',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'medecins.index', 'label' => 'Liste des médecins'],
                    ['route' => 'medecins.create', 'label' => 'Nouveau médecin'],
                ],
            ],
            [
                'id' => 'pharmacie',
                'icon' => 'pills',
                'label' => 'Pharmacie',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'ordonnances.index', 'label' => 'Ordonnances'],
                    ['route' => 'medicaments.index', 'label' => 'Médicaments'],
                ],
            ],
            [
                'id' => 'facturation',
                'icon' => 'file-invoice-dollar',
                'label' => 'Facturation',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'factures.index', 'label' => 'Factures'],
                    ['route' => 'paiements.index', 'label' => 'Paiements'],
                ],
            ],
            [
                'id' => 'examens',
                'icon' => 'flask',
                'label' => 'Bilans complémentaires',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'examens.index', 'label' => 'Liste des examens'],
                    ['route' => 'examens.create', 'label' => 'Nouvel examen'],
                    ['route' => 'examens.results', 'label' => 'Résultats'],
                ],
            ],
            [
                'id' => 'depenses',
                'icon' => 'money-bill-wave',
                'label' => 'Dépenses',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'depenses.index', 'label' => 'Liste des dépenses'],
                    ['route' => 'depenses.create', 'label' => 'Nouvelle dépense'],
                    ['route' => 'depenses.statistiques', 'label' => 'Statistiques'],
                ],
            ],
            [
                'id' => 'contacts',
                'icon' => 'address-book',
                'label' => 'Contacts',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'contacts.index', 'label' => 'Liste des contacts'],
                    ['route' => 'contacts.create', 'label' => 'Nouveau contact'],
                    ['route' => 'contacts.export', 'label' => 'Exporter'],
                ],
            ],
            [
                'id' => 'sms',
                'icon' => 'sms',
                'label' => 'Rappels SMS',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'sms.index', 'label' => 'Rappels planifiés'],
                    ['route' => 'sms.create', 'label' => 'Nouveau rappel'],
                    ['route' => 'sms.logs', 'label' => 'Historique'],
                ],
            ],
            [
                'id' => 'documents',
                'icon' => 'folder-open',
                'label' => 'Documents',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'documents.index', 'label' => 'Mes documents'],
                    ['route' => 'documents.upload', 'label' => 'Téléverser'],
                    ['route' => 'documents.categories', 'label' => 'Catégories'],
                ],
            ],
            [
                'id' => 'statistiques',
                'icon' => 'chart-bar',
                'label' => 'Statistiques',
                'route' => 'statistiques',
                'has_submenu' => false,
                'badge' => null,
            ],
            [
                'id' => 'rapports',
                'icon' => 'file-alt',
                'label' => 'Rapports',
                'route' => 'rapports.index',
                'has_submenu' => false,
                'badge' => null,
            ],
            [
                'id' => 'parametres',
                'icon' => 'cog',
                'label' => 'Paramètres',
                'has_submenu' => true,
                'submenu' => [
                    ['route' => 'parametres.index', 'label' => 'Paramètres (V1)'],
                    ['route' => 'admin.settings.index', 'label' => 'Centre de gouvernance (V2)'],
                    ['route' => 'admin.settings.audit', 'label' => 'Audit Log (V2)'],
                ],
                'badge' => null,
            ],
            [
                'id' => 'utilisateurs',
                'icon' => 'user-shield',
                'label' => 'Utilisateurs',
                'has_submenu' => true,
                'badge' => null,
                'submenu' => [
                    ['route' => 'utilisateurs.index', 'label' => 'Liste des utilisateurs'],
                    ['route' => 'utilisateurs.create', 'label' => 'Nouvel utilisateur'],
                ],
            ],
        ];

        $user = auth()->user();
        if (!$user) {
            return $items;
        }

        return array_values(array_filter($items, function (array $item) use ($user) {
            if (in_array(($item['id'] ?? null), ['parametres', 'utilisateurs'], true) && !$user->isAdmin()) {
                return false;
            }

            return $user->hasModuleAccess($item['id'] ?? '');
        }));
    }
}
