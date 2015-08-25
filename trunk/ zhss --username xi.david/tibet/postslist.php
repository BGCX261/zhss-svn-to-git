<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'posts', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "postsinfo.php" ?>
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
$posts->Export = @$_GET["export"]; // Get export parameter
$sExport = $posts->Export; // Get export parameter, used in header
$sExportFile = $posts->TableVar; // Get export file, used in header
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
	$posts->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$posts->setStartRecordNumber($nStartRec);
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
$posts->setSessionWhere($sFilter);
$posts->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$posts->setReturnUrl("postslist.php");
?>
<?php include "header.php" ?>
<?php if ($posts->Export == "") { ?>
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
<?php if ($posts->Export == "") { ?>
<?php } ?>
<?php

// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $posts->Export <> "");
$bSelectLimit = ($posts->Export == "" && $posts->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $posts->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
?>
<p><span class="phpmaker" style="white-space: nowrap;">TABLE: posts
</span></p>
<?php if ($posts->Export == "") { ?>
<form name="fpostslistsrch" id="fpostslistsrch" action="postslist.php" >
<table class="ewBasicSearch">
	<tr>
		<td><span class="phpmaker">
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo ew_HtmlEncode($posts->getBasicSearchKeyword()) ?>">
			<input type="Submit" name="Submit" id="Submit" value="Search (*)">&nbsp;
			<a href="postslist.php?cmd=reset">Show all</a>&nbsp;
		</span></td>
	</tr>
	<tr>
	<td><span class="phpmaker"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($posts->getBasicSearchType() == "") { ?>checked<?php } ?>>Exact phrase&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND" <?php if ($posts->getBasicSearchType() == "AND") { ?>checked<?php } ?>>All words&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR" <?php if ($posts->getBasicSearchType() == "OR") { ?>checked<?php } ?>>Any word</span></td>
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
<form method="post" name="fpostslist" id="fpostslist">
<?php if ($posts->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="postsadd.php">Add</a>&nbsp;&nbsp;
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
<?php if ($posts->Export <> "") { ?>
id
<?php } else { ?>
	<a href="postslist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $posts->id->ReverseSort() ?>">id<?php if ($posts->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($posts->Export <> "") { ?>
url
<?php } else { ?>
	<a href="postslist.php?order=<?php echo urlencode('url') ?>&ordertype=<?php echo $posts->url->ReverseSort() ?>">url&nbsp;(*)<?php if ($posts->url->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->url->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($posts->Export <> "") { ?>
datetime
<?php } else { ?>
	<a href="postslist.php?order=<?php echo urlencode('datetime') ?>&ordertype=<?php echo $posts->datetime->ReverseSort() ?>">datetime<?php if ($posts->datetime->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->datetime->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($posts->Export <> "") { ?>
title
<?php } else { ?>
	<a href="postslist.php?order=<?php echo urlencode('title') ?>&ordertype=<?php echo $posts->title->ReverseSort() ?>">title&nbsp;(*)<?php if ($posts->title->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->title->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($posts->Export <> "") { ?>
projectname
<?php } else { ?>
	<a href="postslist.php?order=<?php echo urlencode('projectname') ?>&ordertype=<?php echo $posts->projectname->ReverseSort() ?>">projectname&nbsp;(*)<?php if ($posts->projectname->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->projectname->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($posts->Export <> "") { ?>
digest
<?php } else { ?>
	<a href="postslist.php?order=<?php echo urlencode('digest') ?>&ordertype=<?php echo $posts->digest->ReverseSort() ?>">digest&nbsp;(*)<?php if ($posts->digest->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->digest->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($posts->Export <> "") { ?>
counter
<?php } else { ?>
	<a href="postslist.php?order=<?php echo urlencode('counter') ?>&ordertype=<?php echo $posts->counter->ReverseSort() ?>">counter<?php if ($posts->counter->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($posts->counter->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
<?php if ($posts->Export == "") { ?>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<?php } ?>
	</tr>
<?php
if (defined("EW_EXPORT_ALL") && $posts->Export <> "") {
	$nStopRec = $nTotalRecs;
} else {
	$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
}
$nRecCount = $nStartRec - 1;
if (!$rs->EOF) {
	$rs->MoveFirst();
	if (!$posts->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
}
$RowCnt = 0;
while (!$rs->EOF && $nRecCount < $nStopRec) {
	$nRecCount++;
	if (intval($nRecCount) >= intval($nStartRec)) {
		$RowCnt++;

	// Init row class and style
	$posts->CssClass = "ewTableRow";
	$posts->CssStyle = "";

	// Init row event
	$posts->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

	// Display alternate color for rows
	if ($RowCnt % 2 == 0) {
		$posts->CssClass = "ewTableAltRow";
	}
	LoadRowValues($rs); // Load row values
	$posts->RowType = EW_ROWTYPE_VIEW; // Render view
	RenderRow();
?>
	<!-- Table body -->
	<tr<?php echo $posts->DisplayAttributes() ?>>
		<!-- id -->
		<td<?php echo $posts->id->CellAttributes() ?>>
<div<?php echo $posts->id->ViewAttributes() ?>><?php echo $posts->id->ViewValue ?></div>
</td>
		<!-- url -->
		<td<?php echo $posts->url->CellAttributes() ?>>
<div<?php echo $posts->url->ViewAttributes() ?>><?php echo $posts->url->ViewValue ?></div>
</td>
		<!-- datetime -->
		<td<?php echo $posts->datetime->CellAttributes() ?>>
<div<?php echo $posts->datetime->ViewAttributes() ?>><?php echo $posts->datetime->ViewValue ?></div>
</td>
		<!-- title -->
		<td<?php echo $posts->title->CellAttributes() ?>>
<div<?php echo $posts->title->ViewAttributes() ?>><?php echo $posts->title->ViewValue ?></div>
</td>
		<!-- projectname -->
		<td<?php echo $posts->projectname->CellAttributes() ?>>
<div<?php echo $posts->projectname->ViewAttributes() ?>><?php echo $posts->projectname->ViewValue ?></div>
</td>
		<!-- digest -->
		<td<?php echo $posts->digest->CellAttributes() ?>>
<div<?php echo $posts->digest->ViewAttributes() ?>><?php echo $posts->digest->ViewValue ?></div>
</td>
		<!-- counter -->
		<td<?php echo $posts->counter->CellAttributes() ?>>
<div<?php echo $posts->counter->ViewAttributes() ?>><?php echo $posts->counter->ViewValue ?></div>
</td>
<?php if ($posts->Export == "") { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $posts->ViewUrl() ?>">View</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $posts->EditUrl() ?>">Edit</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $posts->CopyUrl() ?>">Copy</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $posts->DeleteUrl() ?>">Delete</a>
</span></td>
<?php } ?>
	</tr>
<?php
	}
	$rs->MoveNext();
}
?>
</table>
<?php if ($posts->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="postsadd.php">Add</a>&nbsp;&nbsp;
	</span></td></tr>
</table>
<?php } ?>
<?php } ?>
</form>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($posts->Export == "") { ?>
<form action="postslist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">Page&nbsp;</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="postslist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
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
<?php if ($posts->Export == "") { ?>
<?php } ?>
<?php if ($posts->Export == "") { ?>
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
	$sql .= "`title` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`projectname` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`digest` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`content` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}

// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $posts;
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
		$posts->setBasicSearchKeyword($sSearchKeyword);
		$posts->setBasicSearchType($sSearchType);
	}
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $posts;
	$sSrchWhere = "";
	$posts->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $posts;
	$posts->setBasicSearchKeyword("");
	$posts->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $posts;
	$sSrchWhere = $posts->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $posts;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$posts->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$posts->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$posts->UpdateSort($posts->id);

		// Field url
		$posts->UpdateSort($posts->url);

		// Field datetime
		$posts->UpdateSort($posts->datetime);

		// Field title
		$posts->UpdateSort($posts->title);

		// Field projectname
		$posts->UpdateSort($posts->projectname);

		// Field digest
		$posts->UpdateSort($posts->digest);

		// Field counter
		$posts->UpdateSort($posts->counter);
		$posts->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $posts->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($posts->SqlOrderBy() <> "") {
			$sOrderBy = $posts->SqlOrderBy();
			$posts->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $posts;

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
			$posts->setSessionOrderBy($sOrderBy);
			$posts->id->setSort("");
			$posts->url->setSort("");
			$posts->datetime->setSort("");
			$posts->title->setSort("");
			$posts->projectname->setSort("");
			$posts->digest->setSort("");
			$posts->counter->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$posts->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $posts;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$posts->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$posts->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $posts->getStartRecordNumber();
		}
	} else {
		$nStartRec = $posts->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$posts->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$posts->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$posts->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $posts;

	// Call Recordset Selecting event
	$posts->Recordset_Selecting($posts->CurrentFilter);

	// Load list page sql
	$sSql = $posts->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$posts->Recordset_Selected($rs);
	return $rs;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $posts;
	$sFilter = $posts->SqlKeyFilter();
	if (!is_numeric($posts->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($posts->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$posts->Row_Selecting($sFilter);

	// Load sql based on filter
	$posts->CurrentFilter = $sFilter;
	$sSql = $posts->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$posts->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $posts;
	$posts->id->setDbValue($rs->fields('id'));
	$posts->url->setDbValue($rs->fields('url'));
	$posts->datetime->setDbValue($rs->fields('datetime'));
	$posts->title->setDbValue($rs->fields('title'));
	$posts->projectname->setDbValue($rs->fields('projectname'));
	$posts->digest->setDbValue($rs->fields('digest'));
	$posts->counter->setDbValue($rs->fields('counter'));
	$posts->content->setDbValue($rs->fields('content'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $posts;

	// Call Row Rendering event
	$posts->Row_Rendering();

	// Common render codes for all row types
	// id

	$posts->id->CellCssStyle = "";
	$posts->id->CellCssClass = "";

	// url
	$posts->url->CellCssStyle = "";
	$posts->url->CellCssClass = "";

	// datetime
	$posts->datetime->CellCssStyle = "";
	$posts->datetime->CellCssClass = "";

	// title
	$posts->title->CellCssStyle = "";
	$posts->title->CellCssClass = "";

	// projectname
	$posts->projectname->CellCssStyle = "";
	$posts->projectname->CellCssClass = "";

	// digest
	$posts->digest->CellCssStyle = "";
	$posts->digest->CellCssClass = "";

	// counter
	$posts->counter->CellCssStyle = "";
	$posts->counter->CellCssClass = "";
	if ($posts->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$posts->id->ViewValue = $posts->id->CurrentValue;
		$posts->id->CssStyle = "";
		$posts->id->CssClass = "";
		$posts->id->ViewCustomAttributes = "";

		// url
		$posts->url->ViewValue = $posts->url->CurrentValue;
		$posts->url->CssStyle = "";
		$posts->url->CssClass = "";
		$posts->url->ViewCustomAttributes = "";

		// datetime
		$posts->datetime->ViewValue = $posts->datetime->CurrentValue;
		$posts->datetime->ViewValue = ew_FormatDateTime($posts->datetime->ViewValue, 5);
		$posts->datetime->CssStyle = "";
		$posts->datetime->CssClass = "";
		$posts->datetime->ViewCustomAttributes = "";

		// title
		$posts->title->ViewValue = $posts->title->CurrentValue;
		$posts->title->CssStyle = "";
		$posts->title->CssClass = "";
		$posts->title->ViewCustomAttributes = "";

		// projectname
		$posts->projectname->ViewValue = $posts->projectname->CurrentValue;
		$posts->projectname->CssStyle = "";
		$posts->projectname->CssClass = "";
		$posts->projectname->ViewCustomAttributes = "";

		// digest
		$posts->digest->ViewValue = $posts->digest->CurrentValue;
		$posts->digest->CssStyle = "";
		$posts->digest->CssClass = "";
		$posts->digest->ViewCustomAttributes = "";

		// counter
		$posts->counter->ViewValue = $posts->counter->CurrentValue;
		$posts->counter->CssStyle = "";
		$posts->counter->CssClass = "";
		$posts->counter->ViewCustomAttributes = "";

		// id
		$posts->id->HrefValue = "";

		// url
		$posts->url->HrefValue = "";

		// datetime
		$posts->datetime->HrefValue = "";

		// title
		$posts->title->HrefValue = "";

		// projectname
		$posts->projectname->HrefValue = "";

		// digest
		$posts->digest->HrefValue = "";

		// counter
		$posts->counter->HrefValue = "";
	} elseif ($posts->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($posts->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($posts->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$posts->Row_Rendered();
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
