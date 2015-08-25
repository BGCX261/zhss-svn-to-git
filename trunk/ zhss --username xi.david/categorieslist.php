<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'categories', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "categoriesinfo.php" ?>
<?php include "userfn50.php" ?>
<?php include "userinfo.php" ?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<?php

// Open connection to the database
$conn = ew_Connect();
?>
<?php
$Security = new cAdvancedSecurity();
?>
<?php
if (!$Security->IsLoggedIn()) $Security->AutoLogin();
if (!$Security->IsLoggedIn()) {
	$Security->SaveLastUrl();
	Page_Terminate("login.php");
}
?>
<?php

// Common page loading event (in userfn*.php)
Page_Loading();
?>
<?php

// Page load event, used in current page
Page_Load();
?>
<?php
$categories->Export = @$_GET["export"]; // Get export parameter
$sExport = $categories->Export; // Get export parameter, used in header
$sExportFile = $categories->TableVar; // Get export file, used in header
?>
<?php
?>
<?php

// Paging variables
$nStartRec = 0; // Start record index
$nStopRec = 0; // Stop record index
$nTotalRecs = 0; // Total number of records
$nDisplayRecs = 20;
$nRecRange = 10;
$nRecCount = 0; // Record count

// Search filters
$sSrchAdvanced = ""; // Advanced search filter
$sSrchBasic = ""; // Basic search filter
$sSrchWhere = ""; // Search where clause
$sFilter = "";

// Master/Detail
$sDbMasterFilter = ""; // Master filter
$sDbDetailFilter = ""; // Detail filter
$sSqlMaster = ""; // Sql for master record

// Handle reset command
ResetCmd();

// Get basic search criteria
$sSrchBasic = BasicSearchWhere();

// Build search criteria
if ($sSrchAdvanced <> "") {
	if ($sSrchWhere <> "") $sSrchWhere .= " AND ";
	$sSrchWhere .= "(" . $sSrchAdvanced . ")";
}
if ($sSrchBasic <> "") {
	if ($sSrchWhere <> "") $sSrchWhere .= " AND ";
	$sSrchWhere .= "(" . $sSrchBasic . ")";
}

// Save search criteria
if ($sSrchWhere <> "") {
	if ($sSrchBasic == "") ResetBasicSearchParms();
	$categories->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$categories->setStartRecordNumber($nStartRec);
} else {
	RestoreSearchParms();
}

// Build filter
$sFilter = "";
if ($sDbDetailFilter <> "") {
	if ($sFilter <> "") $sFilter .= " AND ";
	$sFilter .= "(" . $sDbDetailFilter . ")";
}
if ($sSrchWhere <> "") {
	if ($sFilter <> "") $sFilter .= " AND ";
	$sFilter .= "(" . $sSrchWhere . ")";
}

// Set up filter in Session
$categories->setSessionWhere($sFilter);
$categories->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$categories->setReturnUrl("categorieslist.php");
?>
<?php include "header.php" ?>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<!-- left column (begin) -->
		<td valign="top" class="ewMenuColumn">
<?php if ($categories->Export == "") { ?>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "list"; // Page id

//-->
</script>
<script type="text/javascript">
<!--
var firstrowoffset = 1; // First data row start at
var lastrowoffset = 0; // Last data row end at
var EW_LIST_TABLE_NAME = 'ewlistmain'; // Table name for list page
var rowclass = 'ewTableRow'; // Row class
var rowaltclass = 'ewTableAltRow'; // Row alternate class
var rowmoverclass = 'ewTableHighlightRow'; // Row mouse over class
var rowselectedclass = 'ewTableSelectRow'; // Row selected class
var roweditclass = 'ewTableEditRow'; // Row edit class

//-->
</script>
<script type="text/javascript">
<!--

// js for DHtml Editor
//-->

</script>
<script type="text/javascript">
<!--

// js for Popup Calendar
//-->

</script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<?php } ?>
<?php if ($categories->Export == "") { ?>
<?php } ?>
<?php

// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $categories->Export <> "");
$bSelectLimit = ($categories->Export == "" && $categories->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $categories->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
?>
<p><span class="phpmaker" style="white-space: nowrap;">
</span></p>
<?php if ($categories->Export == "") { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<form name="fcategorieslistsrch" id="fcategorieslistsrch" action="categorieslist.php" >
<div class="menuhead">敏感关键字</div>
<table class="ewBasicSearch">
	<tr>
		<td>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH ?>">关键词:</label>
		</td>
		<td>
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="10" class="searchInput" value="<?php echo $categories->getBasicSearchKeyword() ?>">
		</td>
	</tr>
	<tr>
	<td colspan="2">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($user->getBasicSearchType() == "") { ?>checked="checked"<?php } ?>>匹配以上<b>单个</b>关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND" value="AND" <?php if ($user->getBasicSearchType() == "AND") { ?>checked="checked"<?php } ?>>包含以上<b>全部</b>的关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR" value="OR" <?php if ($user->getBasicSearchType() == "OR") { ?>checked="checked"<?php } ?>>包含以上<b>任意一个</b>关键词</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="Submit" name="Submit" id="Submit" value="搜索 (*)">&nbsp;
			<a href="categorieslist.php?cmd=reset">显示全部</a>
		</td>
	</tr>
</table>
</form>
<?php } ?>
<?php } ?>
		</td>
		<!-- left column (end) -->
		<!-- right column (begin) -->
		<td valign="top" class="ewContentColumn">
<p><b>中和集成监控系统</b></p>

<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form method="post" name="fcategorieslist" id="fcategorieslist">
<?php if ($categories->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
	</span></td></tr>
</table>
<?php } ?>
<?php if ($nTotalRecs > 0) { ?>
<table id="ewlistmain" class="ewTable">
<?php
	$OptionCnt = 0;
if ($Security->IsLoggedIn()) {
	$OptionCnt++; // view
}
if ($Security->IsLoggedIn()) {
	$OptionCnt++; // edit
}
if ($Security->IsLoggedIn()) {
	$OptionCnt++; // copy
}
if ($Security->IsLoggedIn()) {
	$OptionCnt++; // delete
}
?>
	<!-- Table header -->
	<tr class="ewTableHeader">
		<td width="60">
<?php if ($categories->Export <> "") { ?>
id
<?php } else { ?>
	<a href="categorieslist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $categories->id->ReverseSort() ?>">编号<?php if ($categories->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($categories->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td>
<?php if ($categories->Export <> "") { ?>
name
<?php } else { ?>
	<a href="categorieslist.php?order=<?php echo urlencode('name') ?>&ordertype=<?php echo $categories->name->ReverseSort() ?>">类别&nbsp;(*)<?php if ($categories->name->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($categories->name->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td>
<?php if ($categories->Export <> "") { ?>
keywords
<?php } else { ?>
	<a href="categorieslist.php?order=<?php echo urlencode('keywords') ?>&ordertype=<?php echo $categories->keywords->ReverseSort() ?>">关键词&nbsp;(*)<?php if ($categories->keywords->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($categories->keywords->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>		
<?php if ($categories->Export == "") { ?>
<?php if ($Security->CanView()) { ?>
<td nowrap>&nbsp;</td>
<?php } ?>
<?php if ($Security->CanEdit()) { ?>
<td nowrap>&nbsp;</td>
<?php } ?>
<?php if ($Security->CanDelete()) { ?>
<td nowrap>&nbsp;</td>
<?php } ?>
<?php } ?>
	</tr>
<?php
if (defined("EW_EXPORT_ALL") && $categories->Export <> "") {
	$nStopRec = $nTotalRecs;
} else {
	$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
}
$nRecCount = $nStartRec - 1;
if (!$rs->EOF) {
	$rs->MoveFirst();
	if (!$categories->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
}
$RowCnt = 0;
while (!$rs->EOF && $nRecCount < $nStopRec) {
	$nRecCount++;
	if (intval($nRecCount) >= intval($nStartRec)) {
		$RowCnt++;

	// Init row class and style
	$categories->CssClass = "ewTableRow";
	$categories->CssStyle = "";

	// Init row event
	$categories->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

	// Display alternate color for rows
	if ($RowCnt % 2 == 0) {
		$categories->CssClass = "ewTableAltRow";
	}
	LoadRowValues($rs); // Load row values
	$categories->RowType = EW_ROWTYPE_VIEW; // Render view
	RenderRow();
?>
	<!-- Table body -->
	<tr<?php echo $categories->DisplayAttributes() ?>>
		<!-- id -->
		<td<?php echo $categories->id->CellAttributes() ?>>
<div<?php echo $categories->id->ViewAttributes() ?>><?php echo $categories->id->ViewValue ?></div>
</td>
		<!-- name -->
		<td<?php echo $categories->name->CellAttributes() ?>>
<div<?php echo $categories->name->ViewAttributes() ?>><?php echo $categories->name->ViewValue ?></div>
</td>
		<td<?php echo $categories->keywords->CellAttributes() ?>>
<div<?php echo $categories->keywords->ViewAttributes() ?>><?php echo $categories->keywords->ViewValue ?></div>
</td>
<?php if ($categories->Export == "") { ?>
<?php if ($Security->CanView()) { ?>
<?php } ?>
<?php if ($Security->CanEdit()) { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $categories->EditUrl() ?>">编辑</a>
</span></td>
<?php } ?>
<?php if ($Security->CanDelete()) { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $categories->DeleteUrl() ?>">删除</a>
</span></td>
<?php } ?>
<?php } ?>
	</tr>
<?php
	}
	$rs->MoveNext();
}
?>
</table>
<?php if ($categories->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<?php if ($Security->CanAdd()) { ?>
<a href="categoriesadd.php">添加</a>&nbsp;&nbsp;
<?php } ?>
	</span></td></tr>
</table>
<?php } ?>
<?php } ?>
</form>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($categories->Export == "") { ?>
<form action="categorieslist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">第</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="categorieslist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="categorieslist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="categorieslist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="categorieslist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/lastdisab.gif" alt="Last" width="16" height="16" border="0"></td>
	<?php } ?>
	<td><span class="phpmaker">页 在 <?php echo $Pager->PageCount ?> 中</span></td>
	</tr></table>
	<span class="phpmaker">第 <?php echo $Pager->FromIndex ?> 条 到 第 <?php echo $Pager->ToIndex ?> 条记录,总共 <?php echo $Pager->RecordCount ?> 条记录</span>
<?php } else { ?>
	<?php if ($sSrchWhere == "0=101") { ?>
	<span class="phpmaker">请输入搜索标准</span>
	<?php } else { ?>
	<span class="phpmaker">没有发现记录</span>
	<?php } ?>
<?php } ?>
		</td>
	</tr>
</table>
</form>
<?php } ?>
<?php if ($categories->Export == "") { ?>
<?php } ?>
<?php if ($categories->Export == "") { ?>
<script language="JavaScript" type="text/javascript">
<!--

// Write your table-specific startup script here
// document.write("page loaded");
//-->

</script>
<?php } ?>
<?php include "footer.php" ?>
<?php

// If control is passed here, simply terminate the page without redirect
Page_Terminate();

// -----------------------------------------------------------------
//  Subroutine Page_Terminate
//  - called when exit page
//  - clean up connection and objects
//  - if url specified, redirect to url, otherwise end response
function Page_Terminate($url = "") {
	global $conn;

	// Page unload event, used in current page
	Page_Unload();

	// Global page unloaded event (in userfn*.php)
	Page_Unloaded();

	 // Close Connection
	$conn->Close();

	// Go to url if specified
	if ($url <> "") {
		ob_end_clean();
		header("Location: $url");
	}
	exit();
}
?>
<?php

// Return Basic Search sql
function BasicSearchSQL($Keyword) {
	$sKeyword = ew_AdjustSql($Keyword);
	$sql = "";
	$sql .= "`name` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`keywords` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}

// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $categories;
	$sSearchStr = "";
	$sSearchKeyword = ew_StripSlashes(@$_GET[EW_TABLE_BASIC_SEARCH]);
	$sSearchType = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	if ($sSearchKeyword <> "") {
		$sSearch = trim($sSearchKeyword);
		if ($sSearchType <> "") {
			while (strpos($sSearch, "  ") !== FALSE)
				$sSearch = str_replace("  ", " ", $sSearch);
			$arKeyword = explode(" ", trim($sSearch));
			foreach ($arKeyword as $sKeyword) {
				if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
				$sSearchStr .= "(" . BasicSearchSQL($sKeyword) . ")";
			}
		} else {
			$sSearchStr = BasicSearchSQL($sSearch);
		}
	}
	if ($sSearchKeyword <> "") {
		$categories->setBasicSearchKeyword($sSearchKeyword);
		$categories->setBasicSearchType($sSearchType);
	}
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $categories;
	$sSrchWhere = "";
	$categories->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $categories;
	$categories->setBasicSearchKeyword("");
	$categories->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $categories;
	$sSrchWhere = $categories->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $categories;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$categories->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$categories->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$categories->UpdateSort($categories->id);

		// Field name
		$categories->UpdateSort($categories->name);
		$categories->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $categories->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($categories->SqlOrderBy() <> "") {
			$sOrderBy = $categories->SqlOrderBy();
			$categories->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $categories;

	// Get reset cmd
	if (@$_GET["cmd"] <> "") {
		$sCmd = $_GET["cmd"];

		// Reset search criteria
		if (strtolower($sCmd) == "reset" || strtolower($sCmd) == "resetall") {
			ResetSearchParms();
		}

		// Reset Sort Criteria
		if (strtolower($sCmd) == "resetsort") {
			$sOrderBy = "";
			$categories->setSessionOrderBy($sOrderBy);
			$categories->id->setSort("");
			$categories->name->setSort("");
			$categories->keywords->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$categories->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $categories;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$categories->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$categories->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $categories->getStartRecordNumber();
		}
	} else {
		$nStartRec = $categories->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$categories->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$categories->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$categories->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $categories;

	// Call Recordset Selecting event
	$categories->Recordset_Selecting($categories->CurrentFilter);

	// Load list page sql
	$sSql = $categories->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$categories->Recordset_Selected($rs);
	return $rs;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $categories;
	$sFilter = $categories->SqlKeyFilter();
	if (!is_numeric($categories->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($categories->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$categories->Row_Selecting($sFilter);

	// Load sql based on filter
	$categories->CurrentFilter = $sFilter;
	$sSql = $categories->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$categories->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $categories;
	$categories->id->setDbValue($rs->fields('id'));
	$categories->name->setDbValue($rs->fields('name'));
	$categories->keywords->setDbValue($rs->fields('keywords'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $categories;

	// Call Row Rendering event
	$categories->Row_Rendering();

	// Common render codes for all row types
	// id

	$categories->id->CellCssStyle = "";
	$categories->id->CellCssClass = "";

	// name
	$categories->name->CellCssStyle = "";
	$categories->name->CellCssClass = "";
	
		// keywords
	$categories->keywords->CellCssStyle = "";
	$categories->keywords->CellCssClass = "";
	if ($categories->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$categories->id->ViewValue = $categories->id->CurrentValue;
		$categories->id->CssStyle = "";
		$categories->id->CssClass = "";
		$categories->id->ViewCustomAttributes = "";

		// name
		$categories->name->ViewValue = $categories->name->CurrentValue;
		$categories->name->CssStyle = "";
		$categories->name->CssClass = "";
		$categories->name->ViewCustomAttributes = "";

		// keywords
		$categories->keywords->ViewValue = $categories->keywords->CurrentValue;
		$categories->keywords->CssStyle = "";
		$categories->keywords->CssClass = "";
		$categories->keywords->ViewCustomAttributes = "";
		// id
		$categories->id->HrefValue = "";

		// name
		$categories->name->HrefValue = "";

		// keywords
		$categories->keywords->HrefValue = "";
	} elseif ($categories->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($categories->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($categories->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$categories->Row_Rendered();
}
?>
<?php

// Page Load event
function Page_Load() {

	//echo "Page Load";
}

// Page Unload event
function Page_Unload() {

	//echo "Page Unload";
}
?>
