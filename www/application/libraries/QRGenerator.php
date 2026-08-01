<?php
class QRGenerator {

	protected $size;
	protected $data;
	protected $encoding;
	protected $errorCorrectionLevel;
	protected $marginInRows;
	protected $debug;

	public function __construct($data = 'Hello', $size = '300', $encoding = 'UTF-8', $errorCorrectionLevel = 'L', $marginInRows = 1, $debug = false) {

		$this->data = urlencode($data);
		$this->size = ($size > 100 && $size < 800) ? $size : 300;
		$this->encoding = ($encoding == 'Shift_JIS' || $encoding == 'ISO-8859-1' || $encoding == 'UTF-8') ? $encoding : 'UTF-8';
		$this->errorCorrectionLevel = ($errorCorrectionLevel == 'L' || $errorCorrectionLevel == 'M' || $errorCorrectionLevel == 'Q' || $errorCorrectionLevel == 'H') ? $errorCorrectionLevel : 'L';
		$this->marginInRows = ($marginInRows > 0 && $marginInRows < 10) ? $marginInRows : 4;
		$this->debug = ($debug == true) ? true : false;
	}

	public function generate() {
		$data_param = $this->data;
		$host = 'ems_qr_service';

		// Check if ems_qr_service host is resolved in DNS (Docker VPS environment)
		$ip = @gethostbyname($host);
		if ($ip !== $host) {
			return 'http://ems_qr_service:8000/api?data=' . $data_param;
		}

		// Check if local docker port 8005 is mapped on host
		$fp = @fsockopen('127.0.0.1', 8005, $errno, $errstr, 0.2);
		if ($fp) {
			fclose($fp);
			return 'http://127.0.0.1:8005/api?data=' . $data_param;
		}

		// Fallback for local Windows / non-Docker environments
		return "https://api.qrserver.com/v1/create-qr-code/?size=" . $this->size . "x" . $this->size . "&data=" . $data_param;
	}
}