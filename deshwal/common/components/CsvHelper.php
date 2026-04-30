<?php
namespace common\components;

use Yii;

class CsvHelper
{

    public static function saveRecordsToCsv(
        array $rows,
        string $subFolder,
        $recordId,
        string $fileNamePrefix = 'backup',
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): ?string {
        if (empty($rows)) {
            return null;
        }

        $safeRecordId = preg_replace('/[^A-Za-z0-9._-]/', '_', (string)$recordId);

        $baseDir = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . ltrim($subFolder, DIRECTORY_SEPARATOR);
        if (!is_dir($baseDir)) {
            $old = @mkdir($baseDir, 0775, true);
            if ($old === false && !is_dir($baseDir)) {
                throw new \RuntimeException("Cannot create directory: {$baseDir}");
            }
        }

        $recordDir = $baseDir . DIRECTORY_SEPARATOR . $safeRecordId;
        if (!is_dir($recordDir)) {
            $old = @mkdir($recordDir, 0775, true);
            if ($old === false && !is_dir($recordDir)) {
                throw new \RuntimeException("Cannot create directory: {$recordDir}");
            }
        }

        $timestamp = date('Ymd_His');
        $fileName  = $fileNamePrefix . '_' . $recordId . '_' . $timestamp . '.csv';
        $filePath  = $recordDir . DIRECTORY_SEPARATOR . $fileName;

        $firstRow = reset($rows);
        if (!is_array($firstRow)) {
            throw new \RuntimeException('All rows must be associative arrays.');
        }
        $headers = array_keys($firstRow);

        $handle = fopen($filePath, 'w');
        if ($handle === false) {
            throw new \RuntimeException("Cannot open file for writing: {$filePath}");
        }

        fputcsv($handle, $headers, $delimiter, $enclosure, $escape);

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $values = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                if (is_resource($value) || (is_object($value) && !method_exists($value, '__toString'))) {
                    $value = '[unserializable]';
                } elseif (is_object($value)) {
                    $value = (string)$value;
                } elseif (is_bool($value)) {
                    $value = $value ? '1' : '0';
                }
                $values[] = $value;
            }
            fputcsv($handle, $values, $delimiter, $enclosure, $escape);
        }

        fclose($handle);

        return $filePath;
    }
}
