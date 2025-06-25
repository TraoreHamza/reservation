<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRegistrationTest extends WebTestCase
{
    public function testUserCanRegisterAndLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Inscription')->form([
            'registration_form[email]' => 'testuser@example.com',
            'registration_form[plainPassword][first]' => 'Test1234!',
            'registration_form[plainPassword][second]' => 'Test1234!',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
        // TODO: Vérifier que l'utilisateur peut se connecter
    }
}
