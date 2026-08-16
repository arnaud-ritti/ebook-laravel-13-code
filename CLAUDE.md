# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Nature du dépôt

Application **Solvia**, SaaS industriel multi-tenant de facturation. C'est le projet fil rouge de l'ebook *L'Architecture Laravel Moderne : Concevoir des applications enterprise robustes avec Filament v5 et FrankenPHP*.

Ce n'est pas une application produit. Chaque ligne existe pour illustrer un point pédagogique et doit rester lisible isolément, par un développeur PHP senior qui découvre Laravel.

## Lire d'abord la charte

La spécification faisant autorité vit dans le dépôt manuscrit adjacent : `../ebook-laravel-13/CLAUDE.md`. La lire avant d'écrire du code ici. Elle contient la stack verrouillée, les six décisions de cadrage, le tableau des API inexistantes à ne pas régénérer, et le plan des 17 modules.

Les contraintes qui pèsent le plus sur le code :

**Agnosticisme SGBD total.** MySQL, MariaDB et PostgreSQL, prouvé par matrice CI. Donc pas de `DB::raw()` hors adaptateurs isolés, ULID en `char(26)` et non en type `uuid` natif, `decimal(19,4)` avec value object `Money`, numérotation légale par table dédiée plus `lockForUpdate()` et non par séquence native, pas de requêtes JSON.

**Multi-tenant par discriminateur.** `tenant_id` plus global scopes. Le schema-per-tenant est exclu par l'agnosticisme.

**Runtime différé.** FrankenPHP en mode classique jusqu'au Module 7, Octane activé seulement à partir de là. Le code des modules 1 à 6 doit être littéralement correct en shared-nothing.

**Domaine isolé.** Frontière domaine, application, infrastructure posée au Module 5. Filament est un adaptateur d'entrée, jamais le cœur.

**Octane sur FrankenPHP.** `Octane::cache()`, `Octane::table()`, `Octane::concurrently()` et les ticks sont réservés à Swoole. Ne pas les utiliser.

## Historique et tags

`main` linéaire, un commit curaté par module, tags annotés `module-01` à `module-17`. Les tags sont la source de vérité : le manuscrit y référence des chemins de fichiers via des marqueurs `// file: chemin @module-XX`, et un script de vérification compare les deux. Toute divergence casse le build.

Le SaaS s'écrit d'abord en entier, l'historique est reconstruit ensuite. Jamais réécrit après publication. Les branches sont générées par script depuis les tags.

## État du dépôt

Vide. Aucun commit, aucun fichier hors ce CLAUDE.md, aucun tag. Documenter ici les commandes de build, de lint et de test dès leur mise en place, en incluant l'invocation d'un test unique et la matrice CI trois SGBD.
