<?php

namespace App\Test\Controller;

use App\Entity\Instructeur;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $repository;
    private string $path = '/';
    private EntityManagerInterface $manager;
    /** @var EntityManagerInterface */
    protected $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(User::class);
    }
    /**
     * Test Redirection Home Page
     */
    public function testHomePage(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Home');
        self::assertSelectorTextContains('h1', 'Réservez Vos Cours de Conduite en Ligne');
        self::assertCount(1, $crawler->filter('p'));
    }

    /**
     * Test Login width Good Credential
     */
    public function testLoginGoodCredential(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextContains('h1','Authentification');

        $form = $crawler->selectButton('Login')->form([
            '_username' => 'mahdi.webdesigner@gmail.com',
            '_password' => 'Hanafi123*'
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects();
        $this->client->followRedirect();

        self::assertSelectorExists('h1.mb-4.home');
        self::assertSelectorTextContains('h1',"Réservez Vos Cours de Condui");
        self::assertSelectorNotExists('.alert.alert-danger');
        
    }

    /**
     * Test Login width Bad Credential
     */
    public function testLoginBadCredential(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextContains('h1','Authentification');

        $form = $crawler->selectButton('Login')->form([
            '_username' => 'mahdi.webdesigner@gmail.com',
            '_password' => 'Hanafi123'
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects();
        $this->client->followRedirect();

        self::assertSelectorExists('.alert.alert-danger');
        self::assertSelectorNotExists('.alert.alert-success');
        self::assertSelectorTextContains('.alert.alert-danger',"Identifiants invalides.");
    }
    
    /**
     * Test Inscription OK
     * Suppression du record
     */
    public function testInscriptionEtudiantSuccess(): void
    {
        $crawler = $this->client->request('GET', '/register');
        
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextContains('h1','Inscription étudiant');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'mahdi.webdesigner@gmail.com',
            'registration_form[prenom]' => 'Anas',
            'registration_form[nom]' => 'Oubaha',
            'registration_form[phone]' => '0656142533',
            'registration_form[plainPassword]' => 'Hanafi123*',
            'registration_form[agreeTerms]' => '1'
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects();
        $this->client->followRedirect();
        
        self::assertSelectorExists('.alert.alert-success');
        self::assertSelectorTextContains('.alert.alert-success',"Inscription réussie");

        $fixture = $this->repository->findAll();

        self::assertSame('mahdi.webdesigner@gmail.com', $fixture[0]->getEmail());
        self::assertSame([
            0 => 'ROLE_ETUDIANT',
            1 => 'ROLE_USER'
        ], $fixture[0]->getRoles());
        self::assertSame('Anas', $fixture[0]->getEtudiant()->getPrenom());
        self::assertSame('Oubaha', $fixture[0]->getEtudiant()->getNom());
        self::assertSame('0656142533', $fixture[0]->getEtudiant()->getPhone());

        $this->entityManager->remove($fixture[0]);
        $this->entityManager->flush();
    }

    /**
     * Etudiant : Access to List Reservation Require AdminRole
     */
    public function testListReservationRequireAdminRoleForEtudiant(): void {
        $userRepository = $this->entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy([
            'email' => 'mahdi.webdesigner@gmail.com'
        ]);

        self::assertNotNull($user);
        // $user = new user();
        $session = self::bootKernel()->getContainer()->get('session');
        $token = new UsernamePasswordToken($user,null,'main',$user->getRoles());
        $session->set('_security_main',serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(),$session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET','/reservation');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Etudiant : Access to Create new Reservation Require EtudiantRole
     */
    public function testCreateReservationRequireEtudiantRoleForEtudiant(): void {
        $userRepository = $this->entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy([
            'email' => 'mahdi.webdesigner@gmail.com'
        ]);

        self::assertNotNull($user);
        $session = self::bootKernel()->getContainer()->get('session');
        $token = new UsernamePasswordToken($user,null,'main',$user->getRoles());
        $session->set('_security_main',serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(),$session->getId());
        $this->client->getCookieJar()->set($cookie);
        // redirection to page new reservation
        $crawler = $this->client->request('GET','/reservation/new');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        
        /////////////////////
        /////////////////////
        /////////////////////

        // self::assertSelectorTextContains('h1','Ajouter une réservation');

        // $instructorRepository = $this->entityManager->getRepository(Instructeur::class);

        // $instructor = $instructorRepository->findOneBy([
        //     'email' => 'instructor@gmail.com'
        // ]);

        // $form = $crawler->selectButton('Ajouter')->form([
        //     'reservation[hours]' => "2",
        //     'reservation[date_exam]' => "2023-12-12",
        //     'reservation[type_permis]' => 'Formule B',
        //     'reservation[instructeurs]' => "1",
        // ]);
        // $this->client->submit($form);
        // self::assertResponseRedirects();
        // $this->client->followRedirect();        
    }

    /**
     * Admin : Access to List Reservation Require AdminRole
     */
    public function testListReservationRequireAdminRoleForAdmin(): void {
        $userRepository = $this->entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy([
            'email' => 'admin@admin.com'
        ]);

        self::assertNotNull($user);
        $session = self::bootKernel()->getContainer()->get('session');
        $token = new UsernamePasswordToken($user,null,'main',$user->getRoles());
        $session->set('_security_main',serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(),$session->getId());
        $this->client->getCookieJar()->set($cookie);
        // Redirection List reservation
        $this->client->request('GET','/reservation');
        self::assertResponseStatusCodeSame(Response::HTTP_MOVED_PERMANENTLY);
    }

    








    public function testShow(): void
    {

        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setIsVerified('My Title');
        $fixture->setEtudiant('My Title');
        $fixture->setInstructeur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setIsVerified('My Title');
        $fixture->setEtudiant('My Title');
        $fixture->setInstructeur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[email]' => 'Something New',
            'user[roles]' => 'Something New',
            'user[password]' => 'Something New',
            'user[isVerified]' => 'Something New',
            'user[etudiant]' => 'Something New',
            'user[instructeur]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getIsVerified());
        self::assertSame('Something New', $fixture[0]->getEtudiant());
        self::assertSame('Something New', $fixture[0]->getInstructeur());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new User();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setIsVerified('My Title');
        $fixture->setEtudiant('My Title');
        $fixture->setInstructeur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/user/');
    }
}
