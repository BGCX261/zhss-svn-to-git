<?php
define("EW_PAGE_ID", "view", TRUE); // Page ID
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
$archives->Export = @$_GET["export"]; // Get export parameter
$sExport = $archives->Export; // Get export parameter, used in header
$sExportFile = $archives->TableVar; // Get export file, used in header
?>
<?php
if (@$_GET["id"] <> "") {
	$archives->id->setQueryStringValue($_GET["id"]);
} else {
	Page_Terminate("archiveslist.php"); // Return to list page
}

// Get action
if (@$_POST["a_view"] <> "") {
	$archives->CurrentAction = $_POST["a_view"];
} else {
	$archives->CurrentAction = "I"; // Display form
}
switch ($archives->CurrentAction) {
	case "I": // Get a record to display
		if (!LoadRow()) { // Load record based on key
			$_SESSION[EW_SESSION_MESSAGE] = "No records found"; // Set no record message
			Page_Terminate("archiveslist.php"); // Return to list
		}
}
$key = trim(@$_GET[EW_TABLE_BASIC_SEARCH]);
$type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
if( $key != '' ) {
	if( $type == 'OR' || $type == 'AND' ) {
		$key = explode(' ',$key);
	}
}

// Set return url
$archives->setReturnUrl("archivesview.php");

// Render row
$archives->RowType = EW_ROWTYPE_VIEW;
RenderRow();
?>
<?php include "header.php" ?>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<!-- left column (begin) -->
		<td valign="top" class="ewMenuColumn">
		</td>
		<!-- left column (end) -->
		<!-- right column (begin) -->
		<td valign="top" class="ewContentColumn">
<p><b>中和集成监控系统</b></p>
<script type="text/javascript">
<!--
var EW_PAGE_ID = "view"; // Page id

//-->
</script>
<script language="JavaScript" type="text/javascript">
<!--

// Write your client script here, no need to add script tags.
// To include another .js script, use:
// ew_ClientScriptInclude("my_javascript.js"); 
//-->

</script>
<p><span class="phpmaker">
<a href="archiveslist.php">返回列表</a>&nbsp;
<?php if ($Security->CanAdd()) { ?>
<a href="archivesadd.php">添加</a>&nbsp;
<?php } ?>
<?php if ($Security->CanEdit()) { ?>
<a href="<?php echo $archives->EditUrl() ?>">编辑</a>&nbsp;
<?php } ?>
<?php if ($Security->CanDelete()) { ?>
<a href="<?php echo $archives->DeleteUrl() ?>">删除</a>&nbsp;
<?php } ?>
</span>
</p>
<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<p>
<form>
<table class="ewTable">
	<tr class="ewTableRow">
		<td class="ewTableHeader">id</td>
		<td<?php echo $archives->id->CellAttributes() ?>>
<div<?php echo $archives->id->ViewAttributes() ?>><?php echo $archives->id->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">来源</td>
		<td<?php echo $archives->url->CellAttributes() ?>>
<div<?php echo $archives->url->ViewAttributes() ?>><?php echo $archives->url->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">时间</td>
		<td<?php echo $archives->datetime->CellAttributes() ?>>
<div<?php echo $archives->datetime->ViewAttributes() ?>><?php echo $archives->datetime->ViewValue ?></div>
</td>
	</tr>
	<tr class="ewTableAltRow">
		<td class="ewTableHeader">标题</td>
		<td<?php echo $archives->title->CellAttributes() ?>>
<div<?php echo $archives->title->ViewAttributes() ?>><?php echo $archives->title->ViewValue ?><?php echo countword( $archives->content->ViewValue,$key) ?></div>
</td>
	</tr>
	</tr>
	<tr class="ewTableRow">
		<td class="ewTableHeader">内容</td>
		<td<?php echo $archives->content->CellAttributes() ?>>
<div<?php echo $archives->content->ViewAttributes() ?>><?php echo highlight($archives->content->ViewValue,$key) ?></div>
</td>
	</tr>
</table>
</form>
<p>
<script language="JavaScript" type="text/javascript">
<!--

// Write your table-specific startup script here
// document.write("page loaded");
//-->

</script>
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
	$archives->datetime->setDbValue($rs->fields('datetime'));
	$archives->title->setDbValue($rs->fields('title'));
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

	// datetime
	$archives->datetime->CellCssStyle = "";
	$archives->datetime->CellCssClass = "";

	// title
	$archives->title->CellCssStyle = "";
	$archives->title->CellCssClass = "";

	// content
	$archives->content->CellCssStyle = "";
	$archives->content->CellCssClass = "";
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

		// datetime
		$archives->datetime->ViewValue = $archives->datetime->CurrentValue;
		$archives->datetime->ViewValue = ew_FormatDateTime($archives->datetime->ViewValue, 9);
		$archives->datetime->CssStyle = "";
		$archives->datetime->CssClass = "";
		$archives->datetime->ViewCustomAttributes = "";

		// title
		$archives->title->ViewValue = $archives->title->CurrentValue;
		$archives->title->CssStyle = "";
		$archives->title->CssClass = "";
		$archives->title->ViewCustomAttributes = "";

		// content
		$archives->content->ViewValue = $archives->content->CurrentValue;
		if (!is_null($archives->content->ViewValue)) $archives->content->ViewValue = str_replace("\n", "<br>", $archives->content->ViewValue); 
		$archives->content->CssStyle = "";
		$archives->content->CssClass = "";
		$archives->content->ViewCustomAttributes = "";

		// id
		$archives->id->HrefValue = "";

		// url
		$archives->url->HrefValue = "";

		// datetime
		$archives->datetime->HrefValue = "";

		// title
		$archives->title->HrefValue = "";

		// content
		$archives->content->HrefValue = "";
	} elseif ($archives->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($archives->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($archives->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$archives->Row_Rendered();
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

// Page Load event
function Page_Load() {

	//echo "Page Load";
}

// Page Unload event
function Page_Unload() {

	//echo "Page Unload";
}
function highlight($str,$key) {
	if( is_array($key) ){
		foreach($key as $k ) {
			$str = highlight($str,trim($k));
		}
		return $str;
	}
	else if ( is_string($key) ) {
		if( $key == '' ) return $str;
		//return(str_ireplace($key,'<span style="color:#f00;font-weight:bold;text-decoration:underline">'.$key.'</span>',$str));
		return(preg_replace("/($key)/isU","<span style=\"color:#f00;font-weight:bold;text-decoration:underline\">$1</span>",$str));
	}
	return $str;
}
function countword($str,$key) {
	$count = count_word($str,$key);
	if( $count == 0 ){
		return "";
	}
	else  {
		return "<span style=\"color:#f00;font-weight:bold;text-decoration:underline\">($count)</span>";
	}
	return "";
}
function count_word($str,$key) {
	$count = 0;
	if( is_array($key) ){
		foreach($key as $k ) {
			$count = $count + substr_count($str, $k);
		}
		return $count;
	}
	else if ( is_string($key) ) {
		if( $key == '' ) return 0;
		//return(str_ireplace($key,'<span style="color:#f00;font-weight:bold;text-decoration:underline">'.$key.'</span>',$str));
		return(substr_count($str, $key));
	}
	return $count;
}
?>
