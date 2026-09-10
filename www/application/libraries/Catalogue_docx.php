<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/ZipPacker.php';

/**
 * Catalogue_docx
 * High-performance, self-contained OpenXML Word (.docx) generator for EMS Show Catalogue reports.
 * Complies with Microsoft Office Open XML (ECMA-376) standards.
 */
class Catalogue_docx
{
    private $CI;
    private $images = array();
    private $imageRels = array();
    private $relCounter = 10;

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->CI =& get_instance();
        }
    }

    /**
     * Generate and stream the DOCX file to the browser.
     *
     * @param array $payload Structured catalogue data
     */
    public function export($payload)
    {
        $docxBinary = $this->buildDocx($payload);
        $companyName = !empty($payload['company']) ? $payload['company'] : 'Exhibitor';
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $companyName);
        $filename = 'Catalogue_' . $safeName . '_' . date('Ymd_His') . '.docx';

        if (ob_get_length()) {
            @ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . strlen($docxBinary));

        echo $docxBinary;
        exit;
    }

    /**
     * Build the DOCX binary package.
     */
    public function buildDocx($payload)
    {
        $packer = new ZipPacker();

        // 1. [Content_Types].xml
        $packer->addFile('[Content_Types].xml', $this->getContentTypesXml());

        // 2. _rels/.rels
        $packer->addFile('_rels/.rels', $this->getPackageRelsXml());

        // 3. word/settings.xml
        $packer->addFile('word/settings.xml', $this->getSettingsXml());

        // 4. word/styles.xml
        $packer->addFile('word/styles.xml', $this->getStylesXml());

        // 5. Build Document Body and gather images
        $this->images = array();
        $this->imageRels = array();
        $this->relCounter = 10;

        $documentXml = $this->getDocumentXml($payload);

        // Add document XML
        $packer->addFile('word/document.xml', $documentXml);

        // Add document relationships (with image references)
        $packer->addFile('word/_rels/document.xml.rels', $this->getDocumentRelsXml());

        // Add embedded images
        foreach ($this->images as $rel) {
            $packer->addFile('word/' . $rel['target'], $rel['data']);
        }

        return $packer->getZipContent();
    }

    /**
     * Register an image to be embedded into the Word document.
     */
    private function registerImage($filePath, $maxWidth = 200, $maxHeight = 80)
    {
        if (empty($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            return null;
        }

        $info = @getimagesize($filePath);
        if (!$info) {
            return null;
        }

        $origWidth = $info[0];
        $origHeight = $info[1];
        $mime = $info['mime'];

        $ext = 'png';
        if ($mime === 'image/jpeg') {
            $ext = 'jpg';
        }

        $scale = min($maxWidth / max(1, $origWidth), $maxHeight / max(1, $origHeight), 1);
        $renderW = round($origWidth * $scale);
        $renderH = round($origHeight * $scale);

        // 1 pixel = 9525 EMU (English Metric Units)
        $cx = $renderW * 9525;
        $cy = $renderH * 9525;

        $this->relCounter++;
        $relId = 'rId' . $this->relCounter;
        $mediaTarget = 'media/img_' . $this->relCounter . '.' . $ext;

        $imageData = file_get_contents($filePath);
        $this->images[] = array(
            'relId'  => $relId,
            'target' => $mediaTarget,
            'data'   => $imageData,
            'ext'    => $ext,
        );

        $this->imageRels[] = array(
            'relId'  => $relId,
            'target' => $mediaTarget,
        );

        return array(
            'relId' => $relId,
            'cx'    => $cx,
            'cy'    => $cy,
        );
    }

    private function renderDrawingXml($imgInfo, $name = 'Image')
    {
        if (!$imgInfo) {
            return '';
        }
        $relId = $imgInfo['relId'];
        $cx = $imgInfo['cx'];
        $cy = $imgInfo['cy'];

        return '<w:r><w:drawing>' .
            '<wp:inline distT="0" distB="0" distL="0" distR="0" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">' .
                '<wp:extent cx="' . $cx . '" cy="' . $cy . '"/>' .
                '<wp:effectExtent l="0" t="0" r="0" b="0"/>' .
                '<wp:docPr id="' . rand(100, 99999) . '" name="' . htmlspecialchars($name, ENT_XML1) . '"/>' .
                '<wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"/></wp:cNvGraphicFramePr>' .
                '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">' .
                    '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
                        '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
                            '<pic:nvPicPr>' .
                                '<pic:cNvPr id="0" name="' . htmlspecialchars($name, ENT_XML1) . '"/>' .
                                '<pic:cNvPicPr/>' .
                            '</pic:nvPicPr>' .
                            '<pic:blipFill>' .
                                '<a:blip r:embed="' . $relId . '" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/>' .
                                '<a:stretch><a:fillRect/></a:stretch>' .
                            '</pic:blipFill>' .
                            '<pic:spPr>' .
                                '<a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $cx . '" cy="' . $cy . '"/></a:xfrm>' .
                                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                            '</pic:spPr>' .
                        '</pic:pic>' .
                    '</a:graphicData>' .
                '</a:graphic>' .
            '</wp:inline>' .
        '</w:drawing></w:r>';
    }

    private function getDocumentXml($payload)
    {
        $eventColor = !empty($payload['event_color']) ? ltrim($payload['event_color'], '#') : '1F497D';
        if (strlen($eventColor) !== 6) {
            $eventColor = '1F497D';
        }

        // Register logos
        $eventLogoImg = $this->registerImage($payload['event_logo_path'], 160, 60);
        $companyLogoImg = $this->registerImage($payload['company_logo_path'], 220, 90);
        $associateLogoImg = $this->registerImage($payload['associate_logo_path'], 80, 40);
        $organizerLogoImg = $this->registerImage($payload['organizer_logo_path'], 80, 40);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" ' .
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
            'xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" ' .
            'xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ' .
            'xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
        '<w:body>';

        // 1. Event Header Banner (Table with colored background + Event Logo)
        $xml .= '<w:tbl>' .
            '<w:tblPr>' .
                '<w:tblW w:w="9500" w:type="dxa"/>' .
                '<w:tblBorders><w:top w:val="none"/><w:left w:val="none"/><w:bottom w:val="none"/><w:right w:val="none"/><w:insideH w:val="none"/><w:insideV w:val="none"/></w:tblBorders>' .
                '<w:tblCellMar><w:top w:w="120" w:type="dxa"/><w:bottom w:w="120" w:type="dxa"/><w:left w:w="120" w:type="dxa"/><w:right w:w="120" w:type="dxa"/></w:tblCellMar>' .
            '</w:tblPr>' .
            '<w:tr>' .
                '<w:tc><w:tcPr><w:tcW w:w="3800" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="' . $eventColor . '"/></w:tcPr><w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:color w:val="FFFFFF"/><w:b/></w:rPr><w:t>' . htmlspecialchars($payload['exhibition_title'], ENT_XML1) . '</w:t></w:r></w:p></w:tc>' .
                '<w:tc><w:tcPr><w:tcW w:w="1900" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:jc w:val="center"/></w:pPr>' . ($eventLogoImg ? $this->renderDrawingXml($eventLogoImg, 'Event Logo') : '<w:r><w:t></w:t></w:r>') . '</w:p></w:tc>' .
                '<w:tc><w:tcPr><w:tcW w:w="3800" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="' . $eventColor . '"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>' .
            '</w:tr>' .
        '</w:tbl>';

        // Spacer
        $xml .= '<w:p><w:pPr><w:spacing w:after="200"/></w:pPr></w:p>';

        // 2. Company Name & Contact Info + Company Logo (2-Column Table)
        $xml .= '<w:tbl>' .
            '<w:tblPr>' .
                '<w:tblW w:w="9500" w:type="dxa"/>' .
                '<w:tblBorders><w:top w:val="none"/><w:left w:val="none"/><w:bottom w:val="none"/><w:right w:val="none"/><w:insideH w:val="none"/><w:insideV w:val="none"/></w:tblBorders>' .
            '</w:tblPr>' .
            '<w:tr>' .
                // Left Column: Details
                '<w:tc>' .
                    '<w:tcPr><w:tcW w:w="6500" w:type="dxa"/></w:tcPr>' .
                    // Company Heading
                    '<w:p><w:pPr><w:spacing w:after="160"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Helvetica" w:hAnsi="Helvetica"/><w:b/><w:sz w:val="32"/><w:color w:val="' . $eventColor . '"/></w:rPr><w:t>' . htmlspecialchars($payload['company'], ENT_XML1) . '</w:t></w:r></w:p>' .
                    // Contact Info Rows
                    $this->buildInfoParagraph('Address', $payload['address']) .
                    $this->buildInfoParagraph('Country', $payload['country']) .
                    $this->buildInfoParagraph('Telephone', $payload['telephone']) .
                    $this->buildInfoParagraph('Fax', $payload['fax']) .
                    $this->buildInfoParagraph('Email', $payload['email']) .
                    $this->buildInfoParagraph('Website', $payload['website']) .
                    $this->buildInfoParagraph('Contact Person', $payload['contact_name']) .
                    $this->buildInfoParagraph('Designation', $payload['contact_designation']) .
                '</w:tc>' .
                // Right Column: Company Logo
                '<w:tc>' .
                    '<w:tcPr><w:tcW w:w="3000" w:type="dxa"/><w:vAlign w:val="top"/></w:tcPr>' .
                    '<w:p><w:pPr><w:jc w:val="right"/><w:spacing w:after="100"/></w:pPr>' .
                    ($companyLogoImg ? $this->renderDrawingXml($companyLogoImg, 'Company Logo') : '<w:r><w:rPr><w:i/><w:color w:val="888888"/></w:rPr><w:t>[No Logo]</w:t></w:r>') .
                    '</w:p>' .
                '</w:tc>' .
            '</w:tr>' .
        '</w:tbl>';

        // Spacer
        $xml .= '<w:p><w:pPr><w:spacing w:after="180"/></w:pPr></w:p>';

        // 3. Company Profile
        if (!empty($payload['profile'])) {
            $xml .= '<w:p><w:pPr><w:spacing w:after="80"/></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="24"/><w:color w:val="' . $eventColor . '"/></w:rPr><w:t>Company Profile</w:t></w:r></w:p>';
            $cleanProfile = strip_tags(html_entity_decode($payload['profile'], ENT_QUOTES, 'UTF-8'));
            $xml .= '<w:p><w:pPr><w:jc w:val="both"/><w:spacing w:line="276" w:lineRule="auto" w:after="240"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="20"/><w:color w:val="333333"/></w:rPr><w:t xml:space="preserve">' . htmlspecialchars($cleanProfile, ENT_XML1) . '</w:t></w:r></w:p>';
        }

        // 4. Company Principal Table (if active)
        if (!empty($payload['principals_active']) && !empty($payload['principals'])) {
            $xml .= '<w:p><w:pPr><w:spacing w:before="120" w:after="100"/></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="24"/><w:color w:val="' . $eventColor . '"/></w:rPr><w:t>Company Principal</w:t></w:r></w:p>';
            
            $xml .= '<w:tbl>' .
                '<w:tblPr>' .
                    '<w:tblW w:w="9500" w:type="dxa"/>' .
                    '<w:tblBorders>' .
                        '<w:top w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>' .
                        '<w:left w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>' .
                        '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>' .
                        '<w:right w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>' .
                        '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>' .
                        '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>' .
                    '</w:tblBorders>' .
                    '<w:tblCellMar><w:top w:w="100" w:type="dxa"/><w:bottom w:w="100" w:type="dxa"/><w:left w:w="100" w:type="dxa"/><w:right w:w="100" w:type="dxa"/></w:tblCellMar>' .
                '</w:tblPr>' .
                // Header Row
                '<w:tr>' .
                    '<w:tc><w:tcPr><w:tcW w:w="2200" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="F2F2F2"/></w:tcPr><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Company</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="1600" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="F2F2F2"/></w:tcPr><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Country</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="1800" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="F2F2F2"/></w:tcPr><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Phone</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="2200" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="F2F2F2"/></w:tcPr><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Email</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="1700" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="F2F2F2"/></w:tcPr><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Logo</w:t></w:r></w:p></w:tc>' .
                '</w:tr>';

            foreach ($payload['principals'] as $principal) {
                $pLogoImg = !empty($principal['logo_path']) ? $this->registerImage($principal['logo_path'], 80, 40) : null;
                $xml .= '<w:tr>' .
                    '<w:tc><w:tcPr><w:tcW w:w="2200" w:type="dxa"/></w:tcPr><w:p><w:r><w:t>' . htmlspecialchars($principal['name'], ENT_XML1) . '</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="1600" w:type="dxa"/></w:tcPr><w:p><w:r><w:t>' . htmlspecialchars($principal['country'], ENT_XML1) . '</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="1800" w:type="dxa"/></w:tcPr><w:p><w:r><w:t>' . htmlspecialchars($principal['phone'], ENT_XML1) . '</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="2200" w:type="dxa"/></w:tcPr><w:p><w:r><w:t>' . htmlspecialchars($principal['email'], ENT_XML1) . '</w:t></w:r></w:p></w:tc>' .
                    '<w:tc><w:tcPr><w:tcW w:w="1700" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr><w:p><w:pPr><w:jc w:val="center"/></w:pPr>' . ($pLogoImg ? $this->renderDrawingXml($pLogoImg, 'Principal Logo') : '<w:r><w:t></w:t></w:r>') . '</w:p></w:tc>' .
                '</w:tr>';
            }
            $xml .= '</w:tbl>';
        }

        // 5. Footer Banner
        $xml .= '<w:p><w:pPr><w:spacing w:before="280" w:after="80"/></w:pPr></w:p>';
        $xml .= '<w:tbl>' .
            '<w:tblPr>' .
                '<w:tblW w:w="9500" w:type="dxa"/>' .
                '<w:tblBorders><w:top w:val="none"/><w:left w:val="none"/><w:bottom w:val="none"/><w:right w:val="none"/><w:insideH w:val="none"/><w:insideV w:val="none"/></w:tblBorders>' .
                '<w:tblCellMar><w:top w:w="80" w:type="dxa"/><w:bottom w:w="80" w:type="dxa"/><w:left w:w="80" w:type="dxa"/><w:right w:w="80" w:type="dxa"/></w:tblCellMar>' .
            '</w:tblPr>' .
            '<w:tr>' .
                '<w:tc><w:tcPr><w:tcW w:w="3800" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="' . $eventColor . '"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>' .
                '<w:tc><w:tcPr><w:tcW w:w="1000" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:jc w:val="center"/></w:pPr>' . ($associateLogoImg ? $this->renderDrawingXml($associateLogoImg, 'Associate Logo') : '<w:r><w:t></w:t></w:r>') . '</w:p></w:tc>' .
                '<w:tc><w:tcPr><w:tcW w:w="1000" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:jc w:val="center"/></w:pPr>' . ($organizerLogoImg ? $this->renderDrawingXml($organizerLogoImg, 'Organizer Logo') : '<w:r><w:t></w:t></w:r>') . '</w:p></w:tc>' .
                '<w:tc><w:tcPr><w:tcW w:w="3700" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="' . $eventColor . '"/></w:tcPr><w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:color w:val="FFFFFF"/><w:b/><w:sz w:val="18"/></w:rPr><w:t>' . htmlspecialchars($payload['organizer_url'], ENT_XML1) . '</w:t></w:r></w:p></w:tc>' .
            '</w:tr>' .
        '</w:tbl>';

        // Section properties (A4 Portrait, 0.5in margins)
        $xml .= '<w:sectPr>' .
            '<w:pgSz w:w="11906" w:h="16838"/>' .
            '<w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720" w:header="720" w:footer="720" w:gutter="0"/>' .
        '</w:sectPr>';

        $xml .= '</w:body></w:document>';
        return $xml;
    }

    private function buildInfoParagraph($label, $value)
    {
        if (empty($value)) {
            $value = '-';
        }
        return '<w:p><w:pPr><w:spacing w:after="40"/></w:pPr>' .
            '<w:r><w:rPr><w:b/><w:sz w:val="20"/><w:color w:val="555555"/></w:rPr><w:t xml:space="preserve">' . htmlspecialchars($label, ENT_XML1) . ':  </w:t></w:r>' .
            '<w:r><w:rPr><w:sz w:val="20"/><w:color w:val="222222"/></w:rPr><w:t>' . htmlspecialchars($value, ENT_XML1) . '</w:t></w:r>' .
        '</w:p>';
    }

    private function getContentTypesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Default Extension="png" ContentType="image/png"/>' .
            '<Default Extension="jpg" ContentType="image/jpeg"/>' .
            '<Default Extension="jpeg" ContentType="image/jpeg"/>' .
            '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' .
            '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>' .
            '<Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>' .
        '</Types>';
    }

    private function getPackageRelsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>' .
        '</Relationships>';
    }

    private function getDocumentRelsXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
            '<Relationship Id="rIdSettings" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>';

        foreach ($this->imageRels as $rel) {
            $xml .= '<Relationship Id="' . $rel['relId'] . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="' . $rel['target'] . '"/>';
        }

        $xml .= '</Relationships>';
        return $xml;
    }

    private function getSettingsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
            '<w:defaultTabStop w:val="720"/>' .
        '</w:settings>';
    }

    private function getStylesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
            '<w:docDefaults>' .
                '<w:rPrDefault>' .
                    '<w:rPr>' .
                        '<w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/>' .
                        '<w:sz w:val="20"/>' .
                    '</w:rPr>' .
                '</w:rPrDefault>' .
            '</w:docDefaults>' .
        '</w:styles>';
    }
}
