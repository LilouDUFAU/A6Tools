# Module gestcommande

## Description
Gestion des commandes clients et fournisseurs : création, édition, suppression, suivi des états et alertes.

## Fonctionnalités
- Créer, modifier, supprimer une commande
- Associer clients, fournisseurs, produits
- Suivi des états (en attente, validée, livrée, etc.)
- Filtres par site, état, urgence
- Alertes sur délais de livraison

## Structure
- `index.blade.php` : Liste et tableau de bord des commandes
- `create.blade.php` : Formulaire de création
- `edit.blade.php` : Formulaire de modification
- `show.blade.php` : Détail d’une commande

## Utilisation
1. Accéder au menu "Commandes"
2. Créer ou modifier une commande
3. Suivre l’état via le tableau de bord

## Prérequis
- Base de données configurée (voir `.env`)
- Authentification requise