# Comment contribuer

Pour contribuer au projet, clonez localement le projet et installez les dépendances. Pour plus de détail, se référer au fichier
README.md.

## Procédures

### Issue

Commencez par ouvrir une issue en vérifiant qu'il n'y en a pas déjà une. 

### Branche

Créez ensuite une branche et commencez le développement. Ne travaillez en aucun cas sur la branche main !

### Tester

Veuillez créer des tests pour toutes les fonctionnalités que vous développez et vérifiez que la couverture de code ne tombe pas en dessous de 70%. Référez-vous au fichier README.md pour plus de détail. Pour plus d'information sur les teste dans un projet Symfony, référez-vous à la documentation : [Testing](https://symfony.com/doc/current/testing.html);

Si votre test nécessité l'utilisation d'une base de données, vous pouvez utiliser le trait **FixtureTrait** puis, appelez dans votre test la méthode **makeFixture**.

```php
$client = static::createClient();
$this->makeFixture();
```

N'hésitez pas à aller lire le code des autres classes de test pour vous en inspirer dans la rédaction de vos tests.

### Pull Request

Une fois votre ajout terminé, ouvrez une pull request et attendez qu'elle soit validée.

## Qualité de code

Veuillez à utiliser les standards de code de Symfony, puisque nous travaillons sur un projet Symfony. Référez-vous à la documentation pour plus d'informations : [Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html). Des outils permettent de facilité le respect des standards, comme **PHP CS Fixer**.

Vous vous aidez, on a installé dans le projet phpstan et PhpMetrics qui vous permettront de voir la qualité du code et sa complexité.

```bash
vendor/bin/phpstan analyse src tests
phpmetrics --report-html="./var/report/" ./src
```

## Standard pour les messages de commit

Il y a quelques règles à respecter pour les messages de commit. Faites précéder votre message d'un préfixe :

* add: vous ajoutez une fonctionnalité
* fix: vous corrigez un bug
* change: vous modifiez une fonctionnalité existante
* remove: vous enlevez une fonctionnalité
