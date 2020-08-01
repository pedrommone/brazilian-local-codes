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

        $cells = $this->crawler->filter('table > tbody > tr > td')
            ->slice(1)
            ->each(fn (Crawler $crawler) => $this->parseTd($crawler));

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

    private function parseTd(Crawler $td): string
    {
        if ($td->children()->count() === 1) {
            return $td->children()->first()->text();
        }

        // Try to match a resolution "31 (Redação dada pela Resolução nº 643, de 2 de dezembro de 2014)"
        preg_match('/(\w+)\(/', $td->children()->eq(1)->text(), $matches);

        if (count($matches) === 0) {
            // Try to match a double code inside at first "p"
            preg_match('/.*(\d{2})/', $td->children()->eq(0)->text(), $codes);

            return $codes[1];
        }

        return $matches[1];
    }
}
