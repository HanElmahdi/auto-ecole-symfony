<?php

namespace App\Tests;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Facebook\WebDriver\WebDriverBy;

class ContactPanterTest extends PantherTestCase
{
    /** @var EntityManagerInterface */
    protected $entityManager;
    
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }
    
    public function testLoginAsEtudiantSuccess(): void
    {
        $client = static::createPantherClient();

        $crawler = $client->request('GET', '/login');
        self::assertSelectorTextContains('h1','Authentification');

        $form = $crawler->selectButton('Login')->form([
            '_username' => "mahdi.webdesigner@gmail.com",
            '_password' => "admin"
        ]);

        $client->submit($form);

        $this->assertSame('1','1');
        $this->assertSelectorTextContains('h1.mb-4', 'Réservez Vos Cours de Co');

    }

    public function testLoginAsEtudiantFail(): void
    {
        $client = static::createPantherClient();

        $crawler = $client->request('GET', '/login');
        self::assertSelectorTextContains('h1','Authentification');

        $form = $crawler->selectButton('Login')->form([
            '_username' => "mahdi.webdesigner@gmail.com",
            '_password' => "fail"
        ]);

        $client->submit($form);

        $this->assertSame('1','1');
        $this->assertSelectorTextContains('h1.mb-4', 'Authentification');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Identifiants invalides.');

    }

    public function testCreateReservation(): void // todo
    {
        $client = static::createPantherClient();

        $crawler = $client->request('GET', '/login');
        self::assertSelectorTextContains('h1','Authentification');

        $form = $crawler->selectButton('Login')->form([
            '_username' => "mahdi.webdesigner@gmail.com",
            '_password' => "admin"
        ]);
        
        // $client->getWebDriver()->findElement(WebDriverBy::name('checkbox_input_example'))->click();

        $client->submit($form);
        $this->assertSelectorTextContains('h1.mb-4', 'Réservez Vos Cours de');
        // $client->waitFor('h1',1);
        
        $crawler = $client->request('GET', '/reservation/new');
        // $client->waitForVisibility('iframe');

        // $client->getWebDriver()->findElement(WebDriverBy::name('reservation[hours]'))->focus();
        // $element = $driver->findElement(WebDriverBy::cssSelector('#food span.dairy'));
        
        
        // 'input[]' => "4242424242424242",
        $form2 = $crawler->filter('#pay-btn')->form([
            'reservation[hours]' => "2",
            'reservation[date_exam]' => "2023-12-12",
            'reservation[type_permis]' => 'Formule B',
            // 'reservation[instructeurs]' => "1",
        ]);
        // $client->getWebDriver()->findElement(WebDriverBy::name('ajouter'))->click();
        // $this->assertSame('1',2);
        $client->submit($form2);

    }
}
