Apache虚拟目录配置

Alias /liubiancn "D:/webroot/liubiancn.com/"
<Directory "D:/webroot/liubiancn.com">
    Options None
    AllowOverride None
    Order allow,deny
    Allow from all
</Directory>

修改程序配置文件ewcfg50.php最后一行，快照目录的路径
如
$viewpath = '/liubiancn';
最后不需要加“/”