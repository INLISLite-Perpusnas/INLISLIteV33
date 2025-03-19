<?php

namespace App\Libraries;

class DataTable
{
	private $queryBuilder;
	private $columnsQuery = [];
	private $columnsAlias = [];
	private $numbering = null;
	private $callbackAddColumns = [];
	private $callbackEditColumns = [];
	private $callbackSumColumns = [];
	private $callbackOnSearchColumns = [];
	private $callbackOnGlobalSearchColumns = [];
	public function __construct(&$builder)
	{
		$this->queryBuilder = (is_subclass_of($builder, '\CodeIgniter\BaseModel') && method_exists($builder, 'builder')) ? ($builder = $builder->builder()) : $builder;
		preg_match('#SELECT[\s]+([\S\s]*)[\s]+FROM#i', $builder->getCompiledSelect(false), $matches);
		if (!empty($matches[1])) {
			foreach (array_map('trim', preg_split('/,(?![^(]*\))/', str_replace('`', '', trim($matches[1])))) as $line) {
				if (($t = count($parts = preg_split('/\bas\b/i', $line, -1, PREG_SPLIT_NO_EMPTY))) > 1) {
					$this->columnsAlias[] = ($alias = trim($parts[$t - 1]));
					if ($t == 2) {
						$this->columnsQuery[$alias] = trim($parts[0]);
					} else {
						$this->columnsQuery[$alias] = substr_replace($line, '', strrpos($line, $alias), strlen($alias));
						if (($pos = strrpos($this->columnsQuery[$alias], ' AS ')) || ($pos = strrpos($this->columnsQuery[$alias], ' as ')) || ($pos = strrpos($this->columnsQuery[$alias], ' As ')) || ($pos = strrpos($this->columnsQuery[$alias], ' aS '))) {
							$this->columnsQuery[$alias] = substr_replace($this->columnsQuery[$alias], '', $pos, 4);
						}
					}
				} else {
					if (($t = count($nextParts = explode('.', $line))) > 1) {
						if (($n = trim($nextParts[$t - 1])) == '*') {
							throw new \Exception("Don't use '*' or for performance reasons!");
						} else if ((bool)preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $n)) {
							$this->columnsAlias[] = $n;
							$this->columnsQuery[$n] = $line;
						}
					} else {
						if ($line == '*') {
							throw new \Exception("Don't use '*' or for performance reasons!");
						} else {
							$this->columnsAlias[] = $line;
							$this->columnsQuery[$line] = $line;
						}
					}
				}
			}
		} else {
			throw new \Exception('No SELECT query!');
		}
		$this->queryBuilder = &$builder;
	}
	public static function of(&$builder)
	{
		return new self($builder);
	}
	public function addNumbering($column = 'no')
	{
		$newColumnsAlias = [$column];
		foreach ($this->columnsAlias as $col) {
			if ($column != $col) {
				$newColumnsAlias[] = $col;
			}
		}
		$this->columnsAlias = $newColumnsAlias;
		$this->numbering = $column;
		return $this;
	}
	public function add($column, $callback, $position = 'last')
	{
		switch ($position) {
			case 'first':
				$newColumnsAlias = [$column];
				foreach ($this->columnsAlias as $col) {
					if ($column != $col) {
						$newColumnsAlias[] = $col;
					}
				}
				$this->columnsAlias = $newColumnsAlias;
				break;
			case 'last':
				$newColumnsAlias = [];
				foreach ($this->columnsAlias as $col) {
					if ($column != $col) {
						$newColumnsAlias[] = $col;
					}
				}
				$newColumnsAlias[] = $column;
				break;
			default:
				$newColumnsAlias = [];
				$position = (int)$position;
				foreach ($this->columnsAlias as $index => $col) {
					if ($index == $position) {
						$newColumnsAlias[] = $column;
					}
					if ($column != $col) {
						$newColumnsAlias[] = $col;
					}
				}

				if (!in_array($column, $newColumnsAlias)) {
					$newColumnsAlias[] = $column;
				}
				$this->columnsAlias = $newColumnsAlias;
				break;
		}
		$this->callbackAddColumns[$column] = $callback;
		return $this;
	}
	public function edit($column, $callback)
	{
		if (!in_array($column, $this->columnsAlias)) {
			$this->columnsAlias[] = $column;
		}
		$this->callbackEditColumns[$column] = $callback;
		return $this;
	}
	public function sum($column, $callback = null)
	{
		$this->callbackSumColumns[$column] = $callback;
		return $this;
	}
	public function onSearch($column, $callback)
	{
		$this->callbackOnSearchColumns[$column] = $callback;
		return $this;
	}
	public function onGlobalSearch($column, $callback)
	{
		$this->callbackOnGlobalSearchColumns[$column] = $callback;
		return $this;
	}
	public function toJson($isJson = true)
	{
		$request = service('request');
		$isObjectColumn = [];
		$availableColumns = [];
		$searchableColumns = [];
		$orderableColumns = [];
		if ($requestColumns = $request->getGetPost('columns')) {
			foreach ($requestColumns as $index => $requestColumn) {
				$colName = null;
				if (isset($requestColumn['data'])) {
					if (isset($this->columnsAlias[$requestColumn['data']])) {
						$colName = $this->columnsAlias[$requestColumn['data']];
						$isObjectColumn[$colName] = false;
					} else if (in_array($requestColumn['data'], $this->columnsAlias)) {
						$colName = $requestColumn['data'];
						$isObjectColumn[$colName] = true;
					} else if (isset($this->columnsQuery[$requestColumn['data']])) {
						$colName = $requestColumn['data'];
						$isObjectColumn[$colName] = true;
					}
				}
				if ($colName == null) {
					throw new \Exception('Column index=' . $index . ' not found!');
				}
				if (isset($requestColumn['searchable']) && $requestColumn['searchable'] == 'true' && isset($this->columnsQuery[$colName])) {
					$searchableColumns[$colName] = isset($requestColumn['search']['value']) ? (trim($requestColumn['search']['value']) == '' ? '' : $requestColumn['search']['value']) : '';
				}
				if (isset($requestColumn['orderable']) && $requestColumn['orderable'] == 'true' && isset($this->columnsQuery[$colName])) {
					$orderableColumns[$colName] = true;
				}
				$availableColumns[] = $colName;
			}
		} else {
			throw new \Exception('Column not found!');
		}

		$countAll = (clone $this->queryBuilder)->countAllResults();
		$isFilter = false;
		foreach ($searchableColumns as $searchColumnName => $searchColumnValue) {
			if ($searchColumnValue != '') {
				$isFilter = true;
				if (isset($this->callbackOnSearchColumns[$searchColumnName])) {
					($this->callbackOnSearchColumns[$searchColumnName])($this->queryBuilder, $this->columnsQuery[$searchColumnName], $searchColumnValue);
				} else {
					$this->queryBuilder->like($this->columnsQuery[$searchColumnName], $searchColumnValue);
				}
			}
		}

		if (($searchGlobalValue = $request->getGetPost('search')) && isset($searchGlobalValue['value']) && (($searchGlobalValue = trim($searchGlobalValue['value'])) != '')) {
			$isFilter = true;
			$this->queryBuilder->groupStart();
			foreach ($searchableColumns as $searchColumnName => $searchColumnValue) {
				if (isset($this->callbackOnGlobalSearchColumns[$searchColumnName])) {
					($this->callbackOnGlobalSearchColumns[$searchColumnName])($this->queryBuilder, $this->columnsQuery[$searchColumnName], $searchGlobalValue);
				} else {
					$this->queryBuilder->orLike($this->columnsQuery[$searchColumnName], $searchGlobalValue);
				}
			}
			$this->queryBuilder->groupEnd();
		}

		$countFiltered = $isFilter ? (clone $this->queryBuilder)->countAllResults() : $countAll;
		$sumQuery = [];
		foreach (array_keys($this->callbackSumColumns) as $colName) {
			if (isset($this->columnsQuery[$colName])) {
				$sumQuery[] = 'SUM(' . $this->columnsQuery[$colName] . ') AS ' . 'sum_' . md5($colName);
			}
		}

		if (!empty($sumQuery)) {
			$selectSum = implode(',', $sumQuery);
			$sumQuery = [];
			foreach ((clone $this->queryBuilder)->select($selectSum)->limit(1)->get()->getResult() as $row) {
				foreach ($this->callbackSumColumns as $colName => $callback) {
					$key = 'sum_' . md5($colName);
					$sumQuery[$colName] = isset($row->$key) ? $row->$key : 0;
					if ($callback != null) {
						$sumQuery[$colName] = $callback($sumQuery[$colName]);
					}
				}
			}
		}

		if ($length = $request->getGetPost('length')) {
			$length = $length < 0 ? 10 : ($length > 100 ? 100 : $length);
		} else {
			$length = 100;
		}

		if ($start = $request->getGetPost('start')) $this->queryBuilder->limit($length, $start = ($start < 0 ? 0 : $start));
		else {
			$start = 0;
			$this->queryBuilder->limit($length);
		}

		if ($requestOrderableColumns = $request->getGetPost('order')) {
			foreach ($requestOrderableColumns as $request) {
				if (isset($orderableColumns[$availableColumns[$request['column']]]) && isset($this->columnsQuery[$orderableColumnName = $availableColumns[$request['column']]])) {
					$this->queryBuilder->orderBy($this->columnsQuery[$orderableColumnName], isset($request['dir']) ? (($request['dir'] == 'desc') ? 'desc' : 'asc') : 'asc', false);
				}
			}
		}

		$rowNumber = 1;
		$queryResult = [];
		foreach ($this->queryBuilder->get()->getResult() as $row) {
			$data = [];
			foreach ($availableColumns as $colName) {
				$colValue = $this->numbering == $colName ? $rowNumber : (isset($row->$colName) ? $row->$colName : null);
				if (isset($this->callbackAddColumns[$colName])) {
					$colValue = ($this->callbackAddColumns[$colName])($row);
				}
				if (isset($this->callbackEditColumns[$colName])) {
					$colValue = ($this->callbackEditColumns[$colName])($row);
				}
				if ($isObjectColumn[$colName]) {
					$data = array_merge($data, [$colName => $colValue]);
				} else {
					$data[] = $colValue;
				}
			}
			$queryResult[] = $data;
			$rowNumber++;
		}

		$response = \Config\Services::response();
		return $response->setJSON([
			'recordsTotal' => $countAll,
			'recordsFiltered' => $countFiltered,
			'data' => $queryResult,
			'sum' => $sumQuery
		]);
	}
	public function toXls($showColumns = [])
	{
		$newShowColumn = [];
		if (is_array($showColumns)) {
			foreach ($showColumns as $col) {
				if (isset($col['data']) && isset($col['name'])) {
					$newShowColumn[$col['data']] = $col['name'];
				} else if (isset($col['data'])) {
					$newShowColumn[$col['data']] = $col['data'];
				} else {
					$newShowColumn[$col] = '';
				}
			}
		} else if (is_string($showColumns)) {
			$showColumns = array_map('trim', explode(',', $showColumns));
			foreach ($showColumns as $col) {
				$newShowColumn[$col] = '';
			}
		}

		$request = service('request');
		$availableColumns = [];
		$searchableColumns = [];
		$orderableColumns = [];
		if ($requestColumns = $request->getGetPost('columns')) {
			foreach ($requestColumns as $index => $requestColumn) {
				$colName = null;
				if (isset($requestColumn['data'])) {
					if (isset($this->columnsAlias[$requestColumn['data']])) {
						$colName = $this->columnsAlias[$requestColumn['data']];
					} else if (in_array($requestColumn['data'], $this->columnsAlias)) {
						$colName = $requestColumn['data'];
					} else if (isset($this->columnsQuery[$requestColumn['data']])) {
						$colName = $requestColumn['data'];
					}
				}
				if ($colName == null) {
					throw new \Exception('Column index=' . $index . ' not found!');
				}
				if (isset($requestColumn['searchable']) && $requestColumn['searchable'] == 'true' && isset($this->columnsQuery[$colName])) {
					$searchableColumns[$colName] = isset($requestColumn['search']['value']) ? (trim($requestColumn['search']['value']) == '' ? '' : $requestColumn['search']['value']) : '';
				}
				if (isset($requestColumn['orderable']) && $requestColumn['orderable'] == 'true' && isset($this->columnsQuery[$colName])) {
					$orderableColumns[$colName] = true;
				}
				$availableColumns[] = $colName;
				if (!empty($showColumns) && isset($showColumns[$colName]) && trim($showColumns[$colName]) == '') {
					if (isset($requestColumn['name']) && trim($requestColumn['name']) != '') {
						$showColumns[$colName] = $requestColumn['name'];
					}
				}
			}
		} else {
			$availableColumns = $this->columnsAlias;
		}

		foreach ($searchableColumns as $searchColumnName => $searchColumnValue) {
			if ($searchColumnValue != '') {
				if (isset($this->callbackOnSearchColumns[$searchColumnName])) {
					($this->callbackOnSearchColumns[$searchColumnName])($this->queryBuilder, $this->columnsQuery[$searchColumnName], $searchColumnValue);
				} else {
					$this->queryBuilder->like($this->columnsQuery[$searchColumnName], $searchColumnValue);
				}
			}
		}

		if (($searchGlobalValue = $request->getGetPost('search')) && isset($searchGlobalValue['value']) && (($searchGlobalValue = trim($searchGlobalValue['value'])) != '')) {
			$this->queryBuilder->groupStart();
			foreach ($searchableColumns as $searchColumnName => $searchColumnValue) {
				if (isset($this->callbackOnGlobalSearchColumns[$searchColumnName])) {
					($this->callbackOnGlobalSearchColumns[$searchColumnName])($this->queryBuilder, $this->columnsQuery[$searchColumnName], $searchGlobalValue);
				} else {
					$this->queryBuilder->orLike($this->columnsQuery[$searchColumnName], $searchGlobalValue);
				}
			}
			$this->queryBuilder->groupEnd();
		}

		if ($requestOrderableColumns = $request->getGetPost('order')) {
			foreach ($requestOrderableColumns as $request) {
				if (isset($orderableColumns[$availableColumns[$request['column']]]) && isset($this->columnsQuery[$orderableColumnName = $availableColumns[$request['column']]])) {
					$this->queryBuilder->orderBy($this->columnsQuery[$orderableColumnName], isset($request['dir']) ? (($request['dir'] == 'desc') ? 'desc' : 'asc') : 'asc');
				}
			}
		}

		$rowNumber = 1;
		$queryResult = [];
		foreach ($this->queryBuilder->get()->getResult() as $row) {
			$data = [];
			foreach ($availableColumns as $colName) {
				$colValue = $this->numbering == $colName ? $rowNumber : (isset($row->$colName) ? $row->$colName : null);
				if (isset($this->callbackAddColumns[$colName])) {
					$colValue = ($this->callbackAddColumns[$colName])($row);
				}
				if (isset($this->callbackEditColumns[$colName])) {
					$colValue = ($this->callbackEditColumns[$colName])($row);
				}
				$data = array_merge($data, [$colName => $colValue]);
			}
			$queryResult[] = $data;
			$rowNumber++;
		}

		$stringToExport = '';
		if (!empty($showColumns)) {
			foreach ($showColumns as $colName => $colNameDisplay) {
				if (trim($colNameDisplay) != '') {
					$stringToExport .= $colNameDisplay . "\t";
				} else {
					$stringToExport .= ucfirst($colName) . "\t";
				}
			}
		} else if ($requestColumns) {
			foreach ($requestColumns as $index => $requestColumn) {
				if (isset($requestColumn['name']) && trim($requestColumn['name']) != '') {
					$stringToExport .= $requestColumn['name'] . "\t";
				} else {
					$stringToExport .= ucfirst($availableColumns[$index]) . "\t";
				}
			}
		}

		$stringToExport .= "\n";
		if (!empty($showColumns)) {
			foreach ($queryResult as $row) {
				foreach ($availableColumns as $colName) {
					if (isset($showColumns[$colName])) {
						$stringToExport .= strip_tags(str_replace(array("\t", "\n", "\r"), "", str_replace(array("&nbsp;", "&amp;", "&gt;", "&lt;"), array(" ", "&", ">", "<"), $row[$colName]))) . "\t";
					}
				}
				$stringToExport .= "\n";
			}
		} else {
			foreach ($queryResult as $row) {
				foreach ($availableColumns as $colName) {
					$stringToExport .= strip_tags(str_replace(array("\t", "\n", "\r"), "", str_replace(array("&nbsp;", "&amp;", "&gt;", "&lt;"), array(" ", "&", ">", "<"), $row[$colName]))) . "\t";
				}
				$stringToExport .= "\n";
			}
		}

		header('Content-type: application/vnd.ms-excel;charset=UTF-16LE');
		header('Content-Disposition: attachment; filename=export-' . date("Y-m-d_H:i:s") . '.xls');
		header('Cache-Control: no-cache');
		echo "\xFF\xFE" . mb_convert_encoding($stringToExport, 'UTF-16LE', 'UTF-8');
		die();
	}

	public function getFromAPI($api, $token)
	{
		$request		    = \Config\Services::request();
		$isObjectColumn     = [];
		$availableColumns   = [];
		$searchableColumns  = [];
		$orderableColumns   = [];
		if ($requestColumns = $request->getGetPost('columns')) {
			foreach ($requestColumns as $index => $requestColumn) {
				$colName = null;
				if (isset($requestColumn['data'])) {
					if (isset($this->columnsAlias[$requestColumn['data']])) {
						$colName = $this->columnsAlias[$requestColumn['data']];
						$isObjectColumn[$colName] = false;
					} else if (in_array($requestColumn['data'], $this->columnsAlias)) {
						$colName = $requestColumn['data'];
						$isObjectColumn[$colName] = true;
					} else if (isset($this->columnsQuery[$requestColumn['data']])) {
						$colName = $requestColumn['data'];
						$isObjectColumn[$colName] = true;
					}
				}
				if ($colName == null) {
					throw new \Exception('Column index=' . $index . ' not found!');
				}
				if (isset($requestColumn['searchable']) && $requestColumn['searchable'] == 'true' && isset($this->columnsQuery[$colName])) {
					$searchableColumns[$colName] = isset($requestColumn['search']['value']) ? (trim($requestColumn['search']['value']) == '' ? '' : $requestColumn['search']['value']) : '';
				}
				if (isset($requestColumn['orderable']) && $requestColumn['orderable'] == 'true' && isset($this->columnsQuery[$colName])) {
					$orderableColumns[$colName] = true;
				}
				$availableColumns[] = $colName;
			}
		} else {
			throw new \Exception('Column not found!');
		}
		$countAllQuery = (clone $this->queryBuilder)->getCompiledSelect(false);

		$isFilter = false;
		foreach ($searchableColumns as $searchColumnName => $searchColumnValue) {
			if ($searchColumnValue != '') {
				$isFilter = true;
				if (isset($this->callbackOnSearchColumns[$searchColumnName])) {
					($this->callbackOnSearchColumns[$searchColumnName])($this->queryBuilder, $this->columnsQuery[$searchColumnName], $searchColumnValue);
				} else {
					$this->queryBuilder->like($this->columnsQuery[$searchColumnName], $searchColumnValue);
				}
			}
		}

		if (($searchGlobalValue = $request->getGetPost('search')) && isset($searchGlobalValue['value']) && (($searchGlobalValue = trim($searchGlobalValue['value'])) != '')) {
			$isFilter = true;
			$this->queryBuilder->groupStart();
			foreach ($searchableColumns as $searchColumnName => $searchColumnValue) {
				if (isset($this->callbackOnGlobalSearchColumns[$searchColumnName])) {
					($this->callbackOnGlobalSearchColumns[$searchColumnName])($this->queryBuilder, $this->columnsQuery[$searchColumnName], $searchGlobalValue);
				} else {
					$this->queryBuilder->orLike($this->columnsQuery[$searchColumnName], $searchGlobalValue);
				}
			}
			$this->queryBuilder->groupEnd();
		}

		$countFilteredQuery = $isFilter ? (clone $this->queryBuilder)->getCompiledSelect(false) : $countAllQuery;
		$sumQuery = [];
		foreach (array_keys($this->callbackSumColumns) as $colName) {
			if (isset($this->columnsQuery[$colName])) {
				$sumQuery[] = 'SUM(' . $this->columnsQuery[$colName] . ') AS ' . 'sum_' . md5($colName);
			}
		}

		if (!empty($sumQuery)) {
			$selectSum = implode(',', $sumQuery);
			$sumQuery = [];
			foreach ((clone $this->queryBuilder)->select($selectSum)->limit(1)->get()->getResult() as $row) {
				foreach ($this->callbackSumColumns as $colName => $callback) {
					$key = 'sum_' . md5($colName);
					$sumQuery[$colName] = isset($row->$key) ? $row->$key : 0;
					if ($callback != null) {
						$sumQuery[$colName] = $callback($sumQuery[$colName]);
					}
				}
			}
		}

		if ($length = $request->getGetPost('length')) {
			$length = $length < 0 ? 10 : ($length > 100 ? 100 : $length);
		} else {
			$length = 100;
		}

		if ($start = $request->getGetPost('start')) $this->queryBuilder->limit($length, $start = ($start < 0 ? 0 : $start));
		else {
			$start = 0;
			$this->queryBuilder->limit($length);
		}

		if ($requestOrderableColumns = $request->getGetPost('order')) {
			foreach ($requestOrderableColumns as $request) {
				if (isset($orderableColumns[$availableColumns[$request['column']]]) && isset($this->columnsQuery[$orderableColumnName = $availableColumns[$request['column']]])) {
					$this->queryBuilder->orderBy($this->columnsQuery[$orderableColumnName], isset($request['dir']) ? (($request['dir'] == 'desc') ? 'desc' : 'asc') : 'asc', false);
				}
			}
		}

		try {
			$result = json_decode(service('curlrequest')->request("post", $api . '/datatable', [
				'form_params' => [
					'countAllQuery'		 => $countAllQuery,
					'countFilteredQuery' => $countFilteredQuery,
					'resultsQuery'	     => $this->queryBuilder->getCompiledSelect(false),
				],
				"headers" => [
					"Accept"        => "application/json",
					"Authorization" => 'Bearer ' . $token
				]
			])->getBody());
			$countAll	   = $result->countAllQuery;
			$countFiltered = $result->countFilteredQuery;
			$results 	   = $result->resultsQuery;
		} catch (\Exception $e) {
			throw $e;
		}

		$rowNumber   = 1;
		$queryResult = [];
		foreach ($results as $row) {
			$data = [];
			foreach ($availableColumns as $colName) {
				$colValue = $this->numbering == $colName ? $rowNumber : (isset($row->$colName) ? $row->$colName : null);
				if (isset($this->callbackAddColumns[$colName])) {
					$colValue = ($this->callbackAddColumns[$colName])($row);
				}
				if (isset($this->callbackEditColumns[$colName])) {
					$colValue = ($this->callbackEditColumns[$colName])($row);
				}
				if ($isObjectColumn[$colName]) {
					$data = array_merge($data, [$colName => $colValue]);
				} else {
					$data[] = $colValue;
				}
			}
			$queryResult[] = $data;
			$rowNumber++;
		}

		$response = \Config\Services::response();
		return $response->setJSON([
			'recordsTotal' => $countAll,
			'recordsFiltered' => $countFiltered,
			'data' => $queryResult,
			'sum' => $sumQuery
		]);
	}
}
