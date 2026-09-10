<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ZipPacker - Dual-engine ZIP archive builder.
 * Works with PHP native ZipArchive if available, or falls back to a 100% pure PHP
 * PKZIP binary serializer without requiring any external extensions.
 */
class ZipPacker
{
    private $files = array();

    public function addFile($filename, $content)
    {
        $filename = str_replace('\\', '/', $filename);
        $this->files[$filename] = $content;
    }

    public function getZipContent()
    {
        // Engine 1: Native ZipArchive if extension is loaded
        if (class_exists('ZipArchive')) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'zip_');
            $zip = new ZipArchive();
            if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($this->files as $name => $content) {
                    $zip->addFromString($name, $content);
                }
                $zip->close();
                $data = file_get_contents($tmpFile);
                @unlink($tmpFile);
                if ($data !== false && strlen($data) > 0) {
                    return $data;
                }
            }
        }

        // Engine 2: Pure-PHP PKZIP serializer
        return $this->buildPurePhpZip();
    }

    private function buildPurePhpZip()
    {
        $dataSec = '';
        $ctrlDir = '';
        $offset = 0;

        foreach ($this->files as $name => $content) {
            $uncompressedSize = strlen($content);
            $crc = crc32($content);

            if (function_exists('gzdeflate')) {
                $zdata = gzdeflate($content);
                $compressedSize = strlen($zdata);
                $method = 8; // Deflated
            } else {
                $zdata = $content;
                $compressedSize = $uncompressedSize;
                $method = 0; // Stored
            }

            // DOS timestamp (current time)
            $timeArray = getdate();
            $dosTime = (($timeArray['hours'] << 11) | ($timeArray['minutes'] << 5) | ($timeArray['seconds'] >> 1));
            $dosDate = ((($timeArray['year'] - 1980) << 9) | ($timeArray['mon'] << 5) | $timeArray['mday']);

            // Local file header (PK\x03\x04)
            $fr  = "\x50\x4b\x03\x04";
            $fr .= pack('v', 20);            // Version needed to extract (2.0)
            $fr .= pack('v', 0);             // General purpose bit flag
            $fr .= pack('v', $method);       // Compression method
            $fr .= pack('v', $dosTime);      // Last mod file time
            $fr .= pack('v', $dosDate);      // Last mod file date
            $fr .= pack('V', $crc);          // CRC-32
            $fr .= pack('V', $compressedSize); // Compressed size
            $fr .= pack('V', $uncompressedSize); // Uncompressed size
            $fr .= pack('v', strlen($name)); // Filename length
            $fr .= pack('v', 0);             // Extra field length
            $fr .= $name;
            $fr .= $zdata;
            $dataSec .= $fr;

            // Central directory entry (PK\x01\x02)
            $cd  = "\x50\x4b\x01\x02";
            $cd .= pack('v', 20);            // Version made by
            $cd .= pack('v', 20);            // Version needed to extract
            $cd .= pack('v', 0);             // General purpose bit flag
            $cd .= pack('v', $method);       // Compression method
            $cd .= pack('v', $dosTime);      // Last mod file time
            $cd .= pack('v', $dosDate);      // Last mod file date
            $cd .= pack('V', $crc);          // CRC-32
            $cd .= pack('V', $compressedSize); // Compressed size
            $cd .= pack('V', $uncompressedSize); // Uncompressed size
            $cd .= pack('v', strlen($name)); // Filename length
            $cd .= pack('v', 0);             // Extra field length
            $cd .= pack('v', 0);             // File comment length
            $cd .= pack('v', 0);             // Disk number start
            $cd .= pack('v', 0);             // Internal file attributes
            $cd .= pack('V', 32);            // External file attributes (normal file)
            $cd .= pack('V', $offset);       // Relative offset of local header
            $cd .= $name;
            $ctrlDir .= $cd;

            $offset = strlen($dataSec);
        }

        $cdSize = strlen($ctrlDir);
        // End of central directory record (PK\x05\x06)
        $eocd  = "\x50\x4b\x05\x06";
        $eocd .= pack('v', 0);               // Number of this disk
        $eocd .= pack('v', 0);               // Disk with start of central directory
        $eocd .= pack('v', count($this->files)); // Total entries on this disk
        $eocd .= pack('v', count($this->files)); // Total entries in central directory
        $eocd .= pack('V', $cdSize);         // Size of central directory
        $eocd .= pack('V', $offset);         // Offset of start of central directory
        $eocd .= pack('v', 0);               // ZIP comment length

        return $dataSec . $ctrlDir . $eocd;
    }
}
