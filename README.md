Exercice 1 : Développement d'une Application de Gestion des Inscriptions au Permis de Conduire
Contexte : Vous travaillez pour une auto-école et on vous a confié la tâche de développer une application web en utilisant Symfony pour gérer les inscriptions au permis de conduire. Cette application doit permettre aux étudiants de s'inscrire, de réserver des heures de conduite, de passer des tests pratiques et de payer pour les services. Vous devez également mettre en place un système d'authentification pour les étudiants et les instructeurs.

## Gestion

- les instructeur sont ajouter via base de données directement ou bien d'aprés l'interface
- Inscription étudiants            = FULLY_AUTH
- réserver des heures de conduite  = ROLE_ETUDIANT
- historique de réservation        = ROLE_ADMIN = ROLE_INSTRUCTEUR
- historique des étudiants         = ROLE_ADMIN = ROLE_INSTRUCTEUR
- historique des instructeur       = ROLE_ADMIN = ROLE_INSTRUCTEUR

## Tâches :
● Configuration de l'environnement Symfony:
Configurezunprojet Symfony avec la structure de base.

● Créationdel'EntitéÉtudiant:CréezuneentitéSymfonypour
représenter les étudiants. L'entité doit inclure des champs tels que le
nom, le prénom, l'adresse e-mail, le numéro de téléphone, etc.

● Créationdel'EntitéInstructeur:CréezuneentitéSymfonypour

● représenter les instructeurs. L'entité doit inclure des informations
telles que le nom, l'adresse e-mail, le numéro de téléphone, etc.

● Gestiondel'Authentification:Mettezenplaceunsystème
d'authentification pour les étudiants et les instructeurs. Vous pouvez utiliser FOSUserBundle ou une autre bibliothèque d'authentification Symfony.
● Créationdel'EntitéRéservation:CréezuneentitéSymfonypour gérer les réservations des étudiants pour les heures de conduite. L'entité doit inclure des informations sur la date, l'heure, l'étudiant associé, l'instructeur assigné, etc.
● MiseenPlaceduPaiement:Intégrezunsystèmedepaiementpour permettre aux étudiants de payer pour leurs réservations. Vous pouvez utiliser Stripe, PayPal ou une autre solution de paiement en ligne.
● VueFront-End:Créezdesvuesfront-endpourpermettreaux étudiants de s'inscrire, de réserver des heures de conduite et de voir leur historique de réservation. Assurez-vous que l'interface utilisateur est conviviale et réactive.
● TestsUnitaires:Écrivezdestestsunitairespourvaliderle fonctionnement de votre application. Testez les cas d'utilisation principaux, y compris l'inscription, la réservation et le paiement.
Instructions pour les candidats :
● VousdevezcréerunprojetSymfonycompletaveclastructure nécessaire pour répondre à ces exigences.
● unlienversleurprojetdéployéenligne
● Assurez-vousdedocumentervotrecodeetdefournirdesinstructions
sur la façon de déployer et de tester l'application.
● Présentezvotreprojet(interfaceutilisateur+codesource)commesi
vous le montriez à un client lors de l'entretien technique, en expliquant les fonctionnalités et les choix de conception que vous avez faits.


composer require symfony/maker-bundle --dev
composer require orm --dev

symfony console d:d:c   

composer require friendsofsymfony/user-bundle **deprecated from symfony 4**

symfony console make:entity

symfony console make:migration

symfony console cache:clear

symfony console doctrine:schema:update --force

symfony console fos:user:create

# Création de projet

symfony new auto-ecole --version=5.4

## 1 - Authentification

https://symfony.com/doc/5.4/security.html

1 - composer require symfony/security-bundle
2 - symfony console make:user
    The name of the security user class (e.g. User) [User]:
    > User

    Do you want to store user data in the database (via Doctrine)? (yes/no) [yes]:
    > yes

    Enter a property name that will be the unique "display" name for the user (e.g. email, username, uuid) [email]:
    > email

    Will this app need to hash/check user passwords? Choose No if passwords are not needed or will be checked/hashed by some other system (e.g. a single sign-on server).

    Does this app need to hash/check user passwords? (yes/no) [yes]:
    > yes
3 - User example
4 - security.yaml
security:
    enable_authenticator_manager: true
    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

# 2 - Register registration the User: Hashing Passwords

https://symfony.com/doc/5.4/security.html#registering-the-user-hashing-passwords

composer require symfonycasts/verify-email-bundle
composer require form validator twig-bundle                                                                    
symfony console make:registration-form
Next:
 1) Install some missing packages:
      composer require symfony/mailer
 2) In RegistrationController::verifyUserEmail():
    * Customize the last redirectToRoute() after a successful email verification.
    * Make sure you're rendering success flash messages or change the $this->addFlash() line.
 3) Review and customize the form, controller, and templates as needed.
 4) Run "php bin/console make:migration" to generate a migration for the newly added User::isVerified property.php

You can also manually hash a password by running:
php bin/console security:hash-password

# If you do not see the toolbar, install the profiler with:

composer require --dev symfony/profiler-pack

# Form Login
php bin/console make:controller Login

# ApiLogin
php bin/console make:controller --no-template ApiLogin

# Fetching the User Object 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    public function index(): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
        return new Response('Well hi there '.$user->getFirstName());
    }
}

# Fetching the User from a Service


// src/Service/ExampleService.php
// ...

use Symfony\Component\Security\Core\Security;

class ExampleService
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    public function someMethod()
    {
        // returns User object or null if not authenticated
        $user = $this->security->getUser();

        // ...
    }
}

# Fetch the User in a Template 

{% if is_granted('IS_AUTHENTICATED_FULLY') %}
    <p>Email: {{ app.user.email }}</p>
{% endif %}

# Hierarchical Roles 

BAD - $user->getRoles() will not know about the role hierarchy
$hasAccess = in_array('ROLE_ADMIN', $user->getRoles());

// GOOD - use of the normal security methods
 $hasAccess = $this->isGranted('ROLE_ADMIN');
 $this->denyAccessUnlessGranted('ROLE_ADMIN') 

# Securing URL patterns (access_control)


# config/packages/security.yaml
security:
    access_control:
        # require ROLE_ADMIN for /admin*
        - { path: '^/admin', roles: ROLE_ADMIN }

        # or require ROLE_ADMIN or IS_AUTHENTICATED_FULLY for /admin*
        - { path: '^/admin', roles: [IS_AUTHENTICATED_FULLY, ROLE_ADMIN] }

        # the 'path' value can be any valid regular expression
        # (this one will match URLs like /api/post/7298 and /api/comment/528491)
        - { path: ^/api/(post|comment)/\d+$, roles: ROLE_USER }


# Securing Controllers and other Code

// src/Controller/AdminController.php
// ...

public function adminDashboard(): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    // or add an optional message - seen by developers
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
}

## OR 

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * Require ROLE_SUPER_ADMIN only for this action
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function adminDashboard(): Response
    {
        // ...
    }
}

# Allowing Unsecured Access (i.e. Anonymous Users)
# config/packages/security.yaml
security:
    enable_authenticator_manager: true

    # ...
    access_control:
        # allow unauthenticated users to access the login form
        - { path: ^/admin/login, roles: PUBLIC_ACCESS }

        # but require authentication for all other admin routes
        - { path: ^/admin, roles: ROLE_ADMIN }


## Reset Password

https://symfony.com/doc/5.2/security/reset_password.html

Using MakerBundle and SymfonyCastsResetPasswordBundle, you can create a secure out of the box solution to handle forgotten passwords. First, install the SymfonyCastsResetPasswordBundle:

`composer require symfonycasts/reset-password-bundle`

Then, use the make:reset-password command. This asks you a few questions about your app and generates all the files you need! After, you'll see a success message and a list of any other steps you need to do.

`php bin/console make:reset-password`

 Next:
   1) Run "php bin/console make:migration" to generate a migration for the new "App\Entity\ResetPasswordRequest" entity.
   2) Review forms in "src/Form" to customize validation and labels.
   3) Review and customize the templates in `templates/reset_password`.
   4) Make sure your MAILER_DSN env var has the correct settings.
   5) Create a "forgot your password link" to the app_forgot_password_request route on your login form.

# Linking to CSS, JavaScript and Image Assets

https://symfony.com/doc/5.4/templates.html#linking-to-css-javascript-and-image-assets

composer require symfony/asset

{# the image lives at "public/images/logo.png" #}
<img src="{{ asset('images/logo.png') }}" alt="Symfony!"/>

{# the CSS file lives at "public/css/blog.css" #}
<link href="{{ asset('css/blog.css') }}" rel="stylesheet"/>

{# the JS file lives at "public/bundles/acme/js/loader.js" #}
<script src="{{ asset('bundles/acme/js/loader.js') }}"></script>

Absolute Link

<img src="{{ absolute_url(asset('images/logo.png')) }}" alt="Symfony!"/>

<link rel="shortcut icon" href="{{ absolute_url('favicon.png') }}">

# Twig : The App Global Variable
Symfony creates a context object that is injected into every Twig template automatically as a variable called app. It provides access to some application information:

<p>Username: {{ app.user.username ?? 'Anonymous user' }}</p>
{% set route = app.request.get('_route') %}
{% if app.debug %}
    <p>Request method: {{ app.request.method }}</p>
    <p>Application Environment: {{ app.environment }}</p>
{% endif %}

The app variable (which is an instance of AppVariable) gives you access to these variables:

+ app.user + The current user object or null if the user is not authenticated.
+ app.request + The Request object that stores the current request data (depending on your application, this can be a sub-request or a regular request).
+ app.session + The Session object that represents the current user's session or null if there is none.
+ app.flashes + An array of all the flash messages stored in the session. You can also get only the messages of some type (e.g. app.flashes('notice')).
+ app.environment + The name of the current configuration environment (dev, prod, etc).
+ app.debug + True if in debug mode. False otherwise.
+ app.token + A TokenInterface object representing the security token.

# Global Variables
Twig allows you to automatically inject one or more variables into all templates. These global variables are defined in the twig.globals option inside the main Twig configuration file:
- config/packages/twig.yaml
twig:
    # ...
    globals:
        ga_tracking: 'UA-xxxxx-x'
        # the value is the service's id
        uuid: '@App\Generator\UuidGenerator'

<p>The Google tracking code is: {{ ga_tracking }}</p>
UUID: {{ uuid.generate }}

# Rendering a Template in Services

// src/Service/SomeService.php
namespace App\Service;

use Twig\Environment;

class SomeService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function someMethod()
    {
        // ...

        $htmlContents = $this->twig->render('product/index.html.twig', [
            'category' => '...',
            'promotions' => ['...', '...'],
        ]);
    }
}

# Checking if a Template Exists

Templates are loaded in the application using a Twig template loader, which also provides a method to check for template existence. First, get the loader:

use Twig\Environment;

class YourService
{
    // this code assumes that your service uses autowiring to inject dependencies
    // otherwise, inject the service called 'twig' manually
    public function __construct(Environment $twig)
    {
        $loader = $twig->getLoader();
    }
}
if ($loader->exists('theme/layout_responsive.html.twig')) {
    // the template exists, do something
    // ...
}

# Linting Twig Templates
The lint:twig command checks that your Twig templates don't have any syntax errors. It's useful to run it before deploying your application to production (e.g. in your continuous integration server):

- check all the application templates
php bin/console lint:twig
- you can also check directories and individual templates
php bin/console lint:twig templates/email/
php bin/console lint:twig templates/article/recent_list.html.twig
- you can also show the deprecated features used in your templates
php bin/console lint:twig --show-deprecations templates/email/

# Routing to see routes by typing symfony console debug:router

https://symfony.com/doc/5.4/routing.html

composer require doctrine/annotations

- config/routes/annotations.yaml

controllers:
    resource: ../../src/Controller/
    type: annotation

kernel:
    resource: ../../src/Kernel.php
    type: annotation


# Missing package: to use the make:crud command

Missing package: to use the make:crud command, run:
composer require annotations

# Logger

composer require logger //todo

# Test

composer require --dev phpunit/phpunit symfony/test-pack
composer require --dev symfony/test-pack

php bin/phpunit


# create the test database

echo 'symfony console --env=test doctrine:database:create'."<br>";


echo 'symfony console --env=test doctrine:schema:create'."<br>";

echo 'Lancer la commande : /bin/phpunit ou php bin/phpunit';


echo 'symfony console make:test'."<br>";
echo 'php bin/phpunit tests/StockTest.php'."<br>";

echo 'symfony console make:command';
echo 'php bin/phpunit tests/Feature/RefreshStockProfileCommandTest.php';

# Webpack Encore in a Symfony 5 project

composer require symfony/webpack-encore-bundle
nvm use v16.20.0
yarn install

# Add jquery

npm install --save-dev jquery
- add in app.js
    import $ from 'jquery';
    global.$ = global.jQuery = $;
- add this on webpack.config.js
    .autoProvidejQuery()
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    });

# add bootstrap

yarn add sass-loader@^13.0.0 sass --dev

Add this on webpack.config.js
// enables Sass/SCSS support
.enableSassLoader()
Add this on app.scss
@import "~bootstrap/scss/bootstrap";
Add this on app.js
import './styles/app.scss';

# add datepicker

npm install --save-dev jquery-ui
- add this on app.js
    // Import jQuery UI
    import 'jquery-ui/ui/widgets/datepicker';
    import 'jquery-ui/themes/base/all.css';

    // Import Bootstrap DatetimePicker
    import 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css';
    import 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min';

# add stripe

composer require stripe/stripe-php
php bin/console make:controller Stripe
// accepter
4242424242424242
// refuser
4000000000000069

# fixture

composer require --dev doctrine/doctrine-fixtures-bundle

symfony console doctrine:fixtures:load


# TestsUnitaires:
Écrivez des tests unitaires pour valider le fonctionnement de votre application. 
Testez les cas d'utilisation principaux, y compris : 
- l'inscription,
- la réservation 
- paiement.

# create the tables/columns in the test database

composer require --dev symfony/test-pack

symfony console --env=test doctrine:database:create
symfony console --env=test doctrine:schema:create

php bin/phpunit tests/InscriptionEtudiantTest.php 
php bin/phpunit tests/Controller/UserControllerTest.php --filter testIndex 

symfony console make:test
php bin/phpunit tests/StockTest.php 

### Panter

- install this
composer require --dev dbrekelmans/bdi vendor/bin/bdi detect drivers
composer req --dev symfony/panther
- install this on mac
brew install chromedriver geckodriver 

env PANTHER_NO_HEADLESS=1 ./vendor/bin/phpunit tests/ContactPanterTest.php --debug
env PANTHER_NO_HEADLESS=1 ./vendor/bin/phpunit tests/ContactPanterTest.php --stop-on-failure

- correct command to execute panter width open chrome but the chrome is open do the test and close immediatly
env PANTHER_NO_HEADLESS=1 ./vendor/bin/phpunit tests/ContactPanterTest.php --filter testLoginAsEtudiantug

- correct command to execute panter width open chrome
env PANTHER_NO_HEADLESS=1 ./vendor/bin/phpunit tests/ContactPanterTest.php --filter testCreateReservation --debug

- correct command to execute juste do the test without opening chrome
./vendor/bin/phpunit tests/ContactPanterTest.php


composer require --dev  php-webdriver/webdriver
composer require --dev php-webdriver/php-webdriver