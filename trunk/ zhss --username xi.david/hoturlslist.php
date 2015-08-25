<?php
define("EW_PAGE_ID", "list", TRUE); // Page ID
define("EW_TABLE_NAME", 'hoturls', TRUE);
?>
<?php
$view = @$_GET['view'];
if( $view == '' ) {
	$view = @$_COOKIE['view'];
}
if( $view == '' ) {
	$view = 'normal';
}
setcookie('view',$view);
session_start(); // Initialize session data
ob_start(); // Turn on output buffering
?>
<?php include "ewcfg50.php" ?>
<?php include "ewmysql50.php" ?>
<?php include "phpfn50.php" ?>
<?php include "hoturlsinfo.php" ?>
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
$action = @$_POST['action'];
if( $action != '' ) {
	$ids = @$_POST['ids'];
	if( $action == 'delete' ) {
		$idstring = implode('","',$ids);
		$sql = 'DELETE FROM `hoturls` WHERE `id` IN ("' . $idstring . '")';
		$conn->Execute($sql);
	}
	else if( $action == 'archive' ) {
		$idstring = implode('","',$ids);
		$sql = 'SELECT * FROM `hoturls`  WHERE `id` IN ("' . $idstring . '")';
		echo $sql;
		$rs = $conn->Execute($sql);
		while(!$rs->EOF) {
			$title = $rs->fields('title');
			$url = $rs->fields('url');
			$projectname = $rs->fields('projectname');
			$content = $rs->fields('content');
			if( checkArchive($url) ) {
				$hsql = 'INSERT INTO `archives`(`url`,`projectname`,`title`,`content`,`datetime`) VALUES("' . mysql_escape_string($url) . '","'.mysql_escape_string($projectname).'","' . mysql_escape_string($title) . '","' . mysql_escape_string($content) . '",NOW())';
				$conn->Execute($hsql);
			}
			$rs->MoveNext();
			
		}
		$rs->Close();
	}
	Page_Terminate("hoturlslist.php");
}
function checkArchive($url) {
	global $conn;
	$sql = 'SELECT COUNT(`url`) FROM `archives` WHERE `url`="' . $url . '"';
	$cnt = 0;
	if ($rs = $conn->Execute($sql)) {
		if (!$rs->EOF) $cnt = $rs->fields[0];
		$rs->Close();
	}
	return($cnt==0);
}
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
	if( $view == 'classic' ) {
		$hoturls->setSearchWhere($sSrchWhere); // Save to Session
	}
	else {
		$hoturls->setSearchWhere($sSrchWhere); // Save to Session
	}
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
if( $view == 'classic' ) {
	$hoturls->setSessionWhere($sFilter);
}
else {
	$hoturls->setSessionWhere($sFilter);
}
$hoturls->CurrentFilter = "";

// Set Up Sorting Order
SetUpSortOrder();

// Set Return Url
$hoturls->setReturnUrl("hoturlslist.php");
?>
<?php include "header.php" ?>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<!-- left column (begin) -->
		<td valign="top" class="ewMenuColumn">
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
if( $view == 'classic' ) {
	$nTotalRecs = ($bSelectLimit) ? $hoturls->SelectRecordCount() : $rs->RecordCount();
}
else {
	$nTotalRecs = ($bSelectLimit) ? $hoturls->SelectRecordCount() : $rs->RecordCount();
}
$nStartRec = 1;
if ($nDisplayRecs <= 0) $nDisplayRecs = $nTotalRecs; // Display all records
if (!$bExportAll) SetUpStartRec(); // Set up start record position
if( $view == 'classic' ) {
	if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
}
else {
	if ($bSelectLimit) $rs = LoadRecordset($nStartRec-1, $nDisplayRecs);
}

?>
<?php if ($hoturls->Export == "") { ?>
<?php if ($Security->IsLoggedIn()) { ?>
<form name="fhoturlslistsrch" id="fhoturlslistsrch" action="hoturlslist.php" >
<div class="menuhead">命中数据</div>
<table class="ewBasicSearch">
	<tr>
		<td width="60">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH ?>">关键词:</label>
		</td>
		<td>
			<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="searchInput" size="10" value="<?php echo $hoturls->getBasicSearchKeyword() ?>"></p>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>">起始时间:</label>
		</td>
		<td>
			<input type="text" value="<?php echo $hoturls->getBasicSearchStartTime() ?>" class="dateinput searchInput" onkeypress="return false" size="9" id="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>" name="<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>"/></p>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>">结束时间:</label>
		</td>
		<td>
			<input type="text" value="<?php echo $hoturls->getBasicSearchEndTime() ?>" class="dateinput searchInput" onkeypress="return false" size="9" id="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>" name="<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>"/></p>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<label for="<?php echo EW_TABLE_BASIC_SEARCH_SCOPE ?>">网站范围:</label>
		</td>
		<td valign="top">
			<select multiple="multiple" class="searchInput" id="<?php echo EW_TABLE_BASIC_SEARCH_SCOPE ?>" size="3" name="<?php echo EW_TABLE_BASIC_SEARCH_SCOPE ?>[]">
<?php
//$csql = 'SELECT * FROM `hoturls` WHERE `projectname`<>"" GROUP BY `projectname`';
$hsql = 'SELECT * FROM `hoturls` WHERE `projectname`<>"" GROUP BY `projectname`';
$hrs = $conn->Execute($hsql);
if( $hrs->_numOfRows > 0 ) {
	while( !$hrs->EOF && $hrs->fields('projectname') != '' ) {
		echo '<option value="' . $hrs->fields('projectname') . '">' . $hrs->fields('projectname') . '</option>';
		$hrs->MoveNext();
	}
}
if ($hrs) $hrs->Close();
?>
			</select>
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
			<input type="Submit" value="搜索 (*)">&nbsp;
			<a href="hoturlslist.php?cmd=reset">显示全部</a>&nbsp;
<?php
	if( isset($view) && $view == 'classic' ) {
		echo '<input type="hidden" name="view" value="classic" />';
	}
	else {
		echo '<input type="hidden" name="view" value="normal" />';
	}
?>
		</td>
	</tr>
</table>
<script type="text/javascript">
Calendar.setup({
	inputField     :    "<?php echo EW_TABLE_BASIC_SEARCH_START_TIME ?>",
	ifFormat       :    "%Y-%m-%d %H:%M ", 
	showsTime      :    true,
	onUpdate       :    null
});
Calendar.setup({
	inputField     :    "<?php echo EW_TABLE_BASIC_SEARCH_END_TIME ?>",
	ifFormat       :    "%Y-%m-%d %H:%M ",
	showsTime      :    true,
	onUpdate       :    null
});
</script>
</form>
<?php
//$csql = 'SELECT * FROM `hoturls` WHERE `projectname`<>"" GROUP BY `projectname`';
$csql = 'SELECT * FROM `categories`';
$crs = $conn->Execute($csql);
if( $crs->_numOfRows > 0 ) {
?>

<script type="text/javascript">
var hasClass = function(ele,cls) {
	return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
};
var addClass = function(ele,cls) {
	if (!this.hasClass(ele,cls)) ele.className += " "+cls;
};
var removeClass = function(ele,cls) {
	if (hasClass(ele,cls)) {
		var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
		ele.className=ele.className.replace(reg,' ');
	}
};
var $ = function(element) {
	if (typeof element == 'string') element = document.getElementById(element);
	return element;
};
function switchTree(id) {
	var r = $('tree_r_' + id);
	var t = $('tree_t_' + id);
	if( r != null && typeof r != 'undefined' ) {
		var s = '';
		if( hasClass(r,'r_o' ) ) {
			s = 'r_c';
		}
		else {
			s = 'r_o';
		}
		removeClass(r,'r_o');
		removeClass(r,'r_c');
		addClass(r,s);
	}
	if( t != null && typeof t != 'undefined' ) {
		var s = '';
		if( hasClass(t,'t_o' ) ) {
			s = 't_c';
		}
		else {
			s = 't_o';
		}
		removeClass(t,'t_o');
		removeClass(t,'t_c');
		addClass(t,s);
	}
}
</script>
<div class="menuhead">分类列表</div>
<table class="ewBasicSearch">
	<tr>
		<td>
			<ul class="r_t">
<?php
while( !$crs->EOF && $crs->fields('name') != '' && $crs->fields('keywords') != '' ) {
	echo '<li class="r_c" id="tree_r_' . $crs->fields('id') . '">';
	echo '<span class="switch" onclick="switchTree(\'' . $crs->fields('id') . '\')">' . $crs->fields('keywords') . '</span>';
	$hsql = 'SELECT * FROM `hoturls` WHERE `projectname`<>"" AND `categories`="' . $crs->fields('name') . '" GROUP BY `projectname`';
	$hrs = $conn->Execute($hsql);
	if( $hrs->_numOfRows > 0 ) {
		echo '<ul class="t_t t_c" id="tree_t_' . $crs->fields('id') . '">';
		while( !$hrs->EOF && $hrs->fields('projectname') != '' ) {
			echo '<li>';
			echo '<a href="hoturlslist.php?category=' . $crs->fields('name') . '&projectname=' . rawurlencode($hrs->fields('projectname')) . '">' . $hrs->fields('projectname') . '</a>';
			echo '</li>';
			$hrs->MoveNext();
		}
		echo '</ul>';
	}
	echo '</li>';
	$crs->MoveNext();
}
if ($hrs) $hrs->Close();
if ($crs) $crs->Close();
?>
			</ul>
		</td>
	</tr>
</table>
<?php
}
?>
<?php } ?>
<?php } ?>
		</td>
		<!-- left column (end) -->
		<!-- right column (begin) -->
		<td valign="top" class="ewContentColumn">
<?php
	$getArray = array();
	foreach( $_GET as $key => $value ) {
		if( $key != 'view' ) {
			array_push($getArray,$key . '=' . rawurlencode($value));
		}
	}
	$get = implode('&',$getArray);
	$url = 'hoturlslist.php';
	if( $get != '' ) {
		$classicUrl = $url . '?view=classic&' . $get;
		$normalUrl = $url . '?view=normal&' . $get;
	}
	else {
		$classicUrl = $url . '?view=classic';
		$normalUrl = $url . '?view=normal';
	}
?>
<p class="ptitle"><b>中和集成监控系统</b><span><a href="<?php echo $classicUrl ?>">传统视图</a><a href="<?php echo $normalUrl ?>">默认视图</a></span></p>

<?php
if (@$_SESSION[EW_SESSION_MESSAGE] <> "") {
?>
<p><span class="ewmsg"><?php echo $_SESSION[EW_SESSION_MESSAGE] ?></span></p>
<?php
	$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message
}
?>
<form method="post" name="fhoturlslist" id="fhoturlslist" action="hoturlslist.php">
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<script type="text/javascript">
	function deleteSelected() {
		if( confirm('确实要删除选中项目吗？') ) {
			document.getElementById('action').value = 'delete';
			document.getElementById('fhoturlslist').submit();
		}
		document.getElementById('action').value = '';
		return false;
	}
	function archiveSelected() {
		if( confirm('确实要归档选中项目吗？') ) {
			document.getElementById('action').value = 'archive';
			document.getElementById('fhoturlslist').submit();
		}
		document.getElementById('action').value = '';
		return false;
	}
	function checkAll(){
		var cbx = document.forms["fhoturlslist"].elements["ids[]"];
		var chk = document.forms["fhoturlslist"].elements["chkAll"];
		for(var i = 0;i < cbx.length;i ++){
				cbx[i].checked = chk.checked;
		}
	}
	
</script>
<?php } ?>
<?php if ($nTotalRecs > 0) { ?>
<?php
	if( isset($view) && $view == 'classic' ) {
		$keywords = trim($hoturls->getBasicSearchKeyword());
		if( $hoturls->getBasicSearchType() == 'OR' || $hoturls->getBasicSearchType() == 'AND' ) {
			$keywords = explode(' ',$keywords);
		}
	?>
		<table id="ewlistmain" class="ewTable">
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td width="20"><input type="hidden" name="action" id="action" value=""/></td>
<?php } ?>		
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
			LoadRowValues($rs); // Load row values
			$hoturls->RowType = EW_ROWTYPE_VIEW; // Render view
			RenderRow();
			$url_info = parse_url($hoturls->url->ViewValue);
		?>
			<!-- Table body -->
			<tr>
				<td>
					<div><input type="checkbox" name="ids[]" value="<?php echo $hoturls->id->ViewValue ?>"/><a href="<?php echo $hoturls->url->ViewValue ?>" target="_blank"><?php echo $hoturls->title->ViewValue ?><?php echo countword( $hoturls->content->ViewValue,$keywords) ?></a></div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="color:#000"><?php echo highlight(substr($hoturls->content->ViewValue,0,800),$keywords) ?></div>
				</td>
			</tr>
			<tr>
				<td>
					<span style="color:green"><?php echo (isset($url_info['host'])?$url_info['host']:'') . (isset($url_info['path'])?$url_info['path']:'') . '&nbsp;&nbsp;' . $hoturls->datetime->ViewValue ?></span>
					 - <a href="<?php echo $viewpath . '/' . (isset($url_info['host'])?$url_info['host']:'') . '/' . md5($hoturls->url->ViewValue) ?>.html" target="_blank">快照</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<br>
				</td>
			</tr>
		<?php
			}
			$rs->MoveNext();
		}
		?>
		</table>
	<?php
	}
	else {
	?>
	<?php
		$keywords = trim($hoturls->getBasicSearchKeyword());
		if( $hoturls->getBasicSearchType() == 'OR' || $hoturls->getBasicSearchType() == 'AND' ) {
			$keywords = explode(' ',$keywords);
		}
	?>		
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
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td width="20"><input type="hidden" name="action" id="action" value=""/></td>
<?php } ?>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
id
<?php } else { ?>
	编号
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
url
<?php } else { ?>
	站点名称&nbsp;(*)
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
datetime
<?php } else { ?>
	时间
<?php } ?>
		</td>
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
title
<?php } else { ?>
	网页标题&nbsp;(*)
<?php } ?>
		</td>		
		<td valign="top">
<?php if ($hoturls->Export <> "") { ?>
categories
<?php } else { ?>
	分类&nbsp;(*)
<?php } ?>
		</td>
<?php if ($hoturls->Export == "") { ?>
<?php if ($Security->CanView()) { ?>
<td nowrap>&nbsp;</td>
<?php } ?>
<?php if ($Security->CanEdit()) { ?>
<td nowrap>&nbsp;</td>
<?php } ?>
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
		if( $hoturls->getBasicSearchKeyword() == '' ) {
			$keyaction = '';
		}
		else {
			$keyaction = EW_TABLE_BASIC_SEARCH . '=' . rawurlencode($hoturls->getBasicSearchKeyword()) . '&' . EW_TABLE_BASIC_SEARCH_TYPE . '=' . $hoturls->getBasicSearchType();
		}		
		while (!$rs->EOF && $nRecCount < $nStopRec) {
			$nRecCount++;
			if (intval($nRecCount) >= intval($nStartRec)) {
				$RowCnt++;
			LoadRowValues($rs); // Load row values
			$hoturls->RowType = EW_ROWTYPE_VIEW; // Render view
			RenderRow();
			$url_info = parse_url($hoturls->url->ViewValue);
		?>
	<!-- Table body -->
	<tr<?php echo $hoturls->DisplayAttributes() ?>>
<?php if ($Security->CanDelete()||$Security->CanArchive()) { ?>
<td nowrap><input type="checkbox" name="ids[]" value="<?php echo $hoturls->id->ViewValue ?>"/></td>
<?php } ?>
		<!-- id -->
		<td<?php echo $hoturls->id->CellAttributes() ?>>
<div<?php echo $hoturls->id->ViewAttributes() ?>><?php echo $hoturls->id->ViewValue ?></div>
</td>
		<!-- url -->
		<td<?php echo $hoturls->projectname->CellAttributes() ?>>
<div<?php echo $hoturls->projectname->ViewAttributes() ?>><a href="<?php echo $hoturls->url->ViewValue ?>" target="_blank"><?php echo $hoturls->projectname->ViewValue ?></a></div>
</td>
		<!-- datetime -->
		<td<?php echo $hoturls->datetime->CellAttributes() ?>>
<div<?php echo $hoturls->datetime->ViewAttributes() ?>><?php echo $hoturls->datetime->ViewValue ?></div>
</td>
		<!-- title -->
		<td<?php echo $hoturls->title->CellAttributes() ?>>
<div<?php echo $hoturls->title->ViewAttributes() ?>><?php echo $hoturls->title->ViewValue ?><?php echo countword( $hoturls->content->ViewValue,$keywords) ?> </div>
</td>
		<!-- categories -->
		<td<?php echo $hoturls->categories->CellAttributes() ?>>
<div<?php echo $hoturls->categories->ViewAttributes() ?>><?php echo $hoturls->categories->ViewValue ?></div>
</td>
<?php if ($hoturls->Export == "") { ?>
<?php if ($Security->CanView()) { ?>
<td nowrap><span class="phpmaker">
<a href="<?php echo $hoturls->ViewUrl($keyaction) ?>">查看</a>
</span></td>
<?php } ?>
<?php if ($Security->CanEdit()) { ?>
<?php } ?>
<?php } ?>
	</tr>
<?php
	}
	$rs->MoveNext();
}
?>
</table>

<?php } ?>
<?php } ?>
<?php if ($hoturls->Export == "") { ?>
<table>
	<tr><td><span class="phpmaker">
<?php
	echo '<input type="checkbox" onclick="checkAll()" name="chkAll" />全选';
?>	
<?php if ($Security->CanDelete()) { ?>
<?php
	echo '<input type="button" value="删除选中" onclick="deleteSelected()" />';
?>		
<?php } ?>
<?php if ($Security->CanArchive()) { ?>
<?php
	echo '<input type="button" value="归档选中" onclick="archiveSelected()" />';
?>		
<?php } ?>
	</span></td></tr>
</table>
<?php } ?>
</form>
<?php

// Close recordset and connection
if ($rs) $rs->Close();
?>
<?php if ($hoturls->Export == "") { ?>
<form action="hoturlslist.php" name="ewpagerform" id="ewpagerform">
<?php
?>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td nowrap>
<?php if (!isset($Pager)) $Pager = new cPrevNextPager($nStartRec, $nDisplayRecs, $nTotalRecs) ?>
<?php if ($Pager->RecordCount > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td><span class="phpmaker">跳转到第</span></td>
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
	<td><span class="phpmaker"> 页</span></td>
	</tr></table>
	<span class="phpmaker">第 <?php echo $Pager->FromIndex ?> 条 到 第 <?php echo $Pager->ToIndex ?> 条记录,总共 <?php echo $Pager->RecordCount ?>条记录</span>
<?php } else { ?>
	<?php if ($sSrchWhere == "0=101") { ?>
	<span class="phpmaker">请输入搜索标准</span>
	<?php } else { ?>
	<span class="phpmaker">没有找到任何记录</span>
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
function parseTime($starttime,$endtime) {
	$timestamp = 0;
	$startSql = $endSql = '';
	if( $starttime != '' ) {
		$startSql = 'UNIX_TIMESTAMP(h.`datetime`)>=UNIX_TIMESTAMP("' . trim($starttime) . '")';
	}
	if( $endtime != '' ) {
		$endSql = 'UNIX_TIMESTAMP(h.`datetime`)<=UNIX_TIMESTAMP("' . trim($endtime) . '")';
	}
	$sql = $startSql;
	if( $endSql != null ) {
		if( $sql != '' ) $sql .= ' AND ';
		$sql = '(' . $sql . $endSql . ')';
	}
	return $sql;
}
function parseScope($searchscope) {
	$sql = '';
	if( count($searchscope) > 0 ) {
		$scope = implode('","',$searchscope);
		$sql = '(h.`projectname` in ("' . $scope . '"))';
	}
	return $sql;
}
function parseProjectName($projectname,$category) {
	$sql = '';
	if( isset($projectname) && !empty($projectname) ) {
		$sql = 'h.`projectname`="' . $projectname . '"';
	}
      $csplit = split(",",$category);
      
      foreach( $csplit as $cate ) {
                if( !empty($sql) ) $sql .= ' AND ';
                if ($cate != ''){
                $sql .= '(h.`categories` like "%' . $cate . '%")';
            }
      }
      echo $sql;
	if( !empty($sql) ) $sql = '(' . $sql . ')';
	return $sql;

}
// Return Basic Search Where based on search keyword and type
function BasicSearchWhere() {
	global $Security, $hoturls;
	$sSearchStr = "";
	$sSearchKeyword = ew_StripSlashes(@$_GET[EW_TABLE_BASIC_SEARCH]);
	$sSearchType = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	$sSearchStartTime = @$_GET[EW_TABLE_BASIC_SEARCH_START_TIME];
	$sSearchEndTime = @$_GET[EW_TABLE_BASIC_SEARCH_END_TIME];
	$sSearchScope = @$_GET[EW_TABLE_BASIC_SEARCH_SCOPE];
	$sSearchProjectName = @$_GET[EW_TABLE_BASIC_SEARCH_PROJECTNAME];
	$sSearchCategory = @$_GET[EW_TABLE_BASIC_SEARCH_CATEGORY];
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
		$sSearchStrTime = parseTime($sSearchStartTime,$sSearchEndTime);
		if( $sSearchStrTime <> "" ) {
			if( $sSearchStr <> "" ) {
				$sSearchStr = '(' . $sSearchStr . ') AND ';
			}
			$sSearchStr .= $sSearchStrTime;
		}
		$sSearchStrScope = parseScope($sSearchScope);
		if( $sSearchStrScope <> "" ) {
			if( $sSearchStr <> "" ) {
				$sSearchStr = '(' . $sSearchStr . ') AND ';
			}
			$sSearchStr .= $sSearchStrScope;
		}
		$hoturls->setBasicSearchKeyword($sSearchKeyword);
		$hoturls->setBasicSearchType($sSearchType);
		$hoturls->setBasicSearchStartTime($sSearchStartTime);
		$hoturls->setBasicSearchEndTime($sSearchEndTime);
		$hoturls->setBasicSearchScope($sSearchScope);
	}
	$sSearchStrProjectName = parseProjectName($sSearchProjectName,$sSearchCategory);
	if( $sSearchStrProjectName <> "" ) {
		if( $sSearchStr <> "" ) {
			$sSearchStr = '(' . $sSearchStr . ') AND ';
		}
		$sSearchStr .= $sSearchStrProjectName;
	}
	$hoturls->setBasicSearchProjectName($sSearchProjectName);
	$hoturls->setBasicSearchCategory($sSearchCategory);
	return $sSearchStr;
}

// Clear all search parameters
function ResetSearchParms() {

	// Clear search where
	global $hoturls,$view;
	$sSrchWhere = "";
	if( $view == 'classic' ) {
		$hoturls->setSearchWhere($sSrchWhere);
	}
	else {
		$hoturls->setSearchWhere($sSrchWhere);
	}

	// Clear basic search parameters
	ResetBasicSearchParms();
}

// Return Basic Search sql
function BasicSearchSQL($Keyword) {
	$sKeyword = ew_AdjustSql($Keyword);
	$sql = "";
	$sql .= "`url` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= "`categories` LIKE '%" . $sKeyword . "%' OR ";
	$sql .= '`title` LIKE "%' . $sKeyword . '%" OR `content` LIKE "%' . $sKeyword . '%" ';
	if (substr($sql, -4) == " OR ") $sql = substr($sql, 0, strlen($sql)-4);
	return $sql;
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
	global $sSrchWhere, $hoturls,$view;
	if( $view == 'classic' ) {
		$sSrchWhere = $hoturls->getSearchWhere();
	}
	else {
		$sSrchWhere = $hoturls->getSearchWhere();
	}
}

// Set up Sort parameters based on Sort Links clicked
function SetUpSortOrder() {
	global $hoturls,$view;

	// Check for an Order parameter
	if (@$_GET["order"] <> "") {
		$hoturls->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
		$hoturls->CurrentOrderType = @$_GET["ordertype"];

		// Field id
		$hoturls->UpdateSort($hoturls->id);

		// Field url
		$hoturls->UpdateSort($hoturls->url);

		// Field datetime
		$hoturls->UpdateSort($hoturls->datetime);

		// Field categories
		$hoturls->UpdateSort($hoturls->categories);
		$hoturls->setStartRecordNumber(1); // Reset start position
	}
	$sOrderBy = $hoturls->getSessionOrderBy(); // Get order by from Session
	if ($sOrderBy == "") {
		if ($hoturls->SqlOrderBy() <> "") {
			$sOrderBy = $hoturls->SqlOrderBy();
			if( $view == 'classic' ) {
				$hoturls->setSessionOrderBy($sOrderBy);
			}
		}
	}
}

// Reset command based on querystring parameter cmd=
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters
function ResetCmd() {
	global $sDbMasterFilter, $sDbDetailFilter, $nStartRec, $sOrderBy;
	global $hoturls,$view;

	// Get reset cmd
	if (@$_GET["cmd"] <> "") {
		$sCmd = $_GET["cmd"];

		// Reset search criteria
		if (strtolower($sCmd) == "reset" || strtolower($sCmd) == "resetall") {
			if( $view == 'classic' ) {
				ResetSearchParms();
			}
			else {
				ResetSearchParms();
			}
		}

		// Reset Sort Criteria
		if (strtolower($sCmd) == "resetsort") {
			$sOrderBy = "";
			$hoturls->setSessionOrderBy($sOrderBy);
			$hoturls->id->setSort("");
			$hoturls->url->setSort("");
			$hoturls->content->setSort("");
			$hoturls->datetime->setSort("");
			$hoturls->categories->setSort("");
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
	$hoturls->projectname->setDbValue($rs->fields('projectname'));
	$hoturls->url->setDbValue($rs->fields('url'));
	$hoturls->content->setDbValue($rs->fields('content'));
	$hoturls->title->setDbValue($rs->fields('title'));
	$hoturls->datetime->setDbValue($rs->fields('datetime'));
	$hoturls->categories->setDbValue($rs->fields('categories'));
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

	// projectname
	$hoturls->projectname->CellCssStyle = "";
	$hoturls->projectname->CellCssClass = "";
	
	// url
	$hoturls->url->CellCssStyle = "";
	$hoturls->url->CellCssClass = "";

	// datetime
	$hoturls->datetime->CellCssStyle = "";
	$hoturls->datetime->CellCssClass = "";
	
	// content
	$hoturls->content->CellCssStyle = "";
	$hoturls->content->CellCssClass = "";
	
	// title
	$hoturls->title->CellCssStyle = "";
	$hoturls->title->CellCssClass = "";

	// categories
	$hoturls->categories->CellCssStyle = "";
	$hoturls->categories->CellCssClass = "";
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
		
		// content
		$hoturls->content->ViewValue = $hoturls->content->CurrentValue;
		$hoturls->content->CssStyle = "";
		$hoturls->content->CssClass = "";
		$hoturls->content->ViewCustomAttributes = "";
		
		// title
		$hoturls->title->ViewValue = $hoturls->title->CurrentValue;
		$hoturls->title->CssStyle = "";
		$hoturls->title->CssClass = "";
		$hoturls->title->ViewCustomAttributes = "";

		// datetime
		$hoturls->datetime->ViewValue = $hoturls->datetime->CurrentValue;
		$hoturls->datetime->ViewValue = ew_FormatDateTime($hoturls->datetime->ViewValue, 9);
		$hoturls->datetime->CssStyle = "";
		$hoturls->datetime->CssClass = "";
		$hoturls->datetime->ViewCustomAttributes = "";

		// categories
		$hoturls->categories->ViewValue = $hoturls->categories->CurrentValue;
		$hoturls->categories->CssStyle = "";
		$hoturls->categories->CssClass = "";
		$hoturls->categories->ViewCustomAttributes = "";

		// id
		$hoturls->id->HrefValue = "";

		// url
		$hoturls->url->HrefValue = "";
		
		// projectname
		$hoturls->projectname->HrefValue = "";
		
		// content
		$hoturls->content->HrefValue = "";
		
		// title
		$hoturls->title->HrefValue = "";

		// datetime
		$hoturls->datetime->HrefValue = "";

		// categories
		$hoturls->categories->HrefValue = "";
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
function LoadCategoryRecordset($offset = -1, $rowcnt = -1) {
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
function LoadCategoryRowValues(&$rs) {
	global $categories;
	$categories->id->setDbValue($rs->fields('id'));
	$categories->name->setDbValue($rs->fields('name'));
	$categories->keywords->setDbValue($rs->fields('keywords'));
}
function RenderCategoryRow() {
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
	} elseif ($categories->RowType == EW_ROWTYPE_ADD) { // Add row
	} elseif ($categories->RowType == EW_ROWTYPE_EDIT) { // Edit row
	} elseif ($categories->RowType == EW_ROWTYPE_SEARCH) { // Search row
	}

	// Call Row Rendered event
	$categories->Row_Rendered();
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
		return "<span style=\"color:#ff0000;font-weight:bold;text-decoration:underline\">($count)</span>";
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
