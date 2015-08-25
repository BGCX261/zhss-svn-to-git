<!--STATUS OK-->
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  <base href="http://finance.sina.com.cn/realstock/company/sz000628/nc.shtml">
  <style>
  body{margin:4px 0}
  #bd_sn_h{text-align:left;background-color:#ffffff;color:#000000}
  #bd_sn_h #p1{clear:both;font:14px Arial;margin:0 0 0 2px;padding:4px 0 0 0}
  #bd_sn_h a{color:#0000ff;text-decoration:underline}
  #bd_sn_h #p1 a{font-weight:bold}
  #baidu div{position:static}
  </style>

<table id="baidu" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>网站名称:</td>
<td>采集时间</td>
<td>关键词</td>
</tr>
</table>
  <div style="position:relative">
  <?php
  $file_handle = fopen("snapshot.php", "r");
  while (!feof($file_handle)) {$line .= fgets($file_handle);}
  echo $line;
  fclose($file_handle);
  ?>