<?php declare(strict_types = 1);

namespace Mhujer\YamlSortChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

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

		$configFilePath = realpath('yaml-sort-checker.yml');

		$output->writeln(sprintf('Using config file "%s"', $configFilePath));

		$config = Yaml::parse(file_get_contents($configFilePath));

		if (!array_key_exists('files', $config)) {
			$output->writeln('There must be a key "files" in config');
			exit(1);
		}

		if (count($config['files']) === 0) {
			$output->writeln('There must be some files in the config');
			exit(1);
		}

		$isOk = true;
		foreach ($config['files'] as $filename => $options) {
			$depth = $options['depth'];
			$excludedKeys = array_key_exists('excludedKeys', $options) ? $options['excludedKeys'] : [];

			$output->writeln('');
			$output->writeln(sprintf('Checking "%s":', $filename));
			$sortCheckResult = $sortChecker->isSorted($filename, $depth, $excludedKeys);

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
			exit(0);
		}
	}

}
