<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'hoturls', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "hoturlsinfo.php" ?>
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
$hoturls->Export = @$_GET["export"]; // Get export parameter
$sExport = $hoturls->Export; // Get export parameter, used in header
$sExportFile = $hoturls->TableVar; // Get export file, used in header
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
	$hoturls->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$hoturls->setStartRecordNumber($nStartRec);
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
$hoturls->setSessionWhere($sFilter);
$hoturls->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$hoturls->setReturnUrl("hoturlslist.php");
?>
<?php include "header.php" ?>
<?php if ($hoturls->Export == "") { ?>
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
<?php if ($hoturls->Export == "") { ?>
<?php } ?>
<?php

// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $hoturls->Export <> "");
$bSelectLimit = ($hoturls->Export == "" && $hoturls->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $hoturls->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
?>
<p><span class="phpmaker" style="white-space: nowrap;">TABLE: hoturls
</span></p>
<?php if ($hoturls->Export == "") { ?>
<form name="fhoturlslistsrch" id="fhoturlslistsrch" action="hoturlslist.php" >
<table class="ewBasicSearch">
	<tr>
		<td><span class="phpmaker">
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo ew_HtmlEncode($hoturls->getBasicSearchKeyword()) ?>">
			<input type="Submit" name="Submit" id="Submit" value="Search (*)">&nbsp;
			<a href="hoturlslist.php?cmd=reset">Show all</a>&nbsp;
		</span></td>
	</tr>
	<tr>
	<td><span class="phpmaker"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($hoturls->getBasicSearchType() == "") { ?>checked<?php } ?>>Exact phrase&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND" <?php if ($hoturls->getBasicSearchType() == "AND") { ?>checked<?php } ?>>All words&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR" <?php if ($hoturls->getBasicSearchType() == "OR") { ?>checked<?php } ?>>Any word</span></td>
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
<form method="post" name="fhoturlslist" id="fhoturlslist">
<?php if ($hoturls->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="hoturlsadd.php">Add</a>&nbsp;&nbsp;
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
<?php if ($hoturls->Export <> "") { ?>
id
<?php } else { ?>
	<a href="hoturlslist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $hoturls->id->ReverseSort() ?>">id<?php if ($hoturls->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($hoturls->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
url
<?php } else { ?>
	<a href="hoturlslist.php?order=<?php echo urlencode('url') ?>&ordertype=<?php echo $hoturls->url->ReverseSort() ?>">url&nbsp;(*)<?php if ($hoturls->url->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($hoturls->url->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
projectname
<?php } else { ?>
	<a href="hoturlslist.php?order=<?php echo urlencode('projectname') ?>&ordertype=<?php echo $hoturls->projectname->ReverseSort() ?>">projectname&nbsp;(*)<?php if ($hoturls->projectname->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($hoturls->projectname->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
datetime
<?php } else { ?>
	<a href="hoturlslist.php?order=<?php echo urlencode('datetime') ?>&ordertype=<?php echo $hoturls->datetime->ReverseSort() ?>">datetime<?php if ($hoturls->datetime->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($hoturls->datetime->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
categories
<?php } else { ?>
	<a href="hoturlslist.php?order=<?php echo urlencode('categories') ?>&ordertype=<?php echo $hoturls->categories->ReverseSort() ?>">categories&nbsp;(*)<?php if ($hoturls->categories->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($hoturls->categories->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
title
<?php } else { ?>
	<a href="hoturlslist.php?order=<?php echo urlencode('title') ?>&ordertype=<?php echo $hoturls->title->ReverseSort() ?>">title&nbsp;(*)<?php if ($hoturls->title->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($hoturls->title->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
<?php if ($hoturls->Export == "") { ?>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<?php } ?>
	</tr>
<?php
if (defined("EW_EXPORT_ALL") && $hoturls->Export <> "") {
	$nStopRec = $nTotalRecs;
} else {
	$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
}
$nRecCount = $nStartRec - 1;
if (!$rs->EOF) {
	$rs->MoveFirst();
	if (!$hoturls->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
}
$RowCnt = 0;
while (!$rs->EOF && $nRecCount < $nStopRec) {
	$nRecCount++;
	if (intval($nRecCount) >= intval($nStartRec)) {
		$RowCnt++;

	// Init row class and style
	$hoturls->CssClass = "ewTableRow";
	$hoturls->CssStyle = "";

	// Init row event
	$hoturls->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

	// Display alternate color for rows
	if ($RowCnt % 2 == 0) {
		$hoturls->CssClass = "ewTableAltRow";
	}
	LoadRowValues($rs); // Load row values
	$hoturls->RowType = EW_ROWTYPE_VIEW; // Render view
	RenderRow();
?>
	<!-- Table body -->
	<tr<?php echo $hoturls->DisplayAttributes() ?>>
		<!-- id -->
		<td<?php echo $hoturls->id->CellAttributes() ?>>
<div<?php echo $hoturls->id->ViewAttributes() ?>><?php echo $hoturls->id->ViewValue ?></div>
</td>
		<!-- url -->
		<td<?php echo $hoturls->url->CellAttributes() ?>>
<div<?php echo $hoturls->url->ViewAttributes() ?>><?php echo $hoturls->url->ViewValue ?></div>
</td>
		<!-- projectname -->
		<td<?php echo $hoturls->projectname->CellAttributes() ?>>
<div<?php echo $hoturls->projectname->ViewAttributes() ?>><?php echo $hoturls->projectname->ViewValue ?></div>
</td>
		<!-- datetime -->
		<td<?php echo $hoturls->datetime->CellAttributes() ?>>
<div<?php echo $hoturls->datetime->ViewAttributes() ?>><?php echo $hoturls->datetime->ViewValue ?></div>
</td>
		<!-- categories -->
		<td<?php echo $hoturls->categories->CellAttributes() ?>>
<div<?php echo $hoturls->categories->ViewAttributes() ?>><?php echo $hoturls->categories->ViewValue ?></div>
</td>
		<!-- title -->
		<td<?php echo $hoturls->title->CellAttributes() ?>>
<div<?php echo $hoturls->title->ViewAttributes() ?>><?php echo $hoturls->title->ViewValue ?></div>
</td>
<?php if ($hoturls->Export == "") { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $hoturls->ViewUrl() ?>">View</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $hoturls->EditUrl() ?>">Edit</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $hoturls->CopyUrl() ?>">Copy</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $hoturls->DeleteUrl() ?>">Delete</a>
</span></td>
<?php } ?>
	</tr>
<?php
	}
	$rs->MoveNext();
}
?>
</table>
<?php if ($hoturls->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="hoturlsadd.php">Add</a>&nbsp;&nbsp;
	</span></td></tr>
</table>
<?php } ?>
<?php } ?>
</form>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($hoturls->Export == "") { ?>
<form action="hoturlslist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">Page&nbsp;</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="hoturlslist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="hoturlslist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="hoturlslist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="hoturlslist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
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
<?php if ($hoturls->Export == "") { ?>
<?php } ?>
<?php if ($hoturls->Export == "") { ?>
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
	$sql .= "`categories` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`title` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`content` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}

// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $hoturls;
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
		$hoturls->setBasicSearchKeyword($sSearchKeyword);
		$hoturls->setBasicSearchType($sSearchType);
	}
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $hoturls;
	$sSrchWhere = "";
	$hoturls->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $hoturls;
	$hoturls->setBasicSearchKeyword("");
	$hoturls->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $hoturls;
	$sSrchWhere = $hoturls->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $hoturls;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$hoturls->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$hoturls->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$hoturls->UpdateSort($hoturls->id);

		// Field url
		$hoturls->UpdateSort($hoturls->url);

		// Field projectname
		$hoturls->UpdateSort($hoturls->projectname);

		// Field datetime
		$hoturls->UpdateSort($hoturls->datetime);

		// Field categories
		$hoturls->UpdateSort($hoturls->categories);

		// Field title
		$hoturls->UpdateSort($hoturls->title);
		$hoturls->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $hoturls->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($hoturls->SqlOrderBy() <> "") {
			$sOrderBy = $hoturls->SqlOrderBy();
			$hoturls->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $hoturls;

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
			$hoturls->setSessionOrderBy($sOrderBy);
			$hoturls->id->setSort("");
			$hoturls->url->setSort("");
			$hoturls->projectname->setSort("");
			$hoturls->datetime->setSort("");
			$hoturls->categories->setSort("");
			$hoturls->title->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$hoturls->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $hoturls;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$hoturls->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$hoturls->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $hoturls->getStartRecordNumber();
		}
	} else {
		$nStartRec = $hoturls->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$hoturls->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$hoturls->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$hoturls->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $hoturls;

	// Call Recordset Selecting event
	$hoturls->Recordset_Selecting($hoturls->CurrentFilter);

	// Load list page sql
	$sSql = $hoturls->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$hoturls->Recordset_Selected($rs);
	return $rs;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $hoturls;
	$sFilter = $hoturls->SqlKeyFilter();
	if (!is_numeric($hoturls->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($hoturls->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$hoturls->Row_Selecting($sFilter);

	// Load sql based on filter
	$hoturls->CurrentFilter = $sFilter;
	$sSql = $hoturls->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$hoturls->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $hoturls;
	$hoturls->id->setDbValue($rs->fields('id'));
	$hoturls->url->setDbValue($rs->fields('url'));
	$hoturls->projectname->setDbValue($rs->fields('projectname'));
	$hoturls->datetime->setDbValue($rs->fields('datetime'));
	$hoturls->categories->setDbValue($rs->fields('categories'));
	$hoturls->title->setDbValue($rs->fields('title'));
	$hoturls->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $hoturls;

	// Call Row Rendering event
	$hoturls->Row_Rendering();

	// Common render codes for all row types
	// id

	$hoturls->id->CellCssStyle = "";
	$hoturls->id->CellCssClass = "";

	// url
	$hoturls->url->CellCssStyle = "";
	$hoturls->url->CellCssClass = "";

	// projectname
	$hoturls->projectname->CellCssStyle = "";
	$hoturls->projectname->CellCssClass = "";

	// datetime
	$hoturls->datetime->CellCssStyle = "";
	$hoturls->datetime->CellCssClass = "";

	// categories
	$hoturls->categories->CellCssStyle = "";
	$hoturls->categories->CellCssClass = "";

	// title
	$hoturls->title->CellCssStyle = "";
	$hoturls->title->CellCssClass = "";
	if ($hoturls->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$hoturls->id->ViewValue = $hoturls->id->CurrentValue;
		$hoturls->id->CssStyle = "";
		$hoturls->id->CssClass = "";
		$hoturls->id->ViewCustomAttributes = "";

		// url
		$hoturls->url->ViewValue = $hoturls->url->CurrentValue;
		$hoturls->url->CssStyle = "";
		$hoturls->url->CssClass = "";
		$hoturls->url->ViewCustomAttributes = "";

		// projectname
		$hoturls->projectname->ViewValue = $hoturls->projectname->CurrentValue;
		$hoturls->projectname->CssStyle = "";
		$hoturls->projectname->CssClass = "";
		$hoturls->projectname->ViewCustomAttributes = "";

		// datetime
		$hoturls->datetime->ViewValue = $hoturls->datetime->CurrentValue;
		$hoturls->datetime->ViewValue = ew_FormatDateTime($hoturls->datetime->ViewValue, 5);
		$hoturls->datetime->CssStyle = "";
		$hoturls->datetime->CssClass = "";
		$hoturls->datetime->ViewCustomAttributes = "";

		// categories
		$hoturls->categories->ViewValue = $hoturls->categories->CurrentValue;
		$hoturls->categories->CssStyle = "";
		$hoturls->categories->CssClass = "";
		$hoturls->categories->ViewCustomAttributes = "";

		// title
		$hoturls->title->ViewValue = $hoturls->title->CurrentValue;
		$hoturls->title->CssStyle = "";
		$hoturls->title->CssClass = "";
		$hoturls->title->ViewCustomAttributes = "";

		// id
		$hoturls->id->HrefValue = "";

		// url
		$hoturls->url->HrefValue = "";

		// projectname
		$hoturls->projectname->HrefValue = "";

		// datetime
		$hoturls->datetime->HrefValue = "";

		// categories
		$hoturls->categories->HrefValue = "";

		// title
		$hoturls->title->HrefValue = "";
	} elseif ($hoturls->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($hoturls->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($hoturls->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$hoturls->Row_Rendered();
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
