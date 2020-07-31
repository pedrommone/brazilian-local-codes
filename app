#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\CrawlerCommand;
use GuzzleHttp\Client;
use Symfony\Component\Console\Application;
use Symfony\Component\DomCrawler\Crawler;

$application = new Application();

$application->add(new CrawlerCommand(new Client(), new Crawler()));
$application->run();
