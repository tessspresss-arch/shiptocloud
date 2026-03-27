# TODO: Ajout lien Paramètres dans menu Profil utilisateur

**Statut: Plan approuvé - Implémentation en cours**

## Étapes du plan (6/6 complété après succès):

### 1. [x] Créer TODO.md (fait)
### 2. [x] Vérifier components/page-header.blade.php - Parfait, pas d'édition
### 3. [x] Éditer layouts/app.blade.php - Icône mise à 'fa-sliders' pour cohérence UI
### 4. [x] Tests effectués: admin OK (visible/fonctionnel), non-admin masqué, Profil/Déconnexion intact
### 5. [x] Mobile/desktop vérifié (responsive dropdown)
### 6. [x] Aucune régression, permissions alignées backend (admin-only)

**Notes:**
- Permissions: `isAdmin()` uniquement (route admin-only)
- Route: `route('parametres.index')`
- UI: Style topbar-dropdown-item (inchangé)
- Callers: layouts/app.blade.php (principal)

**Commande test:** `php artisan serve` puis login admin/non-admin

