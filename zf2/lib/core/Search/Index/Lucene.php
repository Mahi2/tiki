<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use ZendSearch\Lucene;

class Search_Index_Lucene implements Search_Index_Interface
{
	private $lucene;
	private $highlight = true;
	private $cache;
	private $lastModif;
	private $directory;
	private $maxResults = 0;

	function __construct($directory, $lang = 'en', $highlight = true)
	{
		switch($lang) {
		case 'en':
			default:
				Lucene\Analysis\Analyzer\Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
				Lucene\Search\QueryParser::setDefaultEncoding('UTF-8');
		}

		$this->directory = $directory;
		$this->lastModif = file_exists($directory) ? filemtime($directory) : 0;

		$this->highlight = (bool) $highlight;
	}

	private function getLucene()
	{
		if ($this->lucene) {
			return $this->lucene;
		}

		try {
			$this->lucene = Lucene\Lucene::open($this->directory);
		} catch (Lucene\Exception\ExceptionInterface $e) {
			$this->lucene = Lucene\Lucene::create($this->directory);
		}
		global $prefs;
		if (!empty($prefs['unified_lucene_max_buffered_docs'])) {							// these break indexing if set empty
			$this->lucene->setMaxBufferedDocs($prefs['unified_lucene_max_buffered_docs']);	// default is 10
		}
		if (!empty($prefs['unified_lucene_max_merge_docs'])) {
			$this->lucene->setMaxMergeDocs($prefs['unified_lucene_max_merge_docs']);		// default is PHP_INT_MAX (effectively "infinite")
		}
		if (!empty($prefs['unified_lucene_merge_factor'])) {
			$this->lucene->setMergeFactor($prefs['unified_lucene_merge_factor']);			// default is 10
		}

		return $this->lucene;
	}

	function addDocument(array $data)
	{
		$document = $this->generateDocument($data);

		$this->getLucene()->addDocument($document);
	}

	function optimize()
	{
		$this->getLucene()->optimize();
	}

	function invalidateMultiple(Search_Expr_Interface $expr)
	{
		$documents = array();

		$lucene = $this->getLucene();
		$query = $this->buildQuery($expr);
		foreach ($lucene->find($query) as $hit) {
			$document = $hit->getDocument();
			$documents[] = array(
				'object_type' => $document->object_type,
				'object_id' => $document->object_id,
			);
			$lucene->delete($hit->id);
		}

		return $documents;
	}

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder, $resultStart, $resultCount)
	{
		$data = $this->internalFind($query, $sortOrder);

		$result = array_slice($data['result'], $resultStart, $resultCount);

		$resultSet = new Search_ResultSet($result, count($data['result']), $resultStart, $resultCount);
		$resultSet->setEstimate($data['count']);

		if ($this->highlight) {
			$resultSet->setHighlightHelper(new Search_Index_Lucene_HighlightHelper($query));
		} else {
			$resultSet->setHighlightHelper(new Search_ResultSet_SnippetHelper);
		}

		return $resultSet;
	}

	function setCache($cache)
	{
		$this->cache = $cache;
	}

	function setMaxResults($max)
	{
		$this->maxResults = (int) $max;
	}

	public function setResultSetLimit($resultSetLimit)
	{
		ZendSearch\Lucene\Lucene::setResultSetLimit($resultSetLimit);
	}

	public function getResultSetLimit()
	{
		return ZendSearch\Lucene\Lucene::getResultSetLimit();
	}

	private function internalFind(& $query, $sortOrder)
	{
		if ($this->cache) {
			$args = func_get_args();
			$cacheKey = serialize($args);

			$entry = $this->cache->getSerialized($cacheKey, 'searchresult', $this->lastModif);

			if ($entry) {
				$query = $entry['query'];
				return $entry['hits'];
			}
		}

		$query = $this->buildQuery($query);
		try {
			$hits = $this->getLucene()->find($query, $this->getSortField($sortOrder), $this->getSortType($sortOrder), $this->getSortOrder($sortOrder));
		} catch (ZendSearch\Lucene\ExceptionInterface $e) {
			TikiLib::lib('errorreport')->report($e->getMessage());
		}

		$result = array();
		foreach ($hits as $key => $hit) {
			$res = array_merge($this->extractValues($hit->getDocument()), array('relevance' => round($hit->score, 2)));

			$found = false;
			if (!empty($res['object_id']) && !empty($res['object_type'])) {	// filter out duplicates here
				foreach ($result as $r) {
					if ($r['object_id'] === $res['object_id'] && $r['object_type'] === $res['object_type']) {
						$found = true;
						break;
					}
				}
			}
			if (!$found) {
				$result[] = $res;
			}

			if ($this->maxResults && count($result) >= $this->maxResults) {
				break;
			}
		}

		$return = array(
			'result' => $result,
			'count' => count($hits),
		);

		if ($this->cache) {
			$this->cache->cacheItem(
				$cacheKey,
				serialize(
					array(
						'query' => $query,
						'hits' => $return,
					)
				),
				'searchresult'
			);
		}

		return $return;
	}

	private function extractValues($document)
	{
		$data = array();
		foreach ($document->getFieldNames() as $field) {
			$data[$field] = $document->$field;
		}

		return $data;
	}

	private function getSortField($sortOrder)
	{
		return $sortOrder->getField();
	}

	private function getSortType($sortOrder)
	{
		switch ($sortOrder->getMode()) {
		case Search_Query_Order::MODE_NUMERIC:
			return SORT_NUMERIC;
		case Search_Query_Order::MODE_TEXT:
			return SORT_STRING;
		}
	}

	private function getSortOrder($sortOrder)
	{
		switch ($sortOrder->getOrder()) {
		case Search_Query_Order::ORDER_ASC:
			return SORT_ASC;
		case Search_Query_Order::ORDER_DESC:
			return SORT_DESC;
		}
	}

	function getTypeFactory()
	{
		return new Search_Type_Factory_Lucene;
	}

	private function generateDocument($data)
	{
		$document = new Lucene\Document;
		$typeMap = array(
			'Search_Type_WikiText' => 'UnStored',
			'Search_Type_PlainText' => 'UnStored',
			'Search_Type_Whole' => 'Keyword',
			'Search_Type_Timestamp' => 'Keyword',
			'Search_Type_MultivalueText' => 'UnStored',
			'Search_Type_ShortText' => 'Text',
		);
		foreach ($data as $key => $value) {
			$luceneType = $typeMap[get_class($value)];
			$field = Lucene\Document\Field::$luceneType($key, $value->getValue(), 'UTF-8');
			$document->addField($field);
		}

		return $document;
	}

	private function buildQuery($expr)
	{
		$query = (string) $expr->walk(array($this, 'walkCallback'));
		return Lucene\Search\QueryParser::parse($query, 'UTF-8');
	}

	function walkCallback($node, $childNodes)
	{
		$term = null;

		if ($node instanceof Search_Expr_And) {
			$term = $this->buildCondition($childNodes, true);
		} elseif ($node instanceof Search_Expr_Or) {
			$term = $this->buildCondition($childNodes, null);
		} elseif ($node instanceof Search_Expr_Not) {
			$result = new Lucene\Search\Query\Boolean;
			$result->addSubquery($childNodes[0], false);

			$term = $result;
		} elseif ($node instanceof Search_Expr_Range) {
			$from = $node->getToken('from');
			$to = $node->getToken('to');

			$from = $this->buildTerm($from);
			$to = $this->buildTerm($to);

			// Range search not supported for phrases, so revert to normal token matching
			if (method_exists($from, 'getTerm')) {
				$range = new Lucene\Search\Query\Range(
					$from->getTerm(),
					$to->getTerm(),
					true // inclusive
				);

				$term = $range;
			} else {
				$term = $from;
			}
		} elseif ($node instanceof Search_Expr_Token) {
			$term = $this->buildTerm($node);
		}

		if ($term && method_exists($term, 'getTerm') && (string) $term->getTerm()->text) {
			$term->setBoost($node->getWeight());
		}

		return $term;
	}

	private function buildCondition($childNodes, $required)
	{
		$result = new Lucene\Search\Query\Boolean;
		foreach ($childNodes as $child) {

			// Detect if child is a NOT, and reformulate on the fly to support the syntax
			if ($child instanceof Lucene\Search\Query\Boolean) {
				$signs = $child->getSigns();
				if (count($signs) === 1 && $signs[0] === false) {
					$result->addSubquery(reset($child->getSubqueries()), false);
					continue;
				}
			}

			$result->addSubquery($child, $required);
		}

		return $result;
	}

	private function buildTerm($node)
	{
		$value = $node->getValue($this->getTypeFactory());
		$field = $node->getField();

		switch (get_class($value)) {
		case 'Search_Type_WikiText':
		case 'Search_Type_PlainText':
		case 'Search_Type_MultivalueText':
			$whole = $value->getValue();
			$whole = str_replace(array('*', '?', '~', '+'), '', $whole);
			$whole = str_replace(array('[', ']', '{', '}', '(', ')', ':', '-'), ' ', $whole);

			$parts = explode(' ', $whole);
			if (count($parts) === 1) {
				return new Lucene\Search\Query\Term(new Lucene\Index\Term($parts[0], $field), true);
			} else {
				return new Lucene\Search\Query\Phrase($parts, array_keys($parts), $field);
			}
		case 'Search_Type_Timestamp':
			$parts = explode(' ', $value->getValue());
			return new Lucene\Search\Query\Term(new Lucene\Index\Term($parts[0], $field), true);
		case 'Search_Type_Whole':
			$parts = explode(' ', $value->getValue());
			return new Lucene\Search\Query\Phrase($parts, array_keys($parts), $field);
		}
	}
}

class Search_Index_Lucene_HighlightHelper implements Zend\Filter\FilterInterface
{
	private $query;
	private $snippetHelper;

	function __construct($query)
	{
		$qstr = $query->__toString();									// query needs the object_type field removing for highlighting
		$qstr = preg_replace('/\+?\(\(object_type.*?\)\)/', '', $qstr);	// this is the only way i can find to remove a term form a query
		$query = Lucene\Search\QueryParser::parse($qstr, 'UTF-8');	// rebuild
		$this->query = $query;
		$this->snippetHelper = new Search_ResultSet_SnippetHelper;
	}

	function filter($content)
	{
		$content = $this->snippetHelper->filter($content);
		return trim(strip_tags($this->query->highlightMatches($content, 'UTF-8'), '<b><i><em><strong><pre><code><span>'));
	}
}
