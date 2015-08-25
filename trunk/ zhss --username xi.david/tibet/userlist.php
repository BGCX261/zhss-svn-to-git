<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'user', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "userinfo.php" ?>
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
$user->Export = @$_GET["export"]; // Get export parameter
$sExport = $user->Export; // Get export parameter, used in header
$sExportFile = $user->TableVar; // Get export file, used in header
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
	$user->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$user->setStartRecordNumber($nStartRec);
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
$user->setSessionWhere($sFilter);
$user->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$user->setReturnUrl("userlist.php");
?>
<?php include "header.php" ?>
<?php if ($user->Export == "") { ?>
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
<?php if ($user->Export == "") { ?>
<?php } ?>
<?php

// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $user->Export <> "");
$bSelectLimit = ($user->Export == "" && $user->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $user->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
?>
<p><span class="phpmaker" style="white-space: nowrap;">TABLE: user
</span></p>
<?php if ($user->Export == "") { ?>
<form name="fuserlistsrch" id="fuserlistsrch" action="userlist.php" >
<table class="ewBasicSearch">
	<tr>
		<td><span class="phpmaker">
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo ew_HtmlEncode($user->getBasicSearchKeyword()) ?>">
			<input type="Submit" name="Submit" id="Submit" value="Search (*)">&nbsp;
			<a href="userlist.php?cmd=reset">Show all</a>&nbsp;
		</span></td>
	</tr>
	<tr>
	<td><span class="phpmaker"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($user->getBasicSearchType() == "") { ?>checked<?php } ?>>Exact phrase&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND" <?php if ($user->getBasicSearchType() == "AND") { ?>checked<?php } ?>>All words&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR" <?php if ($user->getBasicSearchType() == "OR") { ?>checked<?php } ?>>Any word</span></td>
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
<form method="post" name="fuserlist" id="fuserlist">
<?php if ($user->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="useradd.php">Add</a>&nbsp;&nbsp;
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
<?php if ($user->Export <> "") { ?>
id
<?php } else { ?>
	<a href="userlist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $user->id->ReverseSort() ?>">id<?php if ($user->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($user->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($user->Export <> "") { ?>
user
<?php } else { ?>
	<a href="userlist.php?order=<?php echo urlencode('user') ?>&ordertype=<?php echo $user->user->ReverseSort() ?>">user&nbsp;(*)<?php if ($user->user->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($user->user->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($user->Export <> "") { ?>
password
<?php } else { ?>
	<a href="userlist.php?order=<?php echo urlencode('password') ?>&ordertype=<?php echo $user->password->ReverseSort() ?>">password&nbsp;(*)<?php if ($user->password->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($user->password->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($user->Export <> "") { ?>
level
<?php } else { ?>
	<a href="userlist.php?order=<?php echo urlencode('level') ?>&ordertype=<?php echo $user->level->ReverseSort() ?>">level<?php if ($user->level->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($user->level->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
<?php if ($user->Export == "") { ?>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<?php } ?>
	</tr>
<?php
if (defined("EW_EXPORT_ALL") && $user->Export <> "") {
	$nStopRec = $nTotalRecs;
} else {
	$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
}
$nRecCount = $nStartRec - 1;
if (!$rs->EOF) {
	$rs->MoveFirst();
	if (!$user->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
}
$RowCnt = 0;
while (!$rs->EOF && $nRecCount < $nStopRec) {
	$nRecCount++;
	if (intval($nRecCount) >= intval($nStartRec)) {
		$RowCnt++;

	// Init row class and style
	$user->CssClass = "ewTableRow";
	$user->CssStyle = "";

	// Init row event
	$user->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

	// Display alternate color for rows
	if ($RowCnt % 2 == 0) {
		$user->CssClass = "ewTableAltRow";
	}
	LoadRowValues($rs); // Load row values
	$user->RowType = EW_ROWTYPE_VIEW; // Render view
	RenderRow();
?>
	<!-- Table body -->
	<tr<?php echo $user->DisplayAttributes() ?>>
		<!-- id -->
		<td<?php echo $user->id->CellAttributes() ?>>
<div<?php echo $user->id->ViewAttributes() ?>><?php echo $user->id->ViewValue ?></div>
</td>
		<!-- user -->
		<td<?php echo $user->user->CellAttributes() ?>>
<div<?php echo $user->user->ViewAttributes() ?>><?php echo $user->user->ViewValue ?></div>
</td>
		<!-- password -->
		<td<?php echo $user->password->CellAttributes() ?>>
<div<?php echo $user->password->ViewAttributes() ?>><?php echo $user->password->ViewValue ?></div>
</td>
		<!-- level -->
		<td<?php echo $user->level->CellAttributes() ?>>
<div<?php echo $user->level->ViewAttributes() ?>><?php echo $user->level->ViewValue ?></div>
</td>
<?php if ($user->Export == "") { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $user->ViewUrl() ?>">View</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $user->EditUrl() ?>">Edit</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $user->CopyUrl() ?>">Copy</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $user->DeleteUrl() ?>">Delete</a>
</span></td>
<?php } ?>
	</tr>
<?php
	}
	$rs->MoveNext();
}
?>
</table>
<?php if ($user->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="useradd.php">Add</a>&nbsp;&nbsp;
	</span></td></tr>
</table>
<?php } ?>
<?php } ?>
</form>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($user->Export == "") { ?>
<form action="userlist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">Page&nbsp;</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="userlist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="userlist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="userlist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="userlist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
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
<?php if ($user->Export == "") { ?>
<?php } ?>
<?php if ($user->Export == "") { ?>
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
	$sql .= "`user` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`password` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}

// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $user;
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
		$user->setBasicSearchKeyword($sSearchKeyword);
		$user->setBasicSearchType($sSearchType);
	}
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $user;
	$sSrchWhere = "";
	$user->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $user;
	$user->setBasicSearchKeyword("");
	$user->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $user;
	$sSrchWhere = $user->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $user;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$user->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$user->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$user->UpdateSort($user->id);

		// Field user
		$user->UpdateSort($user->user);

		// Field password
		$user->UpdateSort($user->password);

		// Field level
		$user->UpdateSort($user->level);
		$user->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $user->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($user->SqlOrderBy() <> "") {
			$sOrderBy = $user->SqlOrderBy();
			$user->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $user;

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
			$user->setSessionOrderBy($sOrderBy);
			$user->id->setSort("");
			$user->user->setSort("");
			$user->password->setSort("");
			$user->level->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$user->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $user;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$user->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$user->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $user->getStartRecordNumber();
		}
	} else {
		$nStartRec = $user->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$user->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$user->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$user->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $user;

	// Call Recordset Selecting event
	$user->Recordset_Selecting($user->CurrentFilter);

	// Load list page sql
	$sSql = $user->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$user->Recordset_Selected($rs);
	return $rs;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $user;
	$sFilter = $user->SqlKeyFilter();
	if (!is_numeric($user->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($user->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$user->Row_Selecting($sFilter);

	// Load sql based on filter
	$user->CurrentFilter = $sFilter;
	$sSql = $user->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$user->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $user;
	$user->id->setDbValue($rs->fields('id'));
	$user->user->setDbValue($rs->fields('user'));
	$user->password->setDbValue($rs->fields('password'));
	$user->level->setDbValue($rs->fields('level'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $user;

	// Call Row Rendering event
	$user->Row_Rendering();

	// Common render codes for all row types
	// id

	$user->id->CellCssStyle = "";
	$user->id->CellCssClass = "";

	// user
	$user->user->CellCssStyle = "";
	$user->user->CellCssClass = "";

	// password
	$user->password->CellCssStyle = "";
	$user->password->CellCssClass = "";

	// level
	$user->level->CellCssStyle = "";
	$user->level->CellCssClass = "";
	if ($user->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$user->id->ViewValue = $user->id->CurrentValue;
		$user->id->CssStyle = "";
		$user->id->CssClass = "";
		$user->id->ViewCustomAttributes = "";

		// user
		$user->user->ViewValue = $user->user->CurrentValue;
		$user->user->CssStyle = "";
		$user->user->CssClass = "";
		$user->user->ViewCustomAttributes = "";

		// password
		$user->password->ViewValue = $user->password->CurrentValue;
		$user->password->CssStyle = "";
		$user->password->CssClass = "";
		$user->password->ViewCustomAttributes = "";

		// level
		$user->level->ViewValue = $user->level->CurrentValue;
		$user->level->CssStyle = "";
		$user->level->CssClass = "";
		$user->level->ViewCustomAttributes = "";

		// id
		$user->id->HrefValue = "";

		// user
		$user->user->HrefValue = "";

		// password
		$user->password->HrefValue = "";

		// level
		$user->level->HrefValue = "";
	} elseif ($user->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($user->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($user->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$user->Row_Rendered();
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
