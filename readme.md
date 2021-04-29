# Projet TodoList
## Objectifs
- Installation minimale du package Symfony
- Voir les composants requis au fur et à mesure
- Gestion de données avec système CRUD
- Class Formulaires et contrôles (contraintes)
- Deux entités relation ManyToOne
- Déploiement sur Heroku

# Etape 1
Check system
```bash
symfony check:requirements
```
## Installation de composants
Voir ces adresses:
- https://packagist.org
- https://flex.symfony.com

## Configuration base de données
nom: db_dev_todolist
On a besoin de doctrine = ORM
Voir la doc Symfony/guides/databse doctrine ORM
```bash
# On tape symfony console doctrine et on nous donne 
composer require symfony/orm-pack
# le fichier .env a été modifié
DATABASE_URL="mysql://root:@127.0.0.1:3306/db_dev_todolist"
# puis 
symfony console doctrine:database:create
```

# Entités
# Principe de relation
- Une To Do List appartient à une catégorie
- Une catégorie contient 0 ou plusieurs "to do"

## Entités catégory
Category(name(string))
Todo(title(string), content(text), created_at(datetime), updated_at(datetime), date_for(datetime), #category)
```bash
symfony console make:entity Category
# puis
symfony console make:entity Todo
# puis la relation
symfony console make:entity Todo
# on ajoute le champs category et on choisit le type relation
```

# Migration
```bash
symfony console make:migration
# puis
symfony console doctrine:migrations:migrate
```
# Fixtures
```bash
composer require orm-fixtures --dev
```

# Alimenter les tables
__NB__ : 
- voir comment définir les dates de création et d'update dès la création d'une Todo
- construction de la classe Todo

### Analyse
1. La table Category doit être remplie en premier
- On part d'un tableau de catégorie
- Pour chaque catégorie je veux l'enregistrer dans la table physique
    sous symfony tout passe par l'objet --> voir class Category
2. La table Todo
- On crée un objet Todo
__NB__ : la méthode setCategory() qui a besoin d'un objet Category comme argument

# Controllers
## TestController
L'objectif est de voir le format de rendu qur propose le controller sachant que Twig n'est pas installé
```bash
symfony console make:controller Test
```
## Installer Twig
```bash
composer require twig
```
## TodoController
```bash
symfony console make:controller Todo
# On a une vue créée dans le dossier Template
```
## La page d'accueil des Todos
Le controller va récupérer notre premier enregistrement de la table Todo
et le passer à la vue 'todo/index'

La mise en forme est gérée par des tables Bootstrap
## La page détail (voir)
1. Une méthode et sa route
```php
    # Le repository en injection de dépendance
    public function detail($id, TodoRepository $repo): Response
```

2. une vue dans template Todo
3. Le lien au niveau du bouton voir de la page d'accueil

# Formulaire
## Install
```bash
composer require form validator
```

## generate
## Etape 1
Génération de la classe du nom que vous voulez
```bash
symfony console make:form
# TodoFormType choisit
```
## Etape 2
On crée la méthode 'create()' dans le todoController
On va créer le lien du bouton pour tester le cheminement jusqu'à la vue 'create.html.twig'
## problématique des routes
```bash
# Besoin d'installer le profiler pour débuguer
composer require --dev symfony/profiler-pack
# aussi
symfony console debug:router
```

Voir :
1. la forme des urls. ex: /todo, /todo/1, /todo/1/edit
2. L'ordre de placement influt
3. La possibilité d'ajouter un paramètre "priority" (à lire)


## Etape 3
Gestion du formulaire dans la méthode adéquate du formulaire
Affichage du formulaire dans la vue
### Améliorer le visuel avec Twig
dans config/package/twig.yaml rajouter
```bash
form_themes: ['bootstrap_4_layout.html.twig']
```
### Problématique du champ category
il fait référence à une relation avec une entité
On va ajouter des types à la classe TodoFormType
### Ajouter d'autres types
Voir la doc, plusieurs options concurrentes

## TodoController : edit()
- on installe un bundle dont le rôle est de faire la correspondance entre une url avec l'id d'un objet et l'objet passé en paramètre
```bash
composer req sensio/framework-extra-bundle
```
## TodoController : delete()
## méthode 1
- un lien depuis la page d'accueil
## méthode 2
- un lien dans la page edit
- on a ajouté une confirmation an javascript
__NB__ : Attention à l'emplacement de `{% block javascripts %}`
# Ajouter une navbar
- un fichier _navbar.html.twig avec une navbar bootstrap
    - un titre
    - un bouton accueil
    - un menu déroulant
- inclure dans base.html.twig dans un {% block navbar %} {% endblock %}

# Contrainte de formulaire
## TodoFormType

Voir pour inhiber le controle HTML5

```php
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Todo::class,
            'attr'=> [
                'novalidate'=>'novalidate'
            ]
        ]);
    }
```

Voir les contraintes des champs
Ici, dans le cas ou un champ esy considéré comme nullable = false dans la database

```php
->add('title', TextType::class, [
                'label'=> "Un titre",
                'empty_data' => '',
```

## Dans l'entité Todo

Ne pas oublier d'importer la classe mais pas Mozart/Assert. Copier/coller depuis la doc.

exemple:

```php
# la classe à importer
use Symfony\Component\Validator\Constraints as Assert; 

 /**
     * @Assert\NotBlank(message="Ce champ ne peut être vide.")
     * @Assert\Length(
     *      min= 10,
     *      minMessage = "Au minimum {{ limit }} caractères"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $title;
```

#
# Version de l'appli avec SQLite
## Procedure à suivre :
1. Installer SQLite Studio
2. Définir la connection dans le fichier .env
```bash
DATABASE_URL="sqlite:///%kernel.project_dir%/var/todo.db"
```
3. Créer ce fichier
```bash
symfony console doctrine:database:create
```
4. Créer une migration pour base de données SQLite
```bash
# Effacer les migrations présentent dans le dossier migrations puis effectuer un :
symfony console make:migration
# puis
symfony console doctrine:migrations:migrate
```
5. Fixtures
```bash
symfony console doctrine:fixtures:load
```
6. Tester et voir dans SQLite Studio

#
# PostGreSQL
#
## Procedure d'installation
1. Installer PostGreSQL
2. 
```yaml
url : https://www.enterprisedb.com/downloads/postgres-postgresql-downloads
```
2. DLL dans php.ini

```bash
# 2 extensions a déticker
extension=pgsql
extension=pdo_pgsql
```

3. Installer l'interface PGAdmin
4. Configurer Symfony
```yaml
# dans config/packages/doctrine.yaml , ajouter:
dbal:
    driver: 'pdo_pgsql'
    charset: utf8
```
5. Connexion à PostGreSQL depuis le fichier .env
```bash
DATABASE_URL="postgresql://postgres:1234@127.0.0.1:5432/db_pg_todolist"
```
6. Céer la base de données
```bash
symfony console doctrine:database:create
```
7. Migration
```bash
# Effacer les migrations présentent dans le dossier migrations puis effectuer un :
symfony console make:migration
# puis
symfony console doctrine:migrations:migrate
```
8. Fixtures
```bash
symfony console doctrine:fixtures:load
```
9. Lancer le serveur
```bash
symfony serve
```

## Migrations et fixtures en prod
Aller voir dans config/bundles.php
```php
Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['all' => true],
```
Puis aller dans le fichier composer.json et décaler cette ligne dans "require"
```js
"doctrine/doctrine-fixtures-bundle": "^3.4"
```
Puis on ajoute une structure dans scripts:
```js
    "scripts": {
        "compil":[
            "php bin/console doctrine:migration:migrate",
            "php bin/console doctrine:fixtures:load --no-interraction --env=PROD"
        ],
```


