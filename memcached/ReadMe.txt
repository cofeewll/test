
Memcache for win32
==========================================================
版本：当前版本是win32_1.4.5
安装：
1>注意install.bat文件中binPath需要修改为memcached.exe所在的目录，displayName为服务名，不建议修改，如果修改后
其它的bat文件也需要做相应的修改；
2>注意避免直接双击被杀毒软件给拦截，最好是进入dos命令行执行
    创建服务脚本：install.bat
    启动服务脚本：start.bat
    停止服务脚本：stop.bat
    卸载服务脚本：uninstall.bat

测试：
telnet 127.0.0.1 11211，如果提示telnet命令不存在，需要去控件面板开启windows的tel服务功能，
win7的开启tel功能操作步骤是：【控制面板】->【程序和功能】->【打开或关闭window功能】，然后找到并勾选tel相关即可。