<?php

// PHPMaker 5 configuration for Table hoturls
$hoturls = new choturls; // Initialize table object

// Define table class
class choturls {

	// Define table level constants
	var $TableVar;
	var $TableName;
	var $SelectLimit = TRUE;
	var $id;
	var $url;
	var $datetime;
	var $projectname;
	var $content;
	var $title;
	var $categories;
	var $fields = array();

	function choturls() {
		$this->TableVar = "hoturls";
		$this->TableName = "hoturls";
		$this->SelectLimit = TRUE;
		$this->id = new cField('hoturls', 'x_id', 'id', "`id`", 3, -1, FALSE);
		$this->fields['id'] =& $this->id;
		$this->url = new cField('hoturls', 'x_url', 'url', "`url`", 200, -1, FALSE);
		$this->fields['url'] =& $this->url;
		$this->projectname = new cField('hoturls', 'x_projectname', 'projectname', "`projectname`", 200, -1, FALSE);
		$this->fields['projectname'] =& $this->projectname;
		$this->content = new cField('hoturls', 'x_content', 'content', "`content`", 200, -1, FALSE);
		$this->fields['content'] =& $this->content;
		$this->title = new cField('hoturls', 'x_title', 'title', "`title`", 200, -1, FALSE);
		$this->fields['title'] =& $this->title;
		$this->datetime = new cField('hoturls', 'x_datetime', 'datetime', "`datetime`", 135, 5, FALSE);
		$this->fields['datetime'] =& $this->datetime;
		$this->categories = new cField('hoturls', 'x_categories', 'categories', "`categories`", 200, -1, FALSE);
		$this->fields['categories'] =& $this->categories;
	}

	// Records per page
	function getRecordsPerPage() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_REC_PER_PAGE];
	}

	function setRecordsPerPage($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_REC_PER_PAGE] = $v;
	}

	// Start record number
	function getStartRecordNumber() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_START_REC];
	}

	function setStartRecordNumber($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_START_REC] = $v;
	}

	// Advanced search
	function getAdvancedSearch($fld) {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ADVANCED_SEARCH . "_" . $fld];
	}

	function setAdvancedSearch($fld, $v) {
		if (@$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ADVANCED_SEARCH . "_" . $fld] <> $v) {
			$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ADVANCED_SEARCH . "_" . $fld] = $v;
		}
	}

	// Basic search Keyword
	function getBasicSearchKeyword() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH];
	}

	function setBasicSearchKeyword($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH] = $v;
	}

	// Basic Search Type
	function getBasicSearchType() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_TYPE];
	}

	function setBasicSearchType($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_TYPE] = $v;
	}
	
	// Basic Search Time
	function getBasicSearchStartTime() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_START_TIME];
	}

	function setBasicSearchStartTime($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_START_TIME] = $v;
	}
	// Basic Search Time
	function getBasicSearchEndTime() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_END_TIME];
	}

	function setBasicSearchEndTime($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_END_TIME] = $v;
	}
	// Basic Search Time
	function getBasicSearchScope() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_SCOPE];
	}

	function setBasicSearchScope($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_SCOPE] = $v;
	}
	// Basic Search ProjectName
	function getBasicSearchProjectName() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_PROJECTNAME];
	}

	function setBasicSearchProjectName($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_PROJECTNAME] = $v;
	}
	// Basic Search Category
	function getBasicSearchCategory() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_CATEGORY];
	}

	function setBasicSearchCategory($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_BASIC_SEARCH_CATEGORY] = $v;
	}
	// Search where clause
	function getSearchWhere() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_SEARCH_WHERE];
	}

	function setSearchWhere($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_SEARCH_WHERE] = $v;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Session WHERE Clause
	
	// Session WHERE Clause
	function getSessionWhere() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_WHERE];
	}

	function setSessionWhere($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_WHERE] = $v;
	}

	// Session ORDER BY
	function getSessionOrderBy() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ORDER_BY];
	}

	function setSessionOrderBy($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ORDER_BY] = $v;
	}

	// Session Key
	function getKey($fld) {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_KEY . "_" . $fld];
	}

	function setKey($fld, $v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_KEY . "_" . $fld] = $v;
	}

	// Table level SQL
	
	function SqlSelect() { // Select
		return "SELECT *FROM `hoturls`";
	}

	function SqlWhere() { // Where
		return "";
	}

	function SqlGroupBy() { // Group By
		return "";
	}

	function SqlHaving() { // Having
		return "";
	}

	function SqlOrderBy() { // Order By
		return "`datetime` DESC";
		//return "";
	}

	// SQL variables
	var $CurrentFilter; // Current filter
	var $CurrentOrder; // Current order
	var $CurrentOrderType; // Current order type

	// Report table sql
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Return table sql with list page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		if ($this->CurrentFilter <> "") {
			if ($sFilter <> "") $sFilter .= " AND ";
			$sFilter .= $this->CurrentFilter;
		}
		$sSort = $this->getSessionOrderBy();

		return ew_BuildSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Return record count
	function SelectRecordCount() {
		global $conn;
		$cnt = -1;
		if ($this->SelectLimit) {
			$sSelect = $this->SelectSQL();
			if (strtoupper(substr($sSelect, 0, 13)) == "SELECT * FROM") {
				$sSelect = "SELECT COUNT(*) FROM" . substr($sSelect, 13);
				if ($rs = $conn->Execute($sSelect)) {
					if (!$rs->EOF) $cnt = $rs->fields[0];
					$rs->Close();
				}
			}
		}
		if ($cnt == -1) {
			if ($rs = $conn->Execute($this->SelectSQL())) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// INSERT statement
	function InsertSQL(&$rs) {
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= (is_null($value) ? "NULL" : ew_QuotedValue($value, $this->fields[$name]->FldDataType)) . ",";
		}
		if (substr($names, -1) == ",") $names = substr($names, 0, strlen($names)-1);
		if (substr($values, -1) == ",") $values = substr($values, 0, strlen($values)-1);
		return "INSERT INTO `hoturls` ($names) VALUES ($values)";
	}

	// UPDATE statement
	function UpdateSQL(&$rs) {
		$SQL = "UPDATE `hoturls` SET ";
		foreach ($rs as $name => $value) {
			$SQL .= $this->fields[$name]->FldExpression . "=" .
					(is_null($value) ? "NULL" : ew_QuotedValue($value, $this->fields[$name]->FldDataType)) . ",";
		}
		if (substr($SQL, -1) == ",") $SQL = substr($SQL, 0, strlen($SQL)-1);
		if ($this->CurrentFilter <> "")	$SQL .= " WHERE " . $this->CurrentFilter;
		return $SQL;
	}

	// DELETE statement
	function DeleteSQL(&$rs) {
		$SQL = "DELETE FROM `hoturls` WHERE ";
		$SQL .= EW_DB_QUOTE_START . 'id' . EW_DB_QUOTE_END . '=' .	ew_QuotedValue($rs['id'], $this->id->FldDataType) . ' AND ';
		if (substr($SQL, -5) == " AND ") $SQL = substr($SQL, 0, strlen($SQL)-5);
		if ($this->CurrentFilter <> "")	$SQL .= " AND " . $this->CurrentFilter;
		return $SQL;
	}

	// Key filter for table
	function SqlKeyFilter() {
		return "`id` = @id@";
	}

	// Return url
	function getReturnUrl() {
		if (@$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] <> "") {
			return $_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL];
		} else {
			return "hoturlslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// View url
	function ViewUrl($action) {
		return $this->KeyUrl("hoturlsview.php",$action);
	}

	// Edit url
	function EditUrl() {
		return $this->KeyUrl("hoturlsedit.php");
	}

	// Inline edit url
	function InlineEditUrl() {
		return $this->KeyUrl("hoturlslist.php", "a=edit");
	}

	// Copy url
	function CopyUrl() {
		return $this->KeyUrl("hoturlsadd.php");
	}

	// Inline copy url
	function InlineCopyUrl() {
		return $this->KeyUrl("hoturlslist.php", "a=copy");
	}

	// Delete url
	function DeleteUrl() {
		return $this->KeyUrl("hoturlsdelete.php");
	}

	// Key url
	function KeyUrl($url, $action = "") {
		$sUrl = $url . "?";
		if ($action <> "") $sUrl .= $action . "&";
		if (!is_null($this->id->CurrentValue)) {
			$sUrl .= "id=" . urlencode($this->id->CurrentValue);
		} else {
			return "javascript:alert('Invalid Record! Key is null');";
		}
		return $sUrl;
	}

	// Function LoadRs
	// - Load Row based on Key Value
	
	function LoadRs($sFilter) {
		global $conn;

		// Set up filter (Sql Where Clause) and get Return Sql
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		return $conn->Execute($sSql);
	}

	// Load row values from rs
	
	function LoadListRowValues(&$rs) {
		$this->id->setDbValue($rs->fields('id'));
		$this->url->setDbValue($rs->fields('url'));
		$this->projectname->setDbValue($rs->fields('projectname'));
		$this->title->setDbValue($rs->fields('title'));
		$this->content->setDbValue($rs->fields('content'));
		$this->datetime->setDbValue($rs->fields('datetime'));
		$this->categories->setDbValue($rs->fields('categories'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// id
		$this->id->ViewValue = $this->id->CurrentValue;
		$this->id->CssStyle = "";
		$this->id->CssClass = "";
		$this->id->ViewCustomAttributes = "";

		// url
		$this->url->ViewValue = $this->url->CurrentValue;
		$this->url->CssStyle = "";
		$this->url->CssClass = "";
		$this->url->ViewCustomAttributes = "";
		
		// projectname
		$this->projectname->ViewValue = $this->projectname->CurrentValue;
		$this->projectname->CssStyle = "";
		$this->projectname->CssClass = "";
		$this->projectname->ViewCustomAttributes = "";
		
		// content
		$this->content->ViewValue = $this->content->CurrentValue;
		$this->content->CssStyle = "";
		$this->content->CssClass = "";
		$this->content->ViewCustomAttributes = "";
		
		// title
		$this->title->ViewValue = $this->title->CurrentValue;
		$this->title->CssStyle = "";
		$this->title->CssClass = "";
		$this->title->ViewCustomAttributes = "";

		// datetime
		$this->datetime->ViewValue = $this->datetime->CurrentValue;
		$this->datetime->ViewValue = ew_FormatDateTime($this->datetime->ViewValue, 9);
		$this->datetime->CssStyle = "";
		$this->datetime->CssClass = "";
		$this->datetime->ViewCustomAttributes = "";

		// categories
		$this->categories->ViewValue = $this->categories->CurrentValue;
		$this->categories->CssStyle = "";
		$this->categories->CssClass = "";
		$this->categories->ViewCustomAttributes = "";

		// id
		$this->id->HrefValue = "";

		// url
		$this->url->HrefValue = "";
		
		// content
		$this->content->HrefValue = "";
		
		// title
		$this->title->HrefValue = "";
		
		// projectname
		$this->projectname->HrefValue = "";

		// datetime
		$this->datetime->HrefValue = "";

		// categories
		$this->categories->HrefValue = "";
	}
	var $CurrentAction; // Current action
	var $EventName; // Event name
	var $EventCancelled; // Event cancelled
	var $CancelMessage; // Cancel message
	var $RowType; // Row Type
	var $CssClass; // Css class
	var $CssStyle; // Css style
	var $RowClientEvents; // Row client events

	// Display Attribute
	function DisplayAttributes() {
		$sAtt = "";
		if (trim($this->CssStyle) <> "") {
			$sAtt .= " style=\"" . trim($this->CssStyle) . "\"";
		}
		if (trim($this->CssClass) <> "") {
			$sAtt .= " class=\"" . trim($this->CssClass) . "\"";
		}
		if ($this->Export == "") {
			if (trim($this->RowClientEvents) <> "") {
				$sAtt .= " " . $this->RowClientEvents;
			}
		}
		return $sAtt;
	}

	// Export
	var $Export;

//	 ----------------
//	  Field objects
//	 ----------------
	function fields($fldname) {
		return $this->fields[$fldname];
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// Row Inserting event
	function Row_Inserting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted(&$rs) {

		//echo "Row Inserted";
	}

	// Row Updating event
	function Row_Updating(&$rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Updated event
	function Row_Updated(&$rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Deleting event
	function Row_Deleting($rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}
}
?>
