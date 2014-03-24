<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 09/03/14
 * Time: 19:52
 */

namespace AM\AdminBundle\Command;

use AM\AdminBundle\Entity\Administrator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('admin:create-administrator')
            ->setDescription('Create a new Administrator')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The administrator username'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'The administrator password'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = strtolower($input->getArgument('username'));
        $password = $input->getArgument('password');

        $administrator = new Administrator();

        $factory = $this->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($administrator);
        $encodedPassword = $encoder->encodePassword($password, $administrator->getSalt());

        $administrator->setUsername($username);
        $administrator->setPassword($encodedPassword);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($administrator);
        $em->flush();

        $output->writeln(sprintf('Administrator %s created with password %s', $username, $password));
    }
} 