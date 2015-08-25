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

// Check QueryString parameters
if (@$_GET["a"] <> "") {
	$logs->CurrentAction = $_GET["a"];

	// Clear inline mode
	if ($logs->CurrentAction == "cancel") {
		ClearInlineMode();
	}

	// Switch to grid edit mode
	if ($logs->CurrentAction == "gridedit") {
		GridEditMode();
	}
} else {

	// Create form object
	$objForm = new cFormObj;
	if (@$_POST["a_list"] <> "") {
		$logs->CurrentAction = $_POST["a_list"]; // Get action

		// Grid Update
		if ($logs->CurrentAction == "gridupdate" && @$_SESSION[EW_SESSION_INLINE_MODE] == "gridedit") {
			GridUpdate();
		}
	}
}

// Get search criteria for advanced search
$sSrchAdvanced = AdvancedSearchWhere();

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
	if ($sSrchAdvanced == "") ResetAdvancedSearchParms();
	$logs->setSearchWhere($sSrchWhere); // Save to Session
	$nStartRec = 1; // Reset start record counter
	$logs->setStartRecordNumber($nStartRec);
} else {
	RestoreSearchParms();
	if ($sSrchWhere == "") {
		//$sSrchWhere = "0=101";
	}
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
var EW_SHOW_HIGHLIGHT = "高亮显示"; 
var EW_HIDE_HIGHLIGHT = "隐藏高亮显示";

//-->
</script>
<script type="text/javascript">
<!--

function ew_ValidateForm(fobj) {
	if (fobj.a_confirm && fobj.a_confirm.value == "F")
		return true;
	var i, elm, aelm, infix;
	var rowcnt = (fobj.key_count) ? Number(fobj.key_count.value) : 1;
	for (i=0; i<rowcnt; i++) {
		infix = (fobj.key_count) ? String(i+1) : "";
		elm = fobj.elements["x" + infix + "_time"];
		if (elm && !ew_HasValue(elm)) {
			if (!ew_OnError(elm, "Please enter required field - time"))
				return false;
		}
		elm = fobj.elements["x" + infix + "_time"];
		if (elm && !ew_CheckDate(elm.value)) {
			if (!ew_OnError(elm, "Incorrect date, format = yyyy/mm/dd - time"))
				return false; 
		}
		elm = fobj.elements["x" + infix + "_type"];
		if (elm && !ew_CheckInteger(elm.value)) {
			if (!ew_OnError(elm, "Incorrect integer - type"))
				return false; 
		}
	}
	return true;
}

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
<p><span class="phpmaker" style="white-space: nowrap;">
</span></p>
<?php if ($logs->Export == "") { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<form name="flogslistsrch" id="flogslistsrch" action="logslist.php" >
<table class="ewBasicSearch">
	<tr>
		<td><span class="phpmaker">
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo $logs->getBasicSearchKeyword() ?>">
			<input type="Submit" name="Submit" id="Submit" value="搜索 (*)">&nbsp;
			<a href="logslist.php?cmd=reset">重设</a>&nbsp;
			<a href="logssrch.php">高级搜索</a>&nbsp;
			<?php if ($sSrchWhere <> "" && $nTotalRecs > 0) { ?>
			<a href="javascript:void(0);" onclick="ew_ToggleHighlight(this);">隐藏高亮显示</a>
			<?php } ?>
		</span></td>
	</tr>
	<tr>
	<td><span class="phpmaker">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="" <?php if ($user->getBasicSearchType() == "") { ?>checked="checked"<?php } ?>>匹配以上<b>单个</b>关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>AND" value="AND" <?php if ($user->getBasicSearchType() == "AND") { ?>checked="checked"<?php } ?>>包含以上<b>全部</b>的关键词</label>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>OR" value="OR" <?php if ($user->getBasicSearchType() == "OR") { ?>checked="checked"<?php } ?>>包含以上<b>任意一个</b>关键词</label>
	</span></td>
	</tr>
</table>
</form>
<?php } ?>
<?php } ?>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form name="flogslist" id="flogslist" action="logslist.php" method="post">
<?php if ($logs->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<?php if ($logs->CurrentAction <> "gridedit") { // Not grid edit mode ?>
<?php if ($Security->IsLoggedIn()) { ?>
<?php if ($nTotalRecs > 0) { ?>
<a href="logslist.php?a=gridedit">批量编辑</a>&nbsp;&nbsp;
<?php } ?>
<?php } ?>
<?php } else { // Grid edit mode ?>
<a href="" onClick="if (ew_ValidateForm(document.flogslist)) document.flogslist.submit();return false;">保存</a>&nbsp;&nbsp;
<a href="logslist.php?a=cancel">取消</a>&nbsp;&nbsp;
<?php } ?>
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
	$OptionCnt++; // delete
}
?>
	<!-- Table header -->
	<tr class="ewTableHeader">
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
id
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('id') ?>&ordertype=<?php echo $logs->id->ReverseSort() ?>">编号<?php if ($logs->id->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->id->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
time
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('time') ?>&ordertype=<?php echo $logs->time->ReverseSort() ?>">时间<?php if ($logs->time->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->time->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
client
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('client') ?>&ordertype=<?php echo $logs->client->ReverseSort() ?>">用户名&nbsp;(*)<?php if ($logs->client->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->client->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
group
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('group') ?>&ordertype=<?php echo $logs->group->ReverseSort() ?>">用户组&nbsp;(*)<?php if ($logs->group->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->group->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
type
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('type') ?>&ordertype=<?php echo $logs->type->ReverseSort() ?>">类型<?php if ($logs->type->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->type->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
		<td valign="top">
<?php if ($logs->Export <> "") { ?>
message
<?php } else { ?>
	<a href="logslist.php?order=<?php echo urlencode('message') ?>&ordertype=<?php echo $logs->message->ReverseSort() ?>">日志信息&nbsp;(*)<?php if ($logs->message->getSort() == "ASC") { ?><img src="images/sortup.gif" width="10" height="9" border="0"><?php } elseif ($logs->message->getSort() == "DESC") { ?><img src="images/sortdown.gif" width="10" height="9" border="0"><?php } ?></a>
<?php } ?>
		</td>
<?php if ($logs->Export == "") { ?>
<?php if ($logs->CurrentAction <> "gridedit") { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<td nowrap>&nbsp;</td>
<?php } ?>
<?php if ($Security->IsLoggedIn()) { ?>
<td nowrap>&nbsp;</td>
<?php } ?>
<?php } ?>
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
if ($logs->CurrentAction == "gridedit") $RowIndex = 0;
while (!$rs->EOF && $nRecCount < $nStopRec) {
	$nRecCount++;
	if (intval($nRecCount) >= intval($nStartRec)) {
		$RowCnt++;
		if ($logs->CurrentAction == "gridedit") $RowIndex++;

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
	if ($logs->CurrentAction == "gridedit") { // Grid edit
		$logs->RowType = EW_ROWTYPE_EDIT; // Render edit
	}
		if ($logs->RowType == EW_ROWTYPE_EDIT && $logs->EventCancelled) { // Update failed
			if ($logs->CurrentAction == "gridedit") {
				RestoreCurrentRowFormValues(); // Restore form values
			}
		}
		if ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit row
			$logs->CssClass = "ewTableEditRow";
			$logs->RowClientEvents = "onmouseover='this.edit=true;ew_MouseOver(this);' onmouseout='ew_MouseOut(this);' onclick='ew_Click(this);'";
		}
	RenderRow();
?>
	<!-- Table body -->
	<tr<?php echo $logs->DisplayAttributes() ?>>
		<!-- id -->
		<td<?php echo $logs->id->CellAttributes() ?>>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit Record ?>
<div<?php echo $logs->id->ViewAttributes() ?>><?php echo $logs->id->EditValue ?></div>
<input type="hidden" name="x<?php echo $RowIndex ?>_id" id="x<?php echo $RowIndex ?>_id" value="<?php echo ew_HtmlEncode($logs->id->CurrentValue) ?>">
<?php } else { ?>
<div<?php echo $logs->id->ViewAttributes() ?>><?php echo $logs->id->ViewValue ?></div>
<?php } ?>
</td>
		<!-- time -->
		<td<?php echo $logs->time->CellAttributes() ?>>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit Record ?>
<input type="text" name="x<?php echo $RowIndex ?>_time" id="x<?php echo $RowIndex ?>_time" title="" value="<?php echo $logs->time->EditValue ?>"<?php echo $logs->time->EditAttributes() ?>>
<?php } else { ?>
<div<?php echo $logs->time->ViewAttributes() ?>><?php echo $logs->time->ViewValue ?></div>
<?php } ?>
</td>
		<!-- client -->
		<td<?php echo $logs->client->CellAttributes() ?>>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit Record ?>
<input type="text" name="x<?php echo $RowIndex ?>_client" id="x<?php echo $RowIndex ?>_client" title="" size="30" maxlength="30" value="<?php echo $logs->client->EditValue ?>"<?php echo $logs->client->EditAttributes() ?>>
<?php } else { ?>
<div<?php echo $logs->client->ViewAttributes() ?>><?php echo $logs->client->ViewValue ?></div>
<?php } ?>
</td>
		<!-- group -->
		<td<?php echo $logs->group->CellAttributes() ?>>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit Record ?>
<input type="text" name="x<?php echo $RowIndex ?>_group" id="x<?php echo $RowIndex ?>_group" title="" size="30" maxlength="20" value="<?php echo $logs->group->EditValue ?>"<?php echo $logs->group->EditAttributes() ?>>
<?php } else { ?>
<div<?php echo $logs->group->ViewAttributes() ?>><?php echo $logs->group->ViewValue ?></div>
<?php } ?>
</td>
		<!-- type -->
		<td<?php echo $logs->type->CellAttributes() ?>>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit Record ?>
<input type="text" name="x<?php echo $RowIndex ?>_type" id="x<?php echo $RowIndex ?>_type" title="" size="30" value="<?php echo $logs->type->EditValue ?>"<?php echo $logs->type->EditAttributes() ?>>
<?php } else { ?>
<div<?php echo $logs->type->ViewAttributes() ?>><?php echo $logs->type->ViewValue ?></div>
<?php } ?>
</td>
		<!-- message -->
		<td<?php echo $logs->message->CellAttributes() ?>>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { // Edit Record ?>
<input type="text" name="x<?php echo $RowIndex ?>_message" id="x<?php echo $RowIndex ?>_message" title="" size="30" maxlength="100" value="<?php echo $logs->message->EditValue ?>"<?php echo $logs->message->EditAttributes() ?>>
<?php } else { ?>
<div<?php echo $logs->message->ViewAttributes() ?>><?php echo $logs->message->ViewValue ?></div>
<?php } ?>
</td>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { ?>
<?php if ($logs->CurrentAction == "gridedit") { ?>
<input type="hidden" name="k<?php echo $RowIndex ?>_key" id="k<?php echo $RowIndex ?>_key" value="<?php echo ew_HtmlEncode($logs->id->CurrentValue) ?>">
<?php } ?>
<?php } else { ?>
<?php if ($logs->Export == "") { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $logs->ViewUrl() ?>">查看</a>
</span></td>
<?php } ?>
<?php if ($Security->IsLoggedIn()) { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $logs->DeleteUrl() ?>">删除</a>
</span></td>
<?php } ?>
<?php } ?>
<?php } ?>
	</tr>
<?php if ($logs->RowType == EW_ROWTYPE_EDIT) { ?>
<?php } ?>
<?php
	}
	$rs->MoveNext();
}
?>
</table>
<?php if ($logs->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<?php if ($logs->CurrentAction <> "gridedit") { // Not grid edit mode ?>
<?php if ($Security->IsLoggedIn()) { ?>
<?php if ($nTotalRecs > 0) { ?>
<a href="logslist.php?a=gridedit">批量编辑</a>&nbsp;&nbsp;
<?php } ?>
<?php } ?>
<?php } else { // Grid edit mode ?>
<a href="" onClick="if (ew_ValidateForm(document.flogslist)) document.flogslist.submit();return false;">保存</a>&nbsp;&nbsp;
<a href="logslist.php?a=cancel">取消</a>&nbsp;&nbsp;
<?php } ?>
	</span></td></tr>
</table>
<?php } ?>
<?php } ?>
<?php if ($logs->CurrentAction == "gridedit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="key_count" id="key_count" value="<?php echo $RowIndex ?>">
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
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">第&nbsp;</span></td>
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
	<td><span class="phpmaker">&nbsp;页 在 <?php echo $Pager->PageCount ?>中</span></td>
	</tr></table>
	<span class="phpmaker">第  <?php echo $Pager->FromIndex ?> 条 到 第 <?php echo $Pager->ToIndex ?> 条记录,总共 <?php echo $Pager->RecordCount ?></span>
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

//  Exit out of inline mode
function ClearInlineMode() {
	global $logs;
	$logs->CurrentAction = ""; // Clear action
	$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
}

// Switch to Grid Edit Mode
function GridEditMode() {
	$_SESSION[EW_SESSION_INLINE_MODE] = "gridedit"; // Enable grid edit
}

// Peform update to grid
function GridUpdate() {
	global $conn, $objForm, $logs;
	$rowindex = 1;
	$bGridUpdate = TRUE;

	// Begin transaction
	$conn->BeginTrans();
	$sKey = "";

	// Update row index and get row key
	$objForm->Index = $rowindex;
	$sThisKey = strval($objForm->GetValue("k_key"));

	// Update all rows based on key
	while ($sThisKey <> "") {

		// Load all values & keys
		LoadFormValues(); // Get form values
		if (LoadKeyValues($sThisKey)) { // Get key values
			$logs->SendEmail = FALSE; // Do not send email on update success
			$bGridUpdate = EditRow(); // Update this row
		} else {
			$bGridUpdate = FALSE; // update failed
		}
		if ($bGridUpdate) {
			if ($sKey <> "") $sKey .= ", ";
			$sKey .= $sThisKey;
		} else {
			break;
		}

		// Update row index and get row key
		$rowindex++; // next row
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue("k_key"));
	}
	if ($bGridUpdate) {
		$conn->CommitTrans(); // Commit transaction
		$_SESSION[EW_SESSION_MESSAGE] = "Update successful"; // Set update success message
		ClearInlineMode(); // Clear inline edit mode
	} else {
		$conn->RollbackTrans(); // Rollback transaction
		if (@$_SESSION[EW_SESSION_MESSAGE] == "") {
			$_SESSION[EW_SESSION_MESSAGE] = "Update failed"; // Set update failed message
		}
		$logs->EventCancelled = TRUE; // Set event cancelled
		$logs->CurrentAction = "gridedit"; // Stay in gridedit mode
	}
}

// Load key values
function LoadKeyValues($sKey) {
	global $logs;
	$arrKeyFlds = explode(EW_COMPOSITE_KEY_SEPARATOR, strval($sKey));
	if (count($arrKeyFlds) >= 1) {
		$logs->id->setFormValue($arrKeyFlds[0]);
		if (!is_numeric($logs->id->FormValue)) {
			return FALSE;
		}
	}
	return TRUE;
}

// Restore form values for current row
function RestoreCurrentRowFormValues() {
	global $objForm, $logs;

	// Update row index and get row key
	$rowindex = 1;
	$objForm->Index = $rowindex;
	$sKey = strval($objForm->GetValue("k_key"));
	while ($sKey <> "") {
		$arrKeyFlds = explode(EW_COMPOSITE_KEY_SEPARATOR, strval($sKey));
		if (count($arrKeyFlds) >= 1) {
			if (strval($arrKeyFlds[0]) == strval($logs->id->CurrentValue)) {
				$objForm->Index = $rowindex;
				LoadFormValues(); // Load form values
				return;
			}
		}

		// Update row index and get row key
		$rowindex++;
		$objForm->Index = $rowindex;
		$sKey = strval($objForm->GetValue("k_key"));
	}
}

// Return Advanced Search Where based on QueryString parameters
function AdvancedSearchWhere() {
	global $Security, $logs;
	$sWhere = "";

	// Field id
	BuildSearchSql($sWhere, $logs->id, @$_GET["x_id"], @$_GET["z_id"], @$_GET["v_id"], @$_GET["y_id"], @$_GET["w_id"]);

	// Field time
	BuildSearchSql($sWhere, $logs->time, ew_UnFormatDateTime(@$_GET["x_time"],9), @$_GET["z_time"], @$_GET["v_time"], ew_UnFormatDateTime(@$_GET["y_time"],9), @$_GET["w_time"]);

	// Field client
	BuildSearchSql($sWhere, $logs->client, @$_GET["x_client"], @$_GET["z_client"], @$_GET["v_client"], @$_GET["y_client"], @$_GET["w_client"]);

	// Field group
	BuildSearchSql($sWhere, $logs->group, @$_GET["x_group"], @$_GET["z_group"], @$_GET["v_group"], @$_GET["y_group"], @$_GET["w_group"]);

	// Field type
	BuildSearchSql($sWhere, $logs->type, @$_GET["x_type"], @$_GET["z_type"], @$_GET["v_type"], @$_GET["y_type"], @$_GET["w_type"]);

	// Field message
	BuildSearchSql($sWhere, $logs->message, @$_GET["x_message"], @$_GET["z_message"], @$_GET["v_message"], @$_GET["y_message"], @$_GET["w_message"]);

	//AdvancedSearchWhere = sWhere
	// Set up search parm

	if ($sWhere <> "") {

		// Field id
		SetSearchParm($logs->id, @$_GET["x_id"], @$_GET["z_id"], @$_GET["v_id"], @$_GET["y_id"], @$_GET["w_id"]);

		// Field time
		SetSearchParm($logs->time, ew_UnFormatDateTime(@$_GET["x_time"],9), @$_GET["z_time"], @$_GET["v_time"], ew_UnFormatDateTime(@$_GET["y_time"],9), @$_GET["w_time"]);

		// Field client
		SetSearchParm($logs->client, @$_GET["x_client"], @$_GET["z_client"], @$_GET["v_client"], @$_GET["y_client"], @$_GET["w_client"]);

		// Field group
		SetSearchParm($logs->group, @$_GET["x_group"], @$_GET["z_group"], @$_GET["v_group"], @$_GET["y_group"], @$_GET["w_group"]);

		// Field type
		SetSearchParm($logs->type, @$_GET["x_type"], @$_GET["z_type"], @$_GET["v_type"], @$_GET["y_type"], @$_GET["w_type"]);

		// Field message
		SetSearchParm($logs->message, @$_GET["x_message"], @$_GET["z_message"], @$_GET["v_message"], @$_GET["y_message"], @$_GET["w_message"]);
	}
	return $sWhere;
}

// Build search sql
function BuildSearchSql(&$Where, &$Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2) {
	$sWrk = "";
	$FldParm = substr($Fld->FldVar, 2);
	$FldVal = ew_StripSlashes($FldVal);
	if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
	$FldVal2 = ew_StripSlashes($FldVal2);
	if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
	$FldOpr = strtoupper(trim($FldOpr));
	if ($FldOpr == "") $FldOpr = "=";
	$FldOpr2 = strtoupper(trim($FldOpr2));
	if ($FldOpr2 == "") $FldOpr2 = "=";
	if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
		if ($FldVal <> "") $FldVal = ($FldVal == "1") ? $Fld->TrueValue : $Fld->FalseValue;
		if ($FldVal2 <> "") $FldVal2 = ($FldVal2 == "1") ? $Fld->TrueValue : $Fld->FalseValue;
	} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
		if ($FldVal <> "") $FldVal = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		if ($FldVal2 <> "") $FldVal2 = ew_UnFormatDateTime($FldVal2, $Fld->FldDateTimeFormat);
	}
	if ($FldOpr == "BETWEEN") {
		$IsValidValue = (($Fld->FldDataType <> EW_DATATYPE_NUMBER) ||
			($Fld->FldDataType == EW_DATATYPE_NUMBER && is_numeric($FldVal) && is_numeric($FldVal2)));
		if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
			$sWrk = $Fld->FldExpression . " BETWEEN " . ew_QuotedValue($FldVal, $Fld->FldDataType) .
				" AND " . ew_QuotedValue($FldVal2, $Fld->FldDataType);
		}
	} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL") {
		$sWrk = $Fld->FldExpression . " " . $FldOpr;
	} else {
		$IsValidValue = (($Fld->FldDataType <> EW_DATATYPE_NUMBER) ||
			($Fld->FldDataType == EW_DATATYPE_NUMBER && is_numeric($FldVal)));
		if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $Fld->FldDataType)) {
			$sWrk = $Fld->FldExpression . SearchString($FldOpr, $FldVal, $Fld->FldDataType);
		}
		$IsValidValue = (($Fld->FldDataType <> EW_DATATYPE_NUMBER) ||
			($Fld->FldDataType == EW_DATATYPE_NUMBER && is_numeric($FldVal2)));
		if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $Fld->FldDataType)) {
			if ($sWrk <> "") {
				$sWrk .= " " . (($FldCond=="OR")?"OR":"AND") . " ";
			}
			$sWrk .= $Fld->FldExpression . SearchString($FldOpr2, $FldVal2, $Fld->FldDataType);
		}
	}
	if ($sWrk <> "") {
		if ($Where <> "") $Where .= " AND ";
		$Where .= "(" . $sWrk . ")";
	}
}

// Return search string
function SearchString($FldOpr, $FldVal, $FldType) {
	if ($FldOpr == "LIKE" || $FldOpr == "NOT LIKE") {
		return " " . $FldOpr . " " . ew_QuotedValue("%" . $FldVal . "%", $FldType);
	} elseif ($FldOpr == "STARTS WITH") {
		return " LIKE " . ew_QuotedValue($FldVal . "%", $FldType);
	} else {
		return " " . $FldOpr . " " . ew_QuotedValue($FldVal, $FldType);
	}
}

// Set search parm
function SetSearchParm($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2) {
	global $logs;
	$FldParm = substr($Fld->FldVar, 2);
	$FldVal = ew_StripSlashes($FldVal);
	if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
	$FldVal2 = ew_StripSlashes($FldVal2);
	if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
	$logs->setAdvancedSearch("x_" . $FldParm, $FldVal);
	$logs->setAdvancedSearch("z_" . $FldParm, $FldOpr);
	$logs->setAdvancedSearch("v_" . $FldParm, $FldCond);
	$logs->setAdvancedSearch("y_" . $FldParm, $FldVal2);
	$logs->setAdvancedSearch("w_" . $FldParm, $FldOpr2);
}

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

	// Clear advanced search parameters
	ResetAdvancedSearchParms();
}

// Clear all basic search parameters
function ResetBasicSearchParms() {

	// Clear basic search parameters
	global $logs;
	$logs->setBasicSearchKeyword("");
	$logs->setBasicSearchType("");
}

// Clear all advanced search parameters
function ResetAdvancedSearchParms() {

	// Clear advanced search parameters
	global $logs;
	$logs->setAdvancedSearch("x_id", "");
	$logs->setAdvancedSearch("x_time", "");
	$logs->setAdvancedSearch("x_client", "");
	$logs->setAdvancedSearch("x_group", "");
	$logs->setAdvancedSearch("x_type", "");
	$logs->setAdvancedSearch("x_message", "");
}

// Restore all search parameters
function RestoreSearchParms() {
	global $sSrchWhere, $logs;
	$sSrchWhere = $logs->getSearchWhere();

	// Restore advanced search settings
	RestoreAdvancedSearchParms();
}

// Restore all advanced search parameters
function RestoreAdvancedSearchParms() {

	// Restore advanced search parms
	global $logs;
	 $logs->id->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_id");
	 $logs->time->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_time");
	 $logs->client->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_client");
	 $logs->group->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_group");
	 $logs->type->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_type");
	 $logs->message->AdvancedSearch->SearchValue = $logs->getAdvancedSearch("x_message");
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

// Load form values
function LoadFormValues() {

	// Load from form
	global $objForm, $logs;
	$logs->id->setFormValue($objForm->GetValue("x_id"));
	$logs->time->setFormValue($objForm->GetValue("x_time"));
	$logs->time->CurrentValue = ew_UnFormatDateTime($logs->time->CurrentValue, 9);
	$logs->client->setFormValue($objForm->GetValue("x_client"));
	$logs->group->setFormValue($objForm->GetValue("x_group"));
	$logs->type->setFormValue($objForm->GetValue("x_type"));
	$logs->message->setFormValue($objForm->GetValue("x_message"));
}

// Restore form values
function RestoreFormValues() {
	global $logs;
	$logs->id->CurrentValue = $logs->id->FormValue;
	$logs->time->CurrentValue = $logs->time->FormValue;
	$logs->time->CurrentValue = ew_UnFormatDateTime($logs->time->CurrentValue, 9);
	$logs->client->CurrentValue = $logs->client->FormValue;
	$logs->group->CurrentValue = $logs->group->FormValue;
	$logs->type->CurrentValue = $logs->type->FormValue;
	$logs->message->CurrentValue = $logs->message->FormValue;
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
		$logs->time->ViewValue = ew_FormatDateTime($logs->time->ViewValue, 9);
		$logs->time->CssStyle = "";
		$logs->time->CssClass = "";
		$logs->time->ViewCustomAttributes = "";

		// client
		$logs->client->ViewValue = $logs->client->CurrentValue;
		$logs->client->ViewValue = ew_Highlight($logs->client->ViewValue, $logs->getBasicSearchKeyword(), $logs->getBasicSearchType(), $logs->getAdvancedSearch("x_client"));
		$logs->client->CssStyle = "";
		$logs->client->CssClass = "";
		$logs->client->ViewCustomAttributes = "";

		// group
		$logs->group->ViewValue = $logs->group->CurrentValue;
		$logs->group->ViewValue = ew_Highlight($logs->group->ViewValue, $logs->getBasicSearchKeyword(), $logs->getBasicSearchType(), $logs->getAdvancedSearch("x_group"));
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
		$logs->message->ViewValue = ew_Highlight($logs->message->ViewValue, $logs->getBasicSearchKeyword(), $logs->getBasicSearchType(), $logs->getAdvancedSearch("x_message"));
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

		// id
		$logs->id->EditCustomAttributes = "";
		$logs->id->EditValue = $logs->id->CurrentValue;
		$logs->id->CssStyle = "";
		$logs->id->CssClass = "";
		$logs->id->ViewCustomAttributes = "";

		// time
		$logs->time->EditCustomAttributes = "";
		$logs->time->EditValue = ew_FormatDateTime($logs->time->CurrentValue, 9);

		// client
		$logs->client->EditCustomAttributes = "";
		$logs->client->EditValue = ew_HtmlEncode($logs->client->CurrentValue);

		// group
		$logs->group->EditCustomAttributes = "";
		$logs->group->EditValue = ew_HtmlEncode($logs->group->CurrentValue);

		// type
		$logs->type->EditCustomAttributes = "";
		$logs->type->EditValue = $logs->type->CurrentValue;

		// message
		$logs->message->EditCustomAttributes = "";
		$logs->message->EditValue = ew_HtmlEncode($logs->message->CurrentValue);
	} elseif ($logs->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$logs->Row_Rendered();
}
?>
<?php

// Update record based on key values
function EditRow() {
	global $conn, $Security, $logs;
	$sFilter = $logs->SqlKeyFilter();
	if (!is_numeric($logs->id->CurrentValue)) {
		return FALSE;
	}
	$sFilter = str_replace("@id@", ew_AdjustSql($logs->id->CurrentValue), $sFilter); // Replace key value
	$logs->CurrentFilter = $sFilter;
	$sSql = $logs->SQL();
	$conn->raiseErrorFn = 'ew_ErrorFn';
	$rs = $conn->Execute($sSql);
	$conn->raiseErrorFn = '';
	if ($rs === FALSE)
		return FALSE;
	if ($rs->EOF) {
		$EditRow = FALSE; // Update Failed
	} else {

		// Save old values
		$rsold =& $rs->fields;
		$rsnew = array();

		// Field id
		// Field time

		$logs->time->SetDbValueDef(ew_UnFormatDateTime($logs->time->CurrentValue, 9), ew_CurrentDate());
		$rsnew['time'] =& $logs->time->DbValue;

		// Field client
		$logs->client->SetDbValueDef($logs->client->CurrentValue, NULL);
		$rsnew['client'] =& $logs->client->DbValue;

		// Field group
		$logs->group->SetDbValueDef($logs->group->CurrentValue, NULL);
		$rsnew['group'] =& $logs->group->DbValue;

		// Field type
		$logs->type->SetDbValueDef($logs->type->CurrentValue, NULL);
		$rsnew['type'] =& $logs->type->DbValue;

		// Field message
		$logs->message->SetDbValueDef($logs->message->CurrentValue, NULL);
		$rsnew['message'] =& $logs->message->DbValue;

		// Call Row Updating event
		$bUpdateRow = $logs->Row_Updating($rsold, $rsnew);
		if ($bUpdateRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$EditRow = $conn->Execute($logs->UpdateSQL($rsnew));
			$conn->raiseErrorFn = '';
		} else {
			if ($logs->CancelMessage <> "") {
				$_SESSION[EW_SESSION_MESSAGE] = $logs->CancelMessage;
				$logs->CancelMessage = "";
			} else {
				$_SESSION[EW_SESSION_MESSAGE] = "Update cancelled";
			}
			$EditRow = FALSE;
		}
	}

	// Call Row Updated event
	if ($EditRow) {
		$logs->Row_Updated($rsold, $rsnew);
	}
	$rs->Close();
	return $EditRow;
}
?>
<?php

// Load advanced search
function LoadAdvancedSearch() {
	global $logs;
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
