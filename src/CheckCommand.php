<?php declare(strict_types = 1);

namespace Mhujer\YamlSortChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command
{

	protected function configure(): void
	{
		$this->setName('yaml-check-sort');
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$output->writeln('#### YAML Sort Checker ####');

		$sortChecker = new SortChecker();

		$filesForChecking = [
			[
				'filename' => __DIR__ . '/../tests/fixture/first-level.yml',
				'depth' => 3,
				'excludedKeys' => [],
			],
			[
				'filename' => __DIR__ . '/../tests/fixture/first-second-and-third-level.yml',
				'depth' => 3,
				'excludedKeys' => [
					'foo' => [
						'car' => [
							'c'
						],
					],
				],
			],
		];

		$isOk = true;
		foreach ($filesForChecking as $fileForChecking) {
			$filename = $fileForChecking['filename'];
			$depth = $fileForChecking['depth'];

			$output->writeln('');
			$output->writeln(sprintf('Checking "%s":', $filename));

			$sortCheckResult = $sortChecker->isSorted($filename, $depth, $fileForChecking['excludedKeys']);

			if ($sortCheckResult->isOk()) {
				$output->writeln('OK');
			} else {
				foreach ($sortCheckResult->getMessages() as $message) {
					$output->writeln($message);
				}
				$isOk = false;
			}
		}

		$output->writeln('');
		if (!$isOk) {
			$output->writeln('Fix the YAMLs!');
			exit(1);
		} else {
			$output->writeln('All YAMLs are properly sorted!');
			exit(1);
		}
	}

}
