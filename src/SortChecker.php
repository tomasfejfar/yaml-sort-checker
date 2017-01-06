<?php declare(strict_types = 1);

namespace Mhujer\YamlSortChecker;

use Symfony\Component\Yaml\Yaml;

class SortChecker
{

	public function isSorted(string $filename, int $depth): SortCheckResult
	{
		try {
			$data = Yaml::parse(file_get_contents($filename));

			$errors = $this->areDataSorted($data, null, $depth);

			return new SortCheckResult($errors);

		} catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
			return new SortCheckResult([
				sprintf('Unable to parse the YAML string: %s', $e->getMessage()),
			]);
		}
	}

	private function areDataSorted(array $yamlData, string $parent = null, int $depth): array
	{
		if ($depth === 0) {
			return [];
		}

		$skippedKeys = [
			'imports',
			'parameters',
		];

		$errors = [];
		$lastKey = null;
		foreach ($yamlData as $key => $value) {
			/*if (in_array($key, $skippedKeys, true)) {
				if (is_array($value)) { // C&P zespodu, RF
					isSorted($value, ($parent !== null ? $parent . '.' : '') . $key, $depth - 1);
				}
				continue;
			}*/

			if ($lastKey !== null/* && is_string($lastKey) && is_string($key)*/) {
				if (strcasecmp($key, $lastKey) < 0) {
					if ($parent !== null) {
						$printKey = $parent . '.' . $key;
						$printLastKey = $parent . '.' . $lastKey;
					} else {
						$printKey = $key;
						$printLastKey = $lastKey;
					}
					$errors[] = sprintf('"%s" should be before "%s"', $printKey, $printLastKey);
				}
			}
			$lastKey = $key;

			if (is_array($value)) {
				$errors = array_merge(
					$errors,
					$this->areDataSorted($value, ($parent !== null ? $parent . '.' : '') . $key, $depth - 1)
				);
			}

		}

		return $errors;
	}

}
