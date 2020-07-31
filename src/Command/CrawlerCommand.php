<?php

namespace Pedrommone\BrazilianLocalCodes\Command;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerCommand extends Command
{
    protected const URI = 'https://www.anatel.gov.br/legislacao/resolucoes/16-2001/383-resolucao-263';

    protected Client $client;
    protected Crawler $crawler;
    protected static $defaultName = 'crawler';

    public function __construct(Client $client, Crawler $crawler)
    {
        $this->client = $client;
        $this->crawler = $crawler;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            'Starting crawler procedure',
            '==========================',
            '',
        ]);

        $request = $this->client->request('GET', self::URI, [
            RequestOptions::VERIFY => false,
        ]);

        $this->crawler->addHtmlContent((string) $request->getBody());

        $cells = $this->crawler->filter('table tr td p')->each(function (Crawler $p) {
            if ($p->filter('span')->count() > 0) {
                return $p->children()->first()->text();
            }

            return $p->text();
        });

        $codes = [];
        $path = __DIR__ . '/../../data/local-codes.json';
        $rows = array_chunk(array_slice($cells, 4), 3);

        $output->writeln(sprintf('Found %s rows to process', count($rows)));

        foreach ($rows as $row) {
            $codes[] = [
                'city' => $row[1],
                'code' => (int) $row[2],
                'state' => $row[0],
            ];
        }

        $output->writeln("Saving files into {$path}");

        if (file_put_contents($path, json_encode($codes))) {
            $output->writeln('Done! :)');

            return Command::SUCCESS;
        }

         return Command::FAILURE;
    }
}
