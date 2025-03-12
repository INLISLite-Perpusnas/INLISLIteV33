<?php

namespace Oai\Controllers;
use Base\Controllers\BaseController;
use Katalog\Models\KatalogModel;
use Katalog\Models\KatalogRuasModel;


class Oai extends BaseController
{    public function index()
    {
        $this->response->setStatusCode(200);
        $this->response->setContentType('text/xml');

        // Generate OAI-PMH response
        $xmlResponse = $this->generateOai();

        return $this->response->setBody($xmlResponse);
    }

    private function generateOai()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">';
        $xml .= '<responseDate>' . gmdate('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '<request verb="ListRecords" metadataPrefix="marcxml">'.base_url('oai').'</request>';
        $xml .= '<ListRecords>';

		$catalogModel = new KatalogModel();
		$catalogs = $catalogModel->findAll(100, 0);
		
		foreach ($catalogs as $row) {
			$xml .= $this->generateRecord('oai:'.base_url().':'.$row->ID, $row->ID);
		}

		// $xml .= '<resumptionToken expirationDate="" completeListSize="125" cursor="100">100____marcxml</resumptionToken>';
        $xml .= '</ListRecords>';
        $xml .= '</OAI-PMH>';

        return $xml;
    }

    private function generateRecord($identifier, $recordID = false)
    {
        $xml = '<record>';
        $xml .= '<header>';
        $xml .= '<identifier>' . $identifier . '</identifier>';
        $xml .= '<datestamp/>';
        $xml .= '</header>';
        $xml .= '<metadata>';
        $xml .= '<record xmlns="http://www.loc.gov/MARC21/slim" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">';

		$catalogRuasModel = new KatalogRuasModel();
		$catalogRuas = $catalogRuasModel->where('Catalogid', $recordID)->orderBy('Tag')->findAll();

		foreach ($catalogRuas as $row) {
			$row->Tag = $row->Tag ?? '';
			$row->Value = $row->Value ?? '';
			$row->Indicator1 = $row->Indicator1 ?? '';
			$row->Indicator2 = $row->Indicator2 ?? '';

			if(in_array($row->Tag, ['001', '005', '007'])) {
				$xml .= $this->generateControlField($row);
			} else {
				$xml .= $this->generateDataField($row);
			}
		}

        $xml .= '</record>';
        $xml .= '</metadata>';
        $xml .= '</record>';

        return $xml;
    }

	private function generateControlField($field = null)
    {
		$xml  = '<controlfield tag="'.$field->Tag.'">'.$field->Value.'</controlfield>';

		return $xml;
	}

	private function generateDataField($field = null)
    {
		$xml  = '<datafield tag="'.$field->Tag.'" ind1="'.$field->Indicator1.'" ind2="'.$field->Indicator2.'">';
		$xml .= $this->generateSubField($field->Value);
		$xml .= '</datafield>';

		return $xml;
	}

	private function generateSubField($input){
		preg_match_all('/\$(\w+)\s([^:$]+)/', $input, $matches);
		$xml = "";
		foreach ($matches[1] as $index => $key) {
			$value = trim($matches[2][$index]);
			$xml  .= '<subfield code="' . $key . '">' . $value . '</subfield>';
		}
		return $xml;
	}
}
