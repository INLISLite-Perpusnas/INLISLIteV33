<?php


namespace Oai\Controllers;
use Base\Controllers\BaseController;
use Katalog\Models\KatalogModel;
use Katalog\Models\KatalogRuasModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class Oai extends \Base\Controllers\BaseController
{
    protected $catalogModel;
    protected $repositoryName;
    protected $adminEmail;
    protected $baseURL;
    protected $earliestDatestamp;
    protected $granularity;
    protected $settingModel;
    
    public function __construct()
    {
        $this->catalogModel = new \Katalog\Models\KatalogModel();
        $this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
        // Konfigurasi OAI-PMH Repository
        $this->repositoryName = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
        $this->adminEmail = $this->settingModel->where('Name', 'EmailPerpustakaan')->first()->Value ?? 'email@perpustakaan.mitra';
        $this->baseURL = base_url('oai-pmh');
        $this->earliestDatestamp = "2020-01-01T00:00:00Z";
        $this->granularity = "YYYY-MM-DDThh:mm:ssZ";
    }
    
    public function index()
    {
        // Set header untuk XML
        $this->response->setContentType('application/xml');
        
        $verb = $this->request->getVar('verb');
        $identifier = $this->request->getVar('identifier');
        $metadataPrefix = $this->request->getVar('metadataPrefix');
        $from = $this->request->getVar('from');
        $until = $this->request->getVar('until');
        $set = $this->request->getVar('set');
        $resumptionToken = $this->request->getVar('resumptionToken');
        
        // Validasi verb
        $validVerbs = ['Identify', 'ListMetadataFormats', 'ListSets', 'ListIdentifiers', 'ListRecords', 'GetRecord'];
        
        if (empty($verb)) {
            return $this->generateError('badVerb', 'Missing verb argument');
        }
        
        if (!in_array($verb, $validVerbs)) {
            return $this->generateError('badVerb', 'Illegal verb argument');
        }
        
        // Route ke method yang sesuai
        switch ($verb) {
            case 'Identify':
                return $this->identify();
            case 'ListMetadataFormats':
                return $this->listMetadataFormats($identifier);
            case 'ListSets':
                return $this->listSets($resumptionToken);
            case 'ListIdentifiers':
                return $this->listIdentifiers($metadataPrefix, $from, $until, $set, $resumptionToken);
            case 'ListRecords':
                return $this->listRecords($metadataPrefix, $from, $until, $set, $resumptionToken);
            case 'GetRecord':
                return $this->getRecord($identifier, $metadataPrefix);
            default:
                return $this->generateError('badVerb', 'Illegal verb argument');
        }
    }
    
    /**
     * OAI-PMH Identify verb
     */
    private function identify()
    {
        $xml = $this->generateXMLHeader('Identify');
        
        $xml .= '<Identify>';
        $xml .= '<repositoryName>' . htmlspecialchars($this->repositoryName) . '</repositoryName>';
        $xml .= '<baseURL>' . htmlspecialchars($this->baseURL) . '</baseURL>';
        $xml .= '<protocolVersion>2.0</protocolVersion>';
        $xml .= '<adminEmail>' . htmlspecialchars($this->adminEmail) . '</adminEmail>';
        $xml .= '<earliestDatestamp>' . $this->earliestDatestamp . '</earliestDatestamp>';
        $xml .= '<deletedRecord>no</deletedRecord>';
        $xml .= '<granularity>' . $this->granularity . '</granularity>';
        $xml .= '<compression>gzip</compression>';
        $xml .= '<compression>deflate</compression>';
        $xml .= '</Identify>';
        
        $xml .= $this->generateXMLFooter();
        
        return $this->response->setBody($xml);
    }
    
    /**
     * OAI-PMH ListMetadataFormats verb
     */
    private function listMetadataFormats($identifier = null)
    {
        // Validasi identifier jika ada
        if ($identifier && !$this->validateIdentifier($identifier)) {
            return $this->generateError('idDoesNotExist', 'The value of the identifier argument is unknown or illegal');
        }
        
        $xml = $this->generateXMLHeader('ListMetadataFormats');
        
        $xml .= '<ListMetadataFormats>';
        
        // Dublin Core
        $xml .= '<metadataFormat>';
        $xml .= '<metadataPrefix>oai_dc</metadataPrefix>';
        $xml .= '<schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>';
        $xml .= '<metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>';
        $xml .= '</metadataFormat>';
        
        // MARC21
        $xml .= '<metadataFormat>';
        $xml .= '<metadataPrefix>marc21</metadataPrefix>';
        $xml .= '<schema>http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd</schema>';
        $xml .= '<metadataNamespace>http://www.loc.gov/MARC21/slim</metadataNamespace>';
        $xml .= '</metadataFormat>';
        
        $xml .= '</ListMetadataFormats>';
        
        $xml .= $this->generateXMLFooter();
        
        return $this->response->setBody($xml);
    }
    
    /**
     * OAI-PMH ListSets verb
     */
    private function listSets($resumptionToken = null)
    {
        $xml = $this->generateXMLHeader('ListSets');
        
        $xml .= '<ListSets>';
        
        // Set berdasarkan Subject/Dewey Classification
        $sets = [
            ['setSpec' => 'dewey:000', 'setName' => 'Computer Science, Information & General Works'],
            ['setSpec' => 'dewey:100', 'setName' => 'Philosophy & Psychology'],
            ['setSpec' => 'dewey:200', 'setName' => 'Religion'],
            ['setSpec' => 'dewey:300', 'setName' => 'Social Sciences'],
            ['setSpec' => 'dewey:400', 'setName' => 'Language'],
            ['setSpec' => 'dewey:500', 'setName' => 'Science & Mathematics'],
            ['setSpec' => 'dewey:600', 'setName' => 'Technology'],
            ['setSpec' => 'dewey:700', 'setName' => 'Arts & Recreation'],
            ['setSpec' => 'dewey:800', 'setName' => 'Literature'],
            ['setSpec' => 'dewey:900', 'setName' => 'History & Geography'],
        ];
        
        foreach ($sets as $set) {
            $xml .= '<set>';
            $xml .= '<setSpec>' . htmlspecialchars($set['setSpec']) . '</setSpec>';
            $xml .= '<setName>' . htmlspecialchars($set['setName']) . '</setName>';
            $xml .= '</set>';
        }
        
        $xml .= '</ListSets>';
        
        $xml .= $this->generateXMLFooter();
        
        return $this->response->setBody($xml);
    }
    
    /**
     * OAI-PMH ListIdentifiers verb
     */
    private function listIdentifiers($metadataPrefix, $from = null, $until = null, $set = null, $resumptionToken = null)
    {
        if (!$metadataPrefix) {
            return $this->generateError('badArgument', 'Missing required argument metadataPrefix');
        }
        
        if (!in_array($metadataPrefix, ['oai_dc', 'marc21'])) {
            return $this->generateError('cannotDisseminateFormat', 'The metadata format is not supported');
        }
        
        $xml = $this->generateXMLHeader('ListIdentifiers');
        
        $catalogs = $this->getCatalogs($from, $until, $set, $resumptionToken);
        
        $xml .= '<ListIdentifiers>';
        
        foreach ($catalogs['records'] as $catalog) {
            $xml .= '<header>';
            $xml .= '<identifier>oai:' . $_SERVER['HTTP_HOST'] . ':' . $catalog->ID . '</identifier>';
            $xml .= '<datestamp>' . $this->formatDate($catalog->UpdateDate ?: $catalog->CreateDate) . '</datestamp>';
            
            // Set specifications berdasarkan Dewey
            if ($catalog->DeweyNo) {
                $deweyClass = substr($catalog->DeweyNo, 0, 1) . '00';
                $xml .= '<setSpec>dewey:' . $deweyClass . '</setSpec>';
            }
            
            $xml .= '</header>';
        }
        
        // Resumption token jika ada data lebih
        if ($catalogs['hasMore']) {
            $xml .= '<resumptionToken completeListSize="' . $catalogs['totalRecords'] . '" cursor="' . $catalogs['cursor'] . '">';
            $xml .= base64_encode(json_encode([
                'offset' => $catalogs['nextOffset'],
                'metadataPrefix' => $metadataPrefix,
                'from' => $from,
                'until' => $until,
                'set' => $set
            ]));
            $xml .= '</resumptionToken>';
        }
        
        $xml .= '</ListIdentifiers>';
        
        $xml .= $this->generateXMLFooter();
        
        return $this->response->setBody($xml);
    }
    
    /**
     * OAI-PMH ListRecords verb
     */
    private function listRecords($metadataPrefix, $from = null, $until = null, $set = null, $resumptionToken = null)
    {
        if (!$metadataPrefix) {
            return $this->generateError('badArgument', 'Missing required argument metadataPrefix');
        }
        
        if (!in_array($metadataPrefix, ['oai_dc', 'marc21'])) {
            return $this->generateError('cannotDisseminateFormat', 'The metadata format is not supported');
        }
        
        $xml = $this->generateXMLHeader('ListRecords');
        
        $catalogs = $this->getCatalogs($from, $until, $set, $resumptionToken);
        
        $xml .= '<ListRecords>';
        
        foreach ($catalogs['records'] as $catalog) {
            $xml .= '<record>';
            
            // Header
            $xml .= '<header>';
            $xml .= '<identifier>oai:' . $_SERVER['HTTP_HOST'] . ':' . $catalog->ID . '</identifier>';
            $xml .= '<datestamp>' . $this->formatDate($catalog->UpdateDate ?: $catalog->CreateDate) . '</datestamp>';
            
            if ($catalog->DeweyNo) {
                $deweyClass = substr($catalog->DeweyNo, 0, 1) . '00';
                $xml .= '<setSpec>dewey:' . $deweyClass . '</setSpec>';
            }
            
            $xml .= '</header>';
            
            // Metadata
            $xml .= '<metadata>';
            
            if ($metadataPrefix === 'oai_dc') {
                $xml .= $this->generateDublinCore($catalog);
            } elseif ($metadataPrefix === 'marc21') {
                $xml .= $this->generateMARC21($catalog);
            }
            
            $xml .= '</metadata>';
            $xml .= '</record>';
        }
        
        // Resumption token
        if ($catalogs['hasMore']) {
            $xml .= '<resumptionToken completeListSize="' . $catalogs['totalRecords'] . '" cursor="' . $catalogs['cursor'] . '">';
            $xml .= base64_encode(json_encode([
                'offset' => $catalogs['nextOffset'],
                'metadataPrefix' => $metadataPrefix,
                'from' => $from,
                'until' => $until,
                'set' => $set
            ]));
            $xml .= '</resumptionToken>';
        }
        
        $xml .= '</ListRecords>';
        
        $xml .= $this->generateXMLFooter();
        
        return $this->response->setBody($xml);
    }
    
    /**
     * OAI-PMH GetRecord verb
     */
    private function getRecord($identifier, $metadataPrefix)
    {
        if (!$identifier || !$metadataPrefix) {
            return $this->generateError('badArgument', 'Missing required arguments');
        }
        
        if (!in_array($metadataPrefix, ['oai_dc', 'marc21'])) {
            return $this->generateError('cannotDisseminateFormat', 'The metadata format is not supported');
        }
        
        // Extract ID dari identifier
        $id = $this->extractIdFromIdentifier($identifier);
        
        if (!$id) {
            return $this->generateError('badArgument', 'Invalid identifier format');
        }
        
        $catalog = $this->catalogModel->where('ID', $id)
                                    ->where('active', 1)
                                    ->where('IsOPAC', 1)
                                    ->first();
        
        if (!$catalog) {
            return $this->generateError('idDoesNotExist', 'The value of the identifier argument is unknown');
        }
        
        $xml = $this->generateXMLHeader('GetRecord');
        
        $xml .= '<GetRecord>';
        $xml .= '<record>';
        
        // Header
        $xml .= '<header>';
        $xml .= '<identifier>' . htmlspecialchars($identifier) . '</identifier>';
        $xml .= '<datestamp>' . $this->formatDate($catalog->UpdateDate ?: $catalog->CreateDate) . '</datestamp>';
        
        if ($catalog->DeweyNo) {
            $deweyClass = substr($catalog->DeweyNo, 0, 1) . '00';
            $xml .= '<setSpec>dewey:' . $deweyClass . '</setSpec>';
        }
        
        $xml .= '</header>';
        
        // Metadata
        $xml .= '<metadata>';
        
        if ($metadataPrefix === 'oai_dc') {
            $xml .= $this->generateDublinCore($catalog);
        } elseif ($metadataPrefix === 'marc21') {
            $xml .= $this->generateMARC21($catalog);
        }
        
        $xml .= '</metadata>';
        $xml .= '</record>';
        $xml .= '</GetRecord>';
        
        $xml .= $this->generateXMLFooter();
        
        return $this->response->setBody($xml);
    }
    
    /**
     * Generate Dublin Core metadata
     */
    private function generateDublinCore($catalog)
    {
        $xml = '<oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" ';
        $xml .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= 'xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">';
        
        // Title
        if ($catalog->Title) {
            $xml .= '<dc:title>' . htmlspecialchars($catalog->Title) . '</dc:title>';
        }
        
        // Creator/Author
        if ($catalog->Author) {
            $authors = explode(';', $catalog->Author);
            foreach ($authors as $author) {
                $xml .= '<dc:creator>' . htmlspecialchars(trim($author)) . '</dc:creator>';
            }
        }
        
        // Subject
        if ($catalog->Subject) {
            $subjects = explode(';', $catalog->Subject);
            foreach ($subjects as $subject) {
                $xml .= '<dc:subject>' . htmlspecialchars(trim($subject)) . '</dc:subject>';
            }
        }
        
        // Description (Physical Description)
        if ($catalog->PhysicalDescription) {
            $xml .= '<dc:description>' . htmlspecialchars($catalog->PhysicalDescription) . '</dc:description>';
        }
        
        // Publisher
        if ($catalog->Publisher) {
            $xml .= '<dc:publisher>' . htmlspecialchars($catalog->Publisher) . '</dc:publisher>';
        }
        
        // Date
        if ($catalog->PublishYear) {
            $xml .= '<dc:date>' . htmlspecialchars($catalog->PublishYear) . '</dc:date>';
        }
        
        // Type
        $xml .= '<dc:type>Text</dc:type>';
        
        // Format
        $xml .= '<dc:format>application/pdf</dc:format>';
        
        // Identifier
        if ($catalog->ISBN) {
            $isbns = explode(';', $catalog->ISBN);
            foreach ($isbns as $isbn) {
                $xml .= '<dc:identifier>ISBN:' . htmlspecialchars(trim($isbn)) . '</dc:identifier>';
            }
        }
        
        if ($catalog->CallNumber) {
            $xml .= '<dc:identifier>Call Number:' . htmlspecialchars($catalog->CallNumber) . '</dc:identifier>';
        }
        
        // Language
        if ($catalog->Languages) {
            $xml .= '<dc:language>' . htmlspecialchars($catalog->Languages) . '</dc:language>';
        }
        
        // Coverage (Publish Location)
        if ($catalog->PublishLocation) {
            $xml .= '<dc:coverage>' . htmlspecialchars($catalog->PublishLocation) . '</dc:coverage>';
        }
        
        $xml .= '</oai_dc:dc>';
        
        return $xml;
    }
    
    /**
     * Generate MARC21 metadata
     */
    private function generateMARC21($catalog)
    {
        // Jika ada MARC_LOC yang tersimpan, gunakan itu
        if ($catalog->MARC_LOC) {
            return $catalog->MARC_LOC;
        }
        
        // Jika tidak ada, generate MARC21 sederhana
        $xml = '<marc:record xmlns:marc="http://www.loc.gov/MARC21/slim">';
        
        // Leader
        $xml .= '<marc:leader>00000nam a2200000 a 4500</marc:leader>';
        
        // Control fields
        $xml .= '<marc:controlfield tag="001">' . $catalog->ControlNumber . '</marc:controlfield>';
        $xml .= '<marc:controlfield tag="003">' . $_SERVER['HTTP_HOST'] . '</marc:controlfield>';
        
        // Title (245)
        if ($catalog->Title) {
            $xml .= '<marc:datafield tag="245" ind1="1" ind2="0">';
            $xml .= '<marc:subfield code="a">' . htmlspecialchars($catalog->Title) . '</marc:subfield>';
            $xml .= '</marc:datafield>';
        }
        
        // Author (100)
        if ($catalog->Author) {
            $xml .= '<marc:datafield tag="100" ind1="1" ind2=" ">';
            $xml .= '<marc:subfield code="a">' . htmlspecialchars($catalog->Author) . '</marc:subfield>';
            $xml .= '</marc:datafield>';
        }
        
        // Publisher (260)
        if ($catalog->Publisher || $catalog->PublishLocation || $catalog->PublishYear) {
            $xml .= '<marc:datafield tag="260" ind1=" " ind2=" ">';
            if ($catalog->PublishLocation) {
                $xml .= '<marc:subfield code="a">' . htmlspecialchars($catalog->PublishLocation) . '</marc:subfield>';
            }
            if ($catalog->Publisher) {
                $xml .= '<marc:subfield code="b">' . htmlspecialchars($catalog->Publisher) . '</marc:subfield>';
            }
            if ($catalog->PublishYear) {
                $xml .= '<marc:subfield code="c">' . htmlspecialchars($catalog->PublishYear) . '</marc:subfield>';
            }
            $xml .= '</marc:datafield>';
        }
        
        // Physical Description (300)
        if ($catalog->PhysicalDescription) {
            $xml .= '<marc:datafield tag="300" ind1=" " ind2=" ">';
            $xml .= '<marc:subfield code="a">' . htmlspecialchars($catalog->PhysicalDescription) . '</marc:subfield>';
            $xml .= '</marc:datafield>';
        }
        
        // ISBN (020)
        if ($catalog->ISBN) {
            $xml .= '<marc:datafield tag="020" ind1=" " ind2=" ">';
            $xml .= '<marc:subfield code="a">' . htmlspecialchars($catalog->ISBN) . '</marc:subfield>';
            $xml .= '</marc:datafield>';
        }
        
        // Call Number (050)
        if ($catalog->CallNumber) {
            $xml .= '<marc:datafield tag="050" ind1=" " ind2=" ">';
            $xml .= '<marc:subfield code="a">' . htmlspecialchars($catalog->CallNumber) . '</marc:subfield>';
            $xml .= '</marc:datafield>';
        }
        
        // Subject (650)
        if ($catalog->Subject) {
            $subjects = explode(';', $catalog->Subject);
            foreach ($subjects as $subject) {
                $xml .= '<marc:datafield tag="650" ind1=" " ind2="0">';
                $xml .= '<marc:subfield code="a">' . htmlspecialchars(trim($subject)) . '</marc:subfield>';
                $xml .= '</marc:datafield>';
            }
        }
        
        $xml .= '</marc:record>';
        
        return $xml;
    }
    
    /**
     * Get catalogs with filters
     */
    private function getCatalogs($from = null, $until = null, $set = null, $resumptionToken = null)
    {
        $limit = 100; // Records per page
        $offset = 0;
        
        $builder = $this->catalogModel->where('active', 1)
                                    ->where('IsOPAC', 1);
        
        // Handle resumption token
        if ($resumptionToken) {
            $tokenData = json_decode(base64_decode($resumptionToken), true);
            if ($tokenData && isset($tokenData['offset'])) {
                $offset = $tokenData['offset'];
                $from = $tokenData['from'] ?? null;
                $until = $tokenData['until'] ?? null;
                $set = $tokenData['set'] ?? null;
            }
        }
        
        // Date filters
        if ($from) {
            $fromDate = $this->parseDate($from);
            if ($fromDate) {
                $builder->where('UpdateDate >=', $fromDate);
            }
        }
        
        if ($until) {
            $untilDate = $this->parseDate($until);
            if ($untilDate) {
                $builder->where('UpdateDate <=', $untilDate);
            }
        }
        
        // Set filter (Dewey classification)
        if ($set && strpos($set, 'dewey:') === 0) {
            $deweyClass = str_replace('dewey:', '', $set);
            $builder->like('DeweyNo', $deweyClass, 'after');
        }
        
        // Get total count
        $totalRecords = $builder->countAllResults(false);
        
        // Get records with limit and offset
        $records = $builder->limit($limit, $offset)
                          ->orderBy('UpdateDate', 'DESC')
                          ->get()
                          ->getResult();
        
        $hasMore = ($offset + $limit) < $totalRecords;
        $nextOffset = $offset + $limit;
        
        return [
            'records' => $records,
            'totalRecords' => $totalRecords,
            'hasMore' => $hasMore,
            'nextOffset' => $nextOffset,
            'cursor' => $offset
        ];
    }
    
    /**
     * Generate XML header
     */
    private function generateXMLHeader($verb)
    {
        $requestURL = current_url(true);
        $responseDate = gmdate('Y-m-d\TH:i:s\Z');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= 'xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">';
        $xml .= '<responseDate>' . $responseDate . '</responseDate>';
        $xml .= '<request verb="' . htmlspecialchars($verb) . '">' . htmlspecialchars($requestURL) . '</request>';
        
        return $xml;
    }
    
    /**
     * Generate XML footer
     */
    private function generateXMLFooter()
    {
        return '</OAI-PMH>';
    }
    
    /**
     * Generate error response
     */
    private function generateError($code, $message)
    {
        $xml = $this->generateXMLHeader('');
        $xml .= '<error code="' . htmlspecialchars($code) . '">' . htmlspecialchars($message) . '</error>';
        $xml .= $this->generateXMLFooter();
        
        return $this->response->setBody($xml);
    }
    
    /**
     * Validate identifier format
     */
    private function validateIdentifier($identifier)
    {
        return preg_match('/^oai:.*:\d+$/', $identifier);
    }
    
    /**
     * Extract ID from OAI identifier
     */
    private function extractIdFromIdentifier($identifier)
    {
        if (preg_match('/^oai:.*:(\d+)$/', $identifier, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    /**
     * Format date to OAI-PMH format
     */
    private function formatDate($date)
    {
        if (!$date) return null;
        
        $timestamp = is_string($date) ? strtotime($date) : $date;
        return gmdate('Y-m-d\TH:i:s\Z', $timestamp);
    }
    
    /**
     * Parse date from OAI-PMH format
     */
    private function parseDate($date)
    {
        $timestamp = strtotime($date);
        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }
}
?>