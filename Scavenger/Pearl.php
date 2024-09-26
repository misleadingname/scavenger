<?php

namespace Scavenger;

use Exception;

enum DataType: int
{
	case Text = 0;
	case Integer = 1;
	case Binary = 2;
}

class Pearl
{
	private string $filePath;
	private array $data = [];

	public function __construct(
		private readonly string  $name,
		private readonly ?string $category = null
	)
	{
		$this->filePath = $this->resolveFilePath($name, $category);
		if (file_exists($this->filePath)) {
			$this->loadFromFile();
		}
	}

	private function resolveFilePath(string $name, ?string $category): string
	{
		$folder = SCAVENGER_CONFIG["Pearls"]["Location"];

		$baseDir = PROJECT_ROOT . "/$folder/$category/";
		return $baseDir . $name . '.prl';
	}

	private function loadFromFile(): void
	{
		$contents = file_get_contents($this->filePath);
		$offset = 0;

		while ($offset < strlen($contents)) {
			$type = unpack('C', $contents[$offset])[1];
			$offset++;

			$keySize = unpack('C', $contents[$offset])[1];
			$offset++;

			$dataSize = unpack('N', substr($contents, $offset, 4))[1];
			$offset += 4;

			$key = substr($contents, $offset, $keySize);
			$offset += $keySize;
			$data = substr($contents, $offset, $dataSize);
			$offset += $dataSize;

			$this->data[$key] = [
				'type' => $type,
				'data' => $data
			];
		}
	}

	/**
	 * @throws Exception
	 */
	public function getData(string $key): mixed
	{
		if (!isset($this->data[$key])) {
			throw new Exception("Key not found: $key");
		}

		$entry = $this->data[$key];
		$type = $entry['type'];
		$data = $entry['data'];

		return match ($type) {
			DataType::Text->value, DataType::Binary->value => $data,
			DataType::Integer->value => unpack('N', $data)[1],
			default => throw new Exception("Unknown type"),
		};
	}

	public function setData(string $key, DataType $type, mixed $value): void
	{
		$data = match ($type) {
			DataType::Text, DataType::Binary => $value,
			DataType::Integer => pack('N', $value),
		};

		$this->data[$key] = [
			'type' => $type->value,
			'data' => $data,
		];
	}

	public function flush(): void
	{
		$binaryData = '';

		foreach ($this->data as $key => $entry) {
			$keySize = strlen($key);
			$dataSize = strlen($entry['data']);

			$binaryData .= pack('C', $entry['type']);
			$binaryData .= pack('C', $keySize);
			$binaryData .= pack('N', $dataSize);
			$binaryData .= $key;
			$binaryData .= $entry['data'];
		}


		if (!file_exists(dirname($this->filePath))) {
			mkdir(dirname($this->filePath), 0777, true);
		}

		file_put_contents($this->filePath, $binaryData);
	}
}