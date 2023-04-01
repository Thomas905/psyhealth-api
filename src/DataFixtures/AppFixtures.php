<?php

namespace App\DataFixtures;

use App\Entity\Plan;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $usernames = ['user1', 'user2', 'user3', 'user4', 'user5'];
        foreach ($usernames as $username) {
            $user = new User();
            $user->setUsername($username);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password'
            );
            $user->setPassword($hashedPassword);
            $user->setHasReplied(0);

            $manager->persist($user);
        }

        $questionlist = ['Question 1', 'Question 2', 'Question 3', 'Question 4', 'Question 5'];
        foreach ($questionlist as $questions) {
            $question = new Question();
            $question->setDescription($questions);
            $manager->persist($question);
        }

        $plan = new Plan();
        $plan->setName('Plan 1');
        $plan->setMonth(1);
        $manager->persist($plan);

        $manager->flush();
    }
}
