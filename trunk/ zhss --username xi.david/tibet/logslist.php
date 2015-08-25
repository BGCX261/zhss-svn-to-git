<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'logs', TRUE);
?>
<?php 
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "logsinfo.php" ?>
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
$logs->Export = @$_GET["export"]; // Get export parameter
$sExport = $logs->Export; // Get export parameter, used in header
$sExportFile = $logs->TableVar; // Get export file, used in header
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
	$logs->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$logs->setStartRecordNumber($nStartRec);
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
$logs->setSessionWhere($sFilter);
$logs->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$logs->setReturnUrl("logslist.php");
?>
<?php include "header.php" ?>
<?php if ($logs->Export == "") { ?>
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
<?php if ($logs->Export == "") { ?>
<?php } ?>
<?php

// Load recordset
$bExportAll = (defined("EW_EXPORT_ALL") && $logs->Export <> "");
$bSelectLimit = ($logs->Export == "" && $logs->SelectLimit);
if (!$bSelectLimit) $rs = LoadRecordset();
$nTotalRecs = ($bSelectLimit) ? $logs->SelectRecordCount() : $rs->RecordCount();
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
?>
<p><span class="phpmaker" style="white-space: nowrap;">TABLE: logs
</span></p>
<?php if ($logs->Export == "") { ?>
<form name="flogslistsrch" id="flogslistsrch" action="logslist.php" >
<table class="ewBasicSearch">
	<tr>
		<td><span class="phpmaker">
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo ew_HtmlEncode($logs->getBasicSearchKeyword()) ?>">
			<input type="Submit" name="Submit" id="Submit" value="Search (*)">&nbsp;
			<a href="logslist.php?cmd=reset">Show all</a>&nbsp;
		</span></td>
	</tr>
	<tr>
	<td><span class="phpmaker"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($logs->getBasicSearchType() == "") { ?>checked<?php } ?>>Exact phrase&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND" <?php if ($logs->getBasicSearchType() == "AND") { ?>checked<?php } ?>>All words&nbsp;&nbsp;<input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR" <?php if ($logs->getBasicSearchType() == "OR") { ?>checked<?php } ?>>Any word</span></td>
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
<form method="post" name="flogslist" id="flogslist">
<?php if ($logs->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="logsadd.php">Add</a>&nbsp;&nbsp;
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
<?php if ($logs->Export <> "") { ?>
id
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $logs->id->ReverseSort() ?>">id<?php if ($logs->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
time
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('time') ?>&ordertype=<?php echo $logs->time->ReverseSort() ?>">time<?php if ($logs->time->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->time->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
client
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('client') ?>&ordertype=<?php echo $logs->client->ReverseSort() ?>">client&nbsp;(*)<?php if ($logs->client->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->client->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
group
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('group') ?>&ordertype=<?php echo $logs->group->ReverseSort() ?>">group&nbsp;(*)<?php if ($logs->group->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->group->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
type
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('type') ?>&ordertype=<?php echo $logs->type->ReverseSort() ?>">type<?php if ($logs->type->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->type->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
message
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('message') ?>&ordertype=<?php echo $logs->message->ReverseSort() ?>">message&nbsp;(*)<?php if ($logs->message->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->message->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
<?php if ($logs->Export == "") { ?>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<td nowrap>&nbsp;</td>
<?php } ?>
	</tr>
<?php
if (defined("EW_EXPORT_ALL") && $logs->Export <> "") {
	$nStopRec = $nTotalRecs;
} else {
	$nStopRec = $nStartRec + $nDisplayRecs - 1; // Set the last record to display
}
$nRecCount = $nStartRec - 1;
if (!$rs->EOF) {
	$rs->MoveFirst();
	if (!$logs->SelectLimit) $rs->Move($nStartRec - 1); // Move to first record directly
}
$RowCnt = 0;
while (!$rs->EOF && $nRecCount < $nStopRec) {
	$nRecCount++;
	if (intval($nRecCount) >= intval($nStartRec)) {
		$RowCnt++;

	// Init row class and style
	$logs->CssClass = "ewTableRow";
	$logs->CssStyle = "";

	// Init row event
	$logs->RowClientEvents = "onmouseover='ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";

	// Display alternate color for rows
	if ($RowCnt % 2 == 0) {
		$logs->CssClass = "ewTableAltRow";
	}
	LoadRowValues($rs); // Load row values
	$logs->RowType = EW_ROWTYPE_VIEW; // Render view
	RenderRow();
?>
	<!-- Table body -->
	<tr<?php echo $logs->DisplayAttributes() ?>>
		<!-- id -->
		<td<?php echo $logs->id->CellAttributes() ?>>
<div<?php echo $logs->id->ViewAttributes() ?>><?php echo $logs->id->ViewValue ?></div>
</td>
		<!-- time -->
		<td<?php echo $logs->time->CellAttributes() ?>>
<div<?php echo $logs->time->ViewAttributes() ?>><?php echo $logs->time->ViewValue ?></div>
</td>
		<!-- client -->
		<td<?php echo $logs->client->CellAttributes() ?>>
<div<?php echo $logs->client->ViewAttributes() ?>><?php echo $logs->client->ViewValue ?></div>
</td>
		<!-- group -->
		<td<?php echo $logs->group->CellAttributes() ?>>
<div<?php echo $logs->group->ViewAttributes() ?>><?php echo $logs->group->ViewValue ?></div>
</td>
		<!-- type -->
		<td<?php echo $logs->type->CellAttributes() ?>>
<div<?php echo $logs->type->ViewAttributes() ?>><?php echo $logs->type->ViewValue ?></div>
</td>
		<!-- message -->
		<td<?php echo $logs->message->CellAttributes() ?>>
<div<?php echo $logs->message->ViewAttributes() ?>><?php echo $logs->message->ViewValue ?></div>
</td>
<?php if ($logs->Export == "") { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $logs->ViewUrl() ?>">View</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $logs->EditUrl() ?>">Edit</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $logs->CopyUrl() ?>">Copy</a>
</span></td>
<td nowrap><span class="phpmaker">
<a href="<?php echo $logs->DeleteUrl() ?>">Delete</a>
</span></td>
<?php } ?>
	</tr>
<?php
	}
	$rs->MoveNext();
}
?>
</table>
<?php if ($logs->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<a href="logsadd.php">Add</a>&nbsp;&nbsp;
	</span></td></tr>
</table>
<?php } ?>
<?php } ?>
</form>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($logs->Export == "") { ?>
<form action="logslist.php" name="ewpagerform" id="ewpagerform">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">Page&nbsp;</span></td>
<!--first page button-->
	<?php if ($Pager->FirstButton->Enabled) { ?>
	<td><a href="logslist.php?start=<?php echo $Pager->FirstButton->Start ?>"><img src="images/first.gif" alt="First" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/firstdisab.gif" alt="First" width="16" height="16" border="0"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($Pager->PrevButton->Enabled) { ?>
	<td><a href="logslist.php?start=<?php echo $Pager->PrevButton->Start ?>"><img src="images/prev.gif" alt="Previous" width="16" height="16" border="0"></a></td>
	<?php } else { ?>
	<td><img src="images/prevdisab.gif" alt="Previous" width="16" height="16" border="0"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($Pager->NextButton->Enabled) { ?>
	<td><a href="logslist.php?start=<?php echo $Pager->NextButton->Start ?>"><img src="images/next.gif" alt="Next" width="16" height="16" border="0"></a></td>	
	<?php } else { ?>
	<td><img src="images/nextdisab.gif" alt="Next" width="16" height="16" border="0"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($Pager->LastButton->Enabled) { ?>
	<td><a href="logslist.php?start=<?php echo $Pager->LastButton->Start ?>"><img src="images/last.gif" alt="Last" width="16" height="16" border="0"></a></td>	
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
<?php if ($logs->Export == "") { ?>
<?php } ?>
<?php if ($logs->Export == "") { ?>
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
	$sql .= "`client` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`group` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`message` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
}

// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $logs;
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
		$logs->setBasicSearchKeyword($sSearchKeyword);
		$logs->setBasicSearchType($sSearchType);
	}
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $logs;
	$sSrchWhere = "";
	$logs->setSearchWhere($sSrchWhere);

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $logs;
	$logs->setBasicSearchKeyword("");
	$logs->setBasicSearchType("");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $logs;
	$sSrchWhere = $logs->getSearchWhere();
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $logs;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$logs->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$logs->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$logs->UpdateSort($logs->id);

		// Field time
		$logs->UpdateSort($logs->time);

		// Field client
		$logs->UpdateSort($logs->client);

		// Field group
		$logs->UpdateSort($logs->group);

		// Field type
		$logs->UpdateSort($logs->type);

		// Field message
		$logs->UpdateSort($logs->message);
		$logs->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $logs->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($logs->SqlOrderBy() <> "") {
			$sOrderBy = $logs->SqlOrderBy();
			$logs->setSessionOrderBy($sOrderBy);
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $logs;

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
			$logs->setSessionOrderBy($sOrderBy);
			$logs->id->setSort("");
			$logs->time->setSort("");
			$logs->client->setSort("");
			$logs->group->setSort("");
			$logs->type->setSort("");
			$logs->message->setSort("");
		}

		// Reset start position
		$nStartRec = 1;
		$logs->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Set up Starting Record parameters based on Pager Navigation
function SetUpStartRec() {
	global $nDisplayRecs, $nStartRec, $nTotalRecs, $nPageNo, $logs;
	if ($nDisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EW_TABLE_START_REC] <> "") {
		$nStartRec = $_GET[EW_TABLE_START_REC];
		$logs->setStartRecordNumber($nStartRec);
	} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
		$nPageNo = $_GET[EW_TABLE_PAGE_NO];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			} elseif ($nStartRec >= intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$logs->setStartRecordNumber($nStartRec);
		} else {
			$nStartRec = $logs->getStartRecordNumber();
		}
	} else {
		$nStartRec = $logs->getStartRecordNumber();
	}

	// Check if correct start record counter
	if (!is_numeric($nStartRec) || $nStartRec == "") { // Avoid invalid start record counter
		$nStartRec = 1; // Reset start record counter
		$logs->setStartRecordNumber($nStartRec);
	} elseif (intval($nStartRec) > intval($nTotalRecs)) { // Avoid starting record > total records
		$nStartRec = intval(($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to last page first record
		$logs->setStartRecordNumber($nStartRec);
	} elseif (($nStartRec-1) % $nDisplayRecs <> 0) {
		$nStartRec = intval(($nStartRec-1)/$nDisplayRecs)*$nDisplayRecs+1; // Point to page boundary
		$logs->setStartRecordNumber($nStartRec);
	}
}
?>
<?php

// Load recordset
function LoadRecordset($offset = -1, $rowcnt = -1) {
	global $conn, $logs;

	// Call Recordset Selecting event
	$logs->Recordset_Selecting($logs->CurrentFilter);

	// Load list page sql
	$sSql = $logs->SelectSQL();
	if ($offset > -1 && $rowcnt > -1) $sSql .= " LIMIT $offset, $rowcnt";

	// Load recordset
	$conn->raiseErrorFn = 'ew_ErrorFn';	
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';

	// Call Recordset Selected event
	$logs->Recordset_Selected($rs);
	return $rs;
}
?>
<?php

// Load row based on key values
function LoadRow() {
	global $conn, $Security, $logs;
	$sFilter = $logs->SqlKeyFilter();
	if (!is_numeric($logs->id->CurrentValue)) {
		return FALSE; // Invalid key, exit
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($logs->id->CurrentValue), $sFilter); // Replace key value

	// Call Row Selecting event
	$logs->Row_Selecting($sFilter);

	// Load sql based on filter
	$logs->CurrentFilter = $sFilter;
	$sSql = $logs->SQL();
	if ($rs = $conn->Execute($sSql)) {
		if ($rs->EOF) {
			$LoadRow = FALSE;
		} else {
			$LoadRow = TRUE;
			$rs->MoveFirst();
			LoadRowValues($rs); // Load row values

			// Call Row Selected event
			$logs->Row_Selected($rs);
		}
		$rs->Close();
	} else {
		$LoadRow = FALSE;
	}
	return $LoadRow;
}

// Load row values from recordset
function LoadRowValues(&$rs) {
	global $logs;
	$logs->id->setDbValue($rs->fields('id'));
	$logs->time->setDbValue($rs->fields('time'));
	$logs->client->setDbValue($rs->fields('client'));
	$logs->group->setDbValue($rs->fields('group'));
	$logs->type->setDbValue($rs->fields('type'));
	$logs->message->setDbValue($rs->fields('message'));
}
?>
<?php

// Render row values based on field settings
function RenderRow() {
	global $conn, $Security, $logs;

	// Call Row Rendering event
	$logs->Row_Rendering();

	// Common render codes for all row types
	// id

	$logs->id->CellCssStyle = "";
	$logs->id->CellCssClass = "";

	// time
	$logs->time->CellCssStyle = "";
	$logs->time->CellCssClass = "";

	// client
	$logs->client->CellCssStyle = "";
	$logs->client->CellCssClass = "";

	// group
	$logs->group->CellCssStyle = "";
	$logs->group->CellCssClass = "";

	// type
	$logs->type->CellCssStyle = "";
	$logs->type->CellCssClass = "";

	// message
	$logs->message->CellCssStyle = "";
	$logs->message->CellCssClass = "";
	if ($logs->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$logs->id->ViewValue = $logs->id->CurrentValue;
		$logs->id->CssStyle = "";
		$logs->id->CssClass = "";
		$logs->id->ViewCustomAttributes = "";

		// time
		$logs->time->ViewValue = $logs->time->CurrentValue;
		$logs->time->ViewValue = ew_FormatDateTime($logs->time->ViewValue, 5);
		$logs->time->CssStyle = "";
		$logs->time->CssClass = "";
		$logs->time->ViewCustomAttributes = "";

		// client
		$logs->client->ViewValue = $logs->client->CurrentValue;
		$logs->client->CssStyle = "";
		$logs->client->CssClass = "";
		$logs->client->ViewCustomAttributes = "";

		// group
		$logs->group->ViewValue = $logs->group->CurrentValue;
		$logs->group->CssStyle = "";
		$logs->group->CssClass = "";
		$logs->group->ViewCustomAttributes = "";

		// type
		$logs->type->ViewValue = $logs->type->CurrentValue;
		$logs->type->CssStyle = "";
		$logs->type->CssClass = "";
		$logs->type->ViewCustomAttributes = "";

		// message
		$logs->message->ViewValue = $logs->message->CurrentValue;
		$logs->message->CssStyle = "";
		$logs->message->CssClass = "";
		$logs->message->ViewCustomAttributes = "";

		// id
		$logs->id->HrefValue = "";

		// time
		$logs->time->HrefValue = "";

		// client
		$logs->client->HrefValue = "";

		// group
		$logs->group->HrefValue = "";

		// type
		$logs->type->HrefValue = "";

		// message
		$logs->message->HrefValue = "";
	} elseif ($logs->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($logs->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$logs->Row_Rendered();
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
