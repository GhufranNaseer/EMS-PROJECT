<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/ZipPacker.php';

/**
 * Catalogue_pptx
 * High-performance, self-contained OpenXML PowerPoint (.pptx) generator for EMS Show Catalogue reports.
 * Complies with Microsoft Office PresentationML (ECMA-376) standards.
 */
class Catalogue_pptx
{
    private $CI;
    private $images = array();
    private $imageRels = array();
    private $slide1Rels = array();
    private $slide2Rels = array();
    private $relCounter = 10;

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->CI =& get_instance();
        }
    }

    /**
     * Generate and stream the PPTX file to the browser.
     *
     * @param array $payload Structured catalogue data
     */
    public function export($payload)
    {
        $pptxBinary = $this->buildPptx($payload);
        $companyName = !empty($payload['company']) ? $payload['company'] : 'Exhibitor';
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $companyName);
        $filename = 'Catalogue_' . $safeName . '_' . date('Ymd_His') . '.pptx';

        if (ob_get_length()) {
            @ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . strlen($pptxBinary));

        echo $pptxBinary;
        exit;
    }

    /**
     * Build the PPTX binary package.
     */
    public function buildPptx($payload)
    {
        $packer = new ZipPacker();
        $this->images = array();
        $this->imageRels = array();
        $this->slide1Rels = array();
        $this->slide2Rels = array();
        $this->relCounter = 10;

        $hasSlide2 = (!empty($payload['principals_active']) && !empty($payload['principals']));

        // 1. [Content_Types].xml
        $packer->addFile('[Content_Types].xml', $this->getContentTypesXml($hasSlide2));

        // 2. _rels/.rels
        $packer->addFile('_rels/.rels', $this->getPackageRelsXml());

        // 3. ppt/_rels/presentation.xml.rels
        $packer->addFile('ppt/_rels/presentation.xml.rels', $this->getPresentationRelsXml($hasSlide2));

        // 4. ppt/presentation.xml
        $packer->addFile('ppt/presentation.xml', $this->getPresentationXml($hasSlide2));

        // 5. Theme & SlideMaster & Layout
        $packer->addFile('ppt/theme/theme1.xml', $this->getThemeXml());
        $packer->addFile('ppt/slideMasters/slideMaster1.xml', $this->getSlideMasterXml());
        $packer->addFile('ppt/slideMasters/_rels/slideMaster1.xml.rels', $this->getSlideMasterRelsXml());
        $packer->addFile('ppt/slideLayouts/slideLayout1.xml', $this->getSlideLayoutXml());
        $packer->addFile('ppt/slideLayouts/_rels/slideLayout1.xml.rels', $this->getSlideLayoutRelsXml());

        // 6. Slides
        $slide1Xml = $this->getSlide1Xml($payload);
        $packer->addFile('ppt/slides/slide1.xml', $slide1Xml);
        $packer->addFile('ppt/slides/_rels/slide1.xml.rels', $this->getSlideRelsXml($this->slide1Rels));

        if ($hasSlide2) {
            $slide2Xml = $this->getSlide2Xml($payload);
            $packer->addFile('ppt/slides/slide2.xml', $slide2Xml);
            $packer->addFile('ppt/slides/_rels/slide2.xml.rels', $this->getSlideRelsXml($this->slide2Rels));
        }

        // Add media files
        foreach ($this->images as $rel) {
            $packer->addFile('ppt/' . $rel['target'], $rel['data']);
        }

        return $packer->getZipContent();
    }

    private function registerSlideImage($filePath, &$slideRels, $maxWidth = 200, $maxHeight = 80)
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

        // 1 pixel = 9525 EMU
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

        $slideRels[] = array(
            'relId'  => $relId,
            'target' => '../' . $mediaTarget,
        );

        return array(
            'relId' => $relId,
            'cx'    => $cx,
            'cy'    => $cy,
        );
    }

    private function renderPicShape($imgInfo, $id, $name, $offX, $offY)
    {
        if (!$imgInfo) {
            return '';
        }
        $relId = $imgInfo['relId'];
        $cx = $imgInfo['cx'];
        $cy = $imgInfo['cy'];

        return '<p:pic>' .
            '<p:nvPicPr>' .
                '<p:cNvPr id="' . $id . '" name="' . htmlspecialchars($name, ENT_XML1) . '"/>' .
                '<p:cNvPicPr><a:picLocks noChangeAspect="1"/></p:cNvPicPr>' .
                '<p:nvPr/>' .
            '</p:nvPicPr>' .
            '<p:blipFill>' .
                '<a:blip r:embed="' . $relId . '"/>' .
                '<a:stretch><a:fillRect/></a:stretch>' .
            '</p:blipFill>' .
            '<p:spPr>' .
                '<a:xfrm><a:off x="' . $offX . '" y="' . $offY . '"/><a:ext cx="' . $cx . '" cy="' . $cy . '"/></a:xfrm>' .
                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
            '</p:spPr>' .
        '</p:pic>';
    }

    private function getSlide1Xml($payload)
    {
        $eventColor = !empty($payload['event_color']) ? ltrim($payload['event_color'], '#') : '1F497D';
        if (strlen($eventColor) !== 6) {
            $eventColor = '1F497D';
        }

        // Register images for Slide 1
        $eventLogoImg = $this->registerSlideImage($payload['event_logo_path'], $this->slide1Rels, 180, 65);
        $companyLogoImg = $this->registerSlideImage($payload['company_logo_path'], $this->slide1Rels, 250, 110);
        $associateLogoImg = $this->registerSlideImage($payload['associate_logo_path'], $this->slide1Rels, 90, 45);
        $organizerLogoImg = $this->registerSlideImage($payload['organizer_logo_path'], $this->slide1Rels, 90, 45);

        // Slide width: 12192000, height: 6858000
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ' .
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
            'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">' .
        '<p:cSld>' .
            '<p:spTree>' .
                '<p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr>' .
                '<p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr>';

        // 1. Top Header Banner Shape
        $xml .= '<p:sp>' .
            '<p:nvSpPr><p:cNvPr id="2" name="Header Banner"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
            '<p:spPr>' .
                '<a:xfrm><a:off x="0" y="0"/><a:ext cx="12192000" cy="762000"/></a:xfrm>' .
                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                '<a:solidFill><a:srgbClr val="' . $eventColor . '"/></a:solidFill>' .
            '</p:spPr>' .
            '<p:txBody>' .
                '<a:bodyPr vert="horz" lIns="360000" tIns="140000" anchor="ctr"/>' .
                '<a:lstStyle/>' .
                '<a:p><a:r><a:rPr lang="en-US" sz="2200" b="1"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($payload['exhibition_title'], ENT_XML1) . ' - SHOW CATALOGUE</a:t></a:r></a:p>' .
            '</p:txBody>' .
        '</p:sp>';

        // Event Logo in Header
        if ($eventLogoImg) {
            $xml .= $this->renderPicShape($eventLogoImg, 3, 'Event Logo', 10200000, 70000);
        }

        // 2. Company Title Block
        $xml .= '<p:sp>' .
            '<p:nvSpPr><p:cNvPr id="4" name="Company Title"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
            '<p:spPr>' .
                '<a:xfrm><a:off x="500000" y="900000"/><a:ext cx="7500000" cy="650000"/></a:xfrm>' .
                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
            '</p:spPr>' .
            '<p:txBody>' .
                '<a:bodyPr vert="horz" lIns="0" tIns="0" anchor="t"/>' .
                '<a:lstStyle/>' .
                '<a:p><a:r><a:rPr lang="en-US" sz="2800" b="1"><a:solidFill><a:srgbClr val="' . $eventColor . '"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($payload['company'], ENT_XML1) . '</a:t></a:r></a:p>' .
            '</p:txBody>' .
        '</p:sp>';

        // 3. Company Logo (Top Right)
        if ($companyLogoImg) {
            $xml .= $this->renderPicShape($companyLogoImg, 5, 'Company Logo', 8500000, 950000);
        }

        // 4. Contact Details Block (Left Column)
        $detailsText = array(
            'Address'        => $payload['address'],
            'Country'        => $payload['country'],
            'Telephone'      => $payload['telephone'],
            'Email'          => $payload['email'],
            'Website'        => $payload['website'],
            'Contact Person' => $payload['contact_name'] . (!empty($payload['contact_designation']) ? ' (' . $payload['contact_designation'] . ')' : ''),
        );

        $xml .= '<p:sp>' .
            '<p:nvSpPr><p:cNvPr id="6" name="Contact Details"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
            '<p:spPr>' .
                '<a:xfrm><a:off x="500000" y="1650000"/><a:ext cx="7500000" cy="1850000"/></a:xfrm>' .
                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
            '</p:spPr>' .
            '<p:txBody>' .
                '<a:bodyPr vert="horz" lIns="0" tIns="0" anchor="t"/>' .
                '<a:lstStyle/>';

        foreach ($detailsText as $label => $val) {
            if (empty($val)) $val = '-';
            $xml .= '<a:p>' .
                '<a:pPr marL="0" indent="0"/>' .
                '<a:r><a:rPr lang="en-US" sz="1300" b="1"><a:solidFill><a:srgbClr val="555555"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($label, ENT_XML1) . ': </a:t></a:r>' .
                '<a:r><a:rPr lang="en-US" sz="1300"><a:solidFill><a:srgbClr val="111111"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($val, ENT_XML1) . '</a:t></a:r>' .
            '</a:p>';
        }

        $xml .= '</p:txBody></p:sp>';

        // 5. Company Profile Narrative (Middle/Lower)
        if (!empty($payload['profile'])) {
            $cleanProfile = strip_tags(html_entity_decode($payload['profile'], ENT_QUOTES, 'UTF-8'));
            if (mb_strlen($cleanProfile) > 650) {
                $cleanProfile = mb_substr($cleanProfile, 0, 650) . '...';
            }

            $xml .= '<p:sp>' .
                '<p:nvSpPr><p:cNvPr id="7" name="Profile Header"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
                '<p:spPr>' .
                    '<a:xfrm><a:off x="500000" y="3650000"/><a:ext cx="11192000" cy="400000"/></a:xfrm>' .
                    '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                '</p:spPr>' .
                '<p:txBody>' .
                    '<a:bodyPr vert="horz" lIns="0" tIns="0" anchor="t"/>' .
                    '<a:lstStyle/>' .
                    '<a:p><a:r><a:rPr lang="en-US" sz="1600" b="1"><a:solidFill><a:srgbClr val="' . $eventColor . '"/></a:solidFill></a:rPr><a:t>Company Profile</a:t></a:r></a:p>' .
                '</p:txBody>' .
            '</p:sp>';

            $xml .= '<p:sp>' .
                '<p:nvSpPr><p:cNvPr id="8" name="Profile Body"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
                '<p:spPr>' .
                    '<a:xfrm><a:off x="500000" y="4150000"/><a:ext cx="11192000" cy="1800000"/></a:xfrm>' .
                    '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                '</p:spPr>' .
                '<p:txBody>' .
                    '<a:bodyPr vert="horz" lIns="0" tIns="0" anchor="t"/>' .
                    '<a:lstStyle/>' .
                    '<a:p><a:r><a:rPr lang="en-US" sz="1300"><a:solidFill><a:srgbClr val="333333"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($cleanProfile, ENT_XML1) . '</a:t></a:r></a:p>' .
                '</p:txBody>' .
            '</p:sp>';
        }

        // 6. Footer Banner Shape
        $xml .= '<p:sp>' .
            '<p:nvSpPr><p:cNvPr id="9" name="Footer Banner"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
            '<p:spPr>' .
                '<a:xfrm><a:off x="0" y="6250000"/><a:ext cx="12192000" cy="608000"/></a:xfrm>' .
                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                '<a:solidFill><a:srgbClr val="' . $eventColor . '"/></a:solidFill>' .
            '</p:spPr>' .
            '<p:txBody>' .
                '<a:bodyPr vert="horz" lIns="360000" tIns="120000" anchor="ctr"/>' .
                '<a:lstStyle/>' .
                '<a:p><a:pPr algn="ctr"/><a:r><a:rPr lang="en-US" sz="1300" b="1"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($payload['organizer_url'], ENT_XML1) . '</a:t></a:r></a:p>' .
            '</p:txBody>' .
        '</p:sp>';

        if ($associateLogoImg) {
            $xml .= $this->renderPicShape($associateLogoImg, 10, 'Associate Logo', 500000, 6300000);
        }
        if ($organizerLogoImg) {
            $xml .= $this->renderPicShape($organizerLogoImg, 11, 'Organizer Logo', 1700000, 6300000);
        }

        $xml .= '</p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sld>';
        return $xml;
    }

    private function getSlide2Xml($payload)
    {
        $eventColor = !empty($payload['event_color']) ? ltrim($payload['event_color'], '#') : '1F497D';
        if (strlen($eventColor) !== 6) {
            $eventColor = '1F497D';
        }

        // Register images for Slide 2
        $eventLogoImg = $this->registerSlideImage($payload['event_logo_path'], $this->slide2Rels, 180, 65);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ' .
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
            'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">' .
        '<p:cSld>' .
            '<p:spTree>' .
                '<p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr>' .
                '<p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr>';

        // 1. Header Banner
        $xml .= '<p:sp>' .
            '<p:nvSpPr><p:cNvPr id="2" name="Header Banner"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
            '<p:spPr>' .
                '<a:xfrm><a:off x="0" y="0"/><a:ext cx="12192000" cy="762000"/></a:xfrm>' .
                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                '<a:solidFill><a:srgbClr val="' . $eventColor . '"/></a:solidFill>' .
            '</p:spPr>' .
            '<p:txBody>' .
                '<a:bodyPr vert="horz" lIns="360000" tIns="140000" anchor="ctr"/>' .
                '<a:lstStyle/>' .
                '<a:p><a:r><a:rPr lang="en-US" sz="2200" b="1"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($payload['company'], ENT_XML1) . ' - COMPANY PRINCIPALS</a:t></a:r></a:p>' .
            '</p:txBody>' .
        '</p:sp>';

        if ($eventLogoImg) {
            $xml .= $this->renderPicShape($eventLogoImg, 3, 'Event Logo', 10200000, 70000);
        }

        // 2. Principals Table Shape
        $rowHeight = 500000;
        $totalRows = count($payload['principals']) + 1;
        $tableHeight = min(5000000, $totalRows * $rowHeight);

        $xml .= '<p:graphicFrame>' .
            '<p:nvGraphicFramePr>' .
                '<p:cNvPr id="4" name="Principals Table"/>' .
                '<p:cNvGraphicFramePr><a:graphicFrameLocks noGrp="1"/></p:cNvGraphicFramePr>' .
                '<p:nvPr/>' .
            '</p:nvGraphicFramePr>' .
            '<p:xfrm><a:off x="600000" y="1100000"/><a:ext cx="10992000" cy="' . $tableHeight . '"/></p:xfrm>' .
            '<a:graphic>' .
                '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/table">' .
                    '<a:tbl>' .
                        '<a:tblPr rtl="0" firstRow="1">' .
                            '<a:tableStyleId>{5C22544A-7EE6-4342-B048-85BDC9FD1C3A}</a:tableStyleId>' .
                        '</a:tblPr>' .
                        '<a:tblGrid>' .
                            '<a:gridCol w="2800000"/>' .
                            '<a:gridCol w="2200000"/>' .
                            '<a:gridCol w="2500000"/>' .
                            '<a:gridCol w="3492000"/>' .
                        '</a:tblGrid>' .
                        // Header Row
                        '<a:tr h="450000">' .
                            $this->renderTableCell('Company', true, $eventColor) .
                            $this->renderTableCell('Country', true, $eventColor) .
                            $this->renderTableCell('Phone', true, $eventColor) .
                            $this->renderTableCell('Email', true, $eventColor) .
                        '</a:tr>';

        foreach ($payload['principals'] as $idx => $principal) {
            $xml .= '<a:tr h="400000">' .
                $this->renderTableCell($principal['name']) .
                $this->renderTableCell($principal['country']) .
                $this->renderTableCell($principal['phone']) .
                $this->renderTableCell($principal['email']) .
            '</a:tr>';
        }

        $xml .= '</a:tbl></a:graphicData></a:graphic></p:graphicFrame>';

        // Footer Banner
        $xml .= '<p:sp>' .
            '<p:nvSpPr><p:cNvPr id="5" name="Footer Banner"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr>' .
            '<p:spPr>' .
                '<a:xfrm><a:off x="0" y="6250000"/><a:ext cx="12192000" cy="608000"/></a:xfrm>' .
                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                '<a:solidFill><a:srgbClr val="' . $eventColor . '"/></a:solidFill>' .
            '</p:spPr>' .
            '<p:txBody>' .
                '<a:bodyPr vert="horz" lIns="360000" tIns="120000" anchor="ctr"/>' .
                '<a:lstStyle/>' .
                '<a:p><a:pPr algn="ctr"/><a:r><a:rPr lang="en-US" sz="1300" b="1"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($payload['organizer_url'], ENT_XML1) . '</a:t></a:r></a:p>' .
            '</p:txBody>' .
        '</p:sp>';

        $xml .= '</p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sld>';
        return $xml;
    }

    private function renderTableCell($text, $isHeader = false, $headerColor = '1F497D')
    {
        $fill = $isHeader ? '<a:solidFill><a:srgbClr val="' . $headerColor . '"/></a:solidFill>' : '<a:noFill/>';
        $textColor = $isHeader ? 'FFFFFF' : '222222';
        $bold = $isHeader ? ' b="1"' : '';
        $sz = $isHeader ? '1400' : '1200';

        return '<a:tc>' .
            '<a:txBody>' .
                '<a:bodyPr vert="horz" anchor="ctr"/>' .
                '<a:lstStyle/>' .
                '<a:p><a:r><a:rPr lang="en-US" sz="' . $sz . '"' . $bold . '><a:solidFill><a:srgbClr val="' . $textColor . '"/></a:solidFill></a:rPr><a:t>' . htmlspecialchars($text, ENT_XML1) . '</a:t></a:r></a:p>' .
            '</a:txBody>' .
            '<a:tcPr>' . $fill . '</a:tcPr>' .
        '</a:tc>';
    }

    private function getContentTypesXml($hasSlide2 = false)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Default Extension="png" ContentType="image/png"/>' .
            '<Default Extension="jpg" ContentType="image/jpeg"/>' .
            '<Default Extension="jpeg" ContentType="image/jpeg"/>' .
            '<Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/>' .
            '<Override PartName="/ppt/slideMasters/slideMaster1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml"/>' .
            '<Override PartName="/ppt/slideLayouts/slideLayout1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml"/>' .
            '<Override PartName="/ppt/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>' .
            '<Override PartName="/ppt/slides/slide1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"/>';

        if ($hasSlide2) {
            $xml .= '<Override PartName="/ppt/slides/slide2.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"/>';
        }

        $xml .= '</Types>';
        return $xml;
    }

    private function getPackageRelsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/>' .
        '</Relationships>';
    }

    private function getPresentationRelsXml($hasSlide2 = false)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rIdMaster1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="slideMasters/slideMaster1.xml"/>' .
            '<Relationship Id="rIdSlide1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide" Target="slides/slide1.xml"/>';

        if ($hasSlide2) {
            $xml .= '<Relationship Id="rIdSlide2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide" Target="slides/slide2.xml"/>';
        }

        $xml .= '</Relationships>';
        return $xml;
    }

    private function getPresentationXml($hasSlide2 = false)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<p:presentation xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ' .
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
            'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">' .
            '<p:sldMasterIdLst><p:sldMasterId id="2147483648" r:id="rIdMaster1"/></p:sldMasterIdLst>' .
            '<p:sldIdLst>' .
                '<p:sldId id="256" r:id="rIdSlide1"/>';

        if ($hasSlide2) {
            $xml .= '<p:sldId id="257" r:id="rIdSlide2"/>';
        }

        $xml .= '</p:sldIdLst>' .
            '<p:sldSz cx="12192000" cy="6858000" type="screen16x9"/>' .
            '<p:notesSz cx="6858000" cy="9144000"/>' .
        '</p:presentation>';

        return $xml;
    }

    private function getSlideRelsXml($slideRels)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rIdLayout1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>';

        foreach ($slideRels as $rel) {
            $xml .= '<Relationship Id="' . $rel['relId'] . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="' . $rel['target'] . '"/>';
        }

        $xml .= '</Relationships>';
        return $xml;
    }

    private function getSlideMasterRelsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rIdLayout1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/>' .
            '<Relationship Id="rIdTheme1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="../theme/theme1.xml"/>' .
        '</Relationships>';
    }

    private function getSlideLayoutRelsXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rIdMaster1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="../slideMasters/slideMaster1.xml"/>' .
        '</Relationships>';
    }

    private function getSlideMasterXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<p:sldMaster xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ' .
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
            'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">' .
            '<p:cSld><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr></p:spTree></p:cSld>' .
            '<p:clrMap bg1="lt1" tx1="dk1" bg2="lt2" tx2="dk2" accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" hlink="hlink" folHlink="folHlink"/>' .
            '<p:sldLayoutIdLst><p:sldLayoutId id="2147483649" r:id="rIdLayout1"/></p:sldLayoutIdLst>' .
        '</p:sldMaster>';
    }

    private function getSlideLayoutXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<p:sldLayout xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ' .
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
            'xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" type="blank">' .
            '<p:cSld name="Blank"><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr></p:spTree></p:cSld>' .
            '<p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr>' .
        '</p:sldLayout>';
    }

    private function getThemeXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Office Theme">' .
            '<a:themeElements>' .
                '<a:clrScheme name="Office">' .
                    '<a:dk1><a:sysClr val="windowText" lastClr="000000"/></a:dk1>' .
                    '<a:lt1><a:sysClr val="window" lastClr="FFFFFF"/></a:lt1>' .
                    '<a:dk2><a:srgbClr val="1F497D"/></a:dk2>' .
                    '<a:lt2><a:srgbClr val="EEECE1"/></a:lt2>' .
                    '<a:accent1><a:srgbClr val="4F81BD"/></a:accent1>' .
                    '<a:accent2><a:srgbClr val="C0504D"/></a:accent2>' .
                    '<a:accent3><a:srgbClr val="9BBB59"/></a:accent3>' .
                    '<a:accent4><a:srgbClr val="8064A2"/></a:accent4>' .
                    '<a:accent5><a:srgbClr val="4BACC6"/></a:accent5>' .
                    '<a:accent6><a:srgbClr val="F79646"/></a:accent6>' .
                    '<a:hlink><a:srgbClr val="0000FF"/></a:hlink>' .
                    '<a:folHlink><a:srgbClr val="800080"/></a:folHlink>' .
                '</a:clrScheme>' .
                '<a:fontScheme name="Office">' .
                    '<a:majorFont><a:latin typeface="Calibri"/></a:majorFont>' .
                    '<a:minorFont><a:latin typeface="Calibri"/></a:minorFont>' .
                '</a:fontScheme>' .
                '<a:fmtScheme name="Office">' .
                    '<a:fillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:fillStyleLst>' .
                    '<a:lnStyleLst><a:ln w="9525"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:ln><a:ln w="9525"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:ln><a:ln w="9525"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:ln></a:lnStyleLst>' .
                    '<a:effectStyleLst><a:effectStyle><a:effectLst/></a:effectStyle><a:effectStyle><a:effectLst/></a:effectStyle><a:effectStyle><a:effectLst/></a:effectStyle></a:effectStyleLst>' .
                    '<a:bgFillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:bgFillStyleLst>' .
                '</a:fmtScheme>' .
            '</a:themeElements>' .
        '</a:theme>';
    }
}
