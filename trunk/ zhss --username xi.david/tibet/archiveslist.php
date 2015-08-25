<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'archives', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "archivesinfo.php" ?>
<?php include "userfn50.php" ?>
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

// Common page loading event (in userfn*.php)
Page_Loading();
?>
<?php

// Page load event, used in current page
Page_Load();
?>
<?php
$archives->Export = @$_GET["export"]; // Get export parameter
$sExport = $archives->Export; // Get export parameter, used in header
$sExportFile = $archives->TableVar; // Get export file, used in header
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
	$archives->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$archives->setStartRecordNumber($nStartRec);
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
$archives->setSessionWhere($sFilter);
$archives->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$archives->setReturnUrl("archiveslist.php");
?>
<?php include "header.php" ?>
<?php if ($archives->Export == "") { ?>
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
<?php if ($archives->Export == "") { ?>
<?php } ?>
<?php

// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $archives->Export <> "");
$bSelectLimit = ($archives->Export == "" && $archives->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $archives->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
?>
<p><span class="phpmaker" style="white-space: nowrap;">TABLE: archives
</span></p>
<?php if ($archives->Export == "") { ?>
<form name="farchiveslistsrch" id="farchiveslistsrch" action="archiveslist.php" >
<table class="ewBasicSearch">
	<tr>
		<td><span class="phpmaker">
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo ew_HtmlEncode($archives->getBasicSearchKeyword()) ?>">
			<input type="Submit" name="Submit" id="Submit" value="Search (*)">&nbsp;
			<a href="archiveslist.php?cmd=reset">Show all</a>&nbsp;
		</span></td>
	</tr>
	<tr>
	<td><span class="phpmaker"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($archives->getBasicSearchType() == "") { ?>checked<?php } ?>>Exact phrase&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND" <?php if ($archives->getBasicSearchType() == "AND") { ?>checked<?php } ?>>All words&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR" <?php if ($archives->getBasicSearchType() == "OR") { ?>checked<?php } ?>>Any word</span></td>
	</tr>
</table>
</form>
<?php } ?>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form method="post" name="farchiveslist" id="farchiveslist">
<?php if ($archives->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="archivesadd.php">Add</a>&nbsp;&nbsp;
	</span></td></tr>
</table>
<?php } ?>
<?php if ($nTotalRecs > 0) { ?>
<table id="ewlistmain" class="ewTable">
<?php
	$OptionCnt = 0;
	$OptionCnt++; // view
	$OptionCnt++; // edit
	$OptionCnt++; // copy
	$OptionCnt++; // delete
?>
	<!-- Table header -->
	<tr class="ewTableHeader">
		<td valign="top">
<?php if ($archives->Export <> "") { ?>
id
<?php } else { ?>
	<a href="archiveslist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $archives->id->ReverseSort() ?>">id<?php if ($archives->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($archives->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($archives->Export <> "") { ?>
url
<?php } else { ?>
	<a href="archiveslist.php?order=<?php echo urlencode('url') ?>&ordertype=<?php echo $archives->url->ReverseSort() ?>">url&nbsp;(*)<?php if ($archives->url->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($archives->url->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($archives->Export <> "") { ?>
projectname
<?php } else { ?>
	<a href="archiveslist.php?order=<?php echo urlencode('projectname') ?>&ordertype=<?php echo $archives->projectname->ReverseSort() ?>">projectname&nbsp;(*)<?php if ($archives->projectname->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($archives->projectname->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($archives->Export <> "") { ?>
title
<?php } else { ?>
	<a href="archiveslist.php?order=<?php echo urlencode('title') ?>&ordertype=<?php echo $archives->title->ReverseSort() ?>">title&nbsp;(*)<?php if ($archives->title->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($archives->title->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($archives->Export <> "") { ?>
datetime
<?php } else { ?>
	<a href="archiveslist.php?order=<?php echo urlencode('datetime') ?>&ordertype=<?php echo $archives->datetime->ReverseSort() ?>">datetime<?php if ($archives->datetime->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($archives->datetime->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
<?php if ($archives->Export == "") { ?>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<?php } ?>
	</tr>
<?php
if (defined("EW_EXPORT_ALL") && $archives->Export <> "") {
	$nStopRec = $nTotalRecs;
} else {
	$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
}
$nRecCount = $nStartRec - 1;
if (!$rs->EOF) {
	$rs->MoveFirst();
	if (!$archives->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
}
$RowCnt = 0;
while (!$rs->EOF && $nRecCount < $nStopRec) {
	$nRecCount++;
	if (intval($nRecCount) >= intval($nStartRec)) {
		$RowCnt++;

	// Init row class and style
	$archives->CssClass = "ewTableRow";
	$archives->CssStyle = "";

	// Init row event
	$archives->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

	// Display alternate color for rows
	if ($RowCnt % 2 == 0) {
		$archives->CssClass = "ewTableAltRow";
	}
	LoadRowValues($rs); // Load row values
	$archives->RowType = EW_ROWTYPE_VIEW; // Render view
	RenderRow();
?>
	<!-- Table body -->
	<tr<?php echo $archives->DisplayAttributes() ?>>
		<!-- id -->
		<td<?php echo $archives->id->CellAttributes() ?>>
<div<?php echo $archives->id->ViewAttributes() ?>><?php echo $archives->id->ViewValue ?></div>
</td>
		<!-- url -->
		<td<?php echo $archives->url->CellAttributes() ?>>
<div<?php echo $archives->url->ViewAttributes() ?>><?php echo $archives->url->ViewValue ?></div>
</td>
		<!-- projectname -->
		<td<?php echo $archives->projectname->CellAttributes() ?>>
<div<?php echo $archives->projectname->ViewAttributes() ?>><?php echo $archives->projectname->ViewValue ?></div>
</td>
		<!-- title -->
		<td<?php echo $archives->title->CellAttributes() ?>>
<div<?php echo $archives->title->ViewAttributes() ?>><?php echo $archives->title->ViewValue ?></div>
</td>
		<!-- datetime -->
		<td<?php echo $archives->datetime->CellAttributes() ?>>
<div<?php echo $archives->datetime->ViewAttributes() ?>><?php echo $archives->datetime->ViewValue ?></div>
</td>
<?php if ($archives->Export == "") { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $archives->ViewUrl() ?>">View</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $archives->EditUrl() ?>">Edit</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $archives->CopyUrl() ?>">Copy</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $archives->DeleteUrl() ?>">Delete</a>
</span></td>
<?php } ?>
	</tr>
<?php
	}
	$rs->MoveNext();
}
?>
</table>
<?php if ($archives->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="archivesadd.php">Add</a>&nbsp;&nbsp;
	</span></td></tr>
</table>
<?php } ?>
<?php } ?>
</form>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($archives->Export == "") { ?>
<form action="archiveslist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">Page&nbsp;</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="archiveslist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/lastdisab.gif" alt="Last" width="16" height="16" border="0"></td>
	<?php } ?>
	<td><span class="phpmaker">&nbsp;of <?php echo $Pager->PageCount ?></span></td>
	</tr></table>
	<span class="phpmaker">Records <?php echo $Pager->FromIndex ?> to <?php echo $Pager->ToIndex ?> of <?php echo $Pager->RecordCount ?></span>
<?php } else { ?>
	<?php if ($sSrchWhere == "0=101") { ?>
	<span class="phpmaker">Please enter search criteria</span>
	<?php } else { ?>
	<span class="phpmaker">No records found</span>
	<?php } ?>
<?php } ?>
		</td>
	</tr>
</table>
</form>
<?php } ?>
<?php if ($archives->Export == "") { ?>
<?php } ?>
<?php if ($archives->Export == "") { ?>
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
	$sql .= "`url` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`projectname` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`title` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`content` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}

// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $archives;
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
		$archives->setBasicSearchKeyword($sSearchKeyword);
		$archives->setBasicSearchType($sSearchType);
	}
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $archives;
	$sSrchWhere = "";
	$archives->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $archives;
	$archives->setBasicSearchKeyword("");
	$archives->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $archives;
	$sSrchWhere = $archives->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $archives;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$archives->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$archives->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$archives->UpdateSort($archives->id);

		// Field url
		$archives->UpdateSort($archives->url);

		// Field projectname
		$archives->UpdateSort($archives->projectname);

		// Field title
		$archives->UpdateSort($archives->title);

		// Field datetime
		$archives->UpdateSort($archives->datetime);
		$archives->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $archives->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($archives->SqlOrderBy() <> "") {
			$sOrderBy = $archives->SqlOrderBy();
			$archives->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $archives;

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
			$archives->setSessionOrderBy($sOrderBy);
			$archives->id->setSort("");
			$archives->url->setSort("");
			$archives->projectname->setSort("");
			$archives->title->setSort("");
			$archives->datetime->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$archives->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $archives;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$archives->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$archives->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $archives->getStartRecordNumber();
		}
	} else {
		$nStartRec = $archives->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$archives->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$archives->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$archives->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $archives;

	// Call Recordset Selecting event
	$archives->Recordset_Selecting($archives->CurrentFilter);

	// Load list page sql
	$sSql = $archives->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$archives->Recordset_Selected($rs);
	return $rs;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $archives;
	$sFilter = $archives->SqlKeyFilter();
	if (!is_numeric($archives->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($archives->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$archives->Row_Selecting($sFilter);

	// Load sql based on filter
	$archives->CurrentFilter = $sFilter;
	$sSql = $archives->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$archives->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $archives;
	$archives->id->setDbValue($rs->fields('id'));
	$archives->url->setDbValue($rs->fields('url'));
	$archives->projectname->setDbValue($rs->fields('projectname'));
	$archives->title->setDbValue($rs->fields('title'));
	$archives->datetime->setDbValue($rs->fields('datetime'));
	$archives->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $archives;

	// Call Row Rendering event
	$archives->Row_Rendering();

	// Common render codes for all row types
	// id

	$archives->id->CellCssStyle = "";
	$archives->id->CellCssClass = "";

	// url
	$archives->url->CellCssStyle = "";
	$archives->url->CellCssClass = "";

	// projectname
	$archives->projectname->CellCssStyle = "";
	$archives->projectname->CellCssClass = "";

	// title
	$archives->title->CellCssStyle = "";
	$archives->title->CellCssClass = "";

	// datetime
	$archives->datetime->CellCssStyle = "";
	$archives->datetime->CellCssClass = "";
	if ($archives->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$archives->id->ViewValue = $archives->id->CurrentValue;
		$archives->id->CssStyle = "";
		$archives->id->CssClass = "";
		$archives->id->ViewCustomAttributes = "";

		// url
		$archives->url->ViewValue = $archives->url->CurrentValue;
		$archives->url->CssStyle = "";
		$archives->url->CssClass = "";
		$archives->url->ViewCustomAttributes = "";

		// projectname
		$archives->projectname->ViewValue = $archives->projectname->CurrentValue;
		$archives->projectname->CssStyle = "";
		$archives->projectname->CssClass = "";
		$archives->projectname->ViewCustomAttributes = "";

		// title
		$archives->title->ViewValue = $archives->title->CurrentValue;
		$archives->title->CssStyle = "";
		$archives->title->CssClass = "";
		$archives->title->ViewCustomAttributes = "";

		// datetime
		$archives->datetime->ViewValue = $archives->datetime->CurrentValue;
		$archives->datetime->ViewValue = ew_FormatDateTime($archives->datetime->ViewValue, 5);
		$archives->datetime->CssStyle = "";
		$archives->datetime->CssClass = "";
		$archives->datetime->ViewCustomAttributes = "";

		// id
		$archives->id->HrefValue = "";

		// url
		$archives->url->HrefValue = "";

		// projectname
		$archives->projectname->HrefValue = "";

		// title
		$archives->title->HrefValue = "";

		// datetime
		$archives->datetime->HrefValue = "";
	} elseif ($archives->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($archives->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($archives->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$archives->Row_Rendered();
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
