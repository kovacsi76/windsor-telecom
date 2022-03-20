<?php

use App\Kernel;

function bootstrap()
{
    $kernel = new Kernel('test', true);
    $kernel->boot();
    $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    $application->setAutoExit(false);
    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:drop',
        '--force' => true,
    ]));
    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:schema:update',
        '--force' => true
    ]));
    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '-n' => true
    ]));
    $kernel->shutdown();
}
