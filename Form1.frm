VERSION 5.00
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Begin VB.Form Form1 
   BackColor       =   &H80000014&
   Caption         =   "Form1"
   ClientHeight    =   2520
   ClientLeft      =   165
   ClientTop       =   810
   ClientWidth     =   5265
   BeginProperty Font 
      Name            =   "微软雅黑"
      Size            =   12
      Charset         =   134
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "Form1.frx":0000
   LinkTopic       =   "Form1"
   ScaleHeight     =   2520
   ScaleWidth      =   5265
   StartUpPosition =   3  '窗口缺省
   Begin MSWinsockLib.Winsock LogSock 
      Left            =   3720
      Top             =   1320
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
   End
   Begin VB.CommandButton ButtonStart 
      Caption         =   "Command1"
      Height          =   615
      Left            =   1680
      TabIndex        =   0
      Top             =   240
      Width           =   1815
   End
   Begin VB.Label LabelLog 
      BackStyle       =   0  'Transparent
      Caption         =   "Listening ..."
      Height          =   975
      Left            =   120
      TabIndex        =   3
      Top             =   1440
      Width           =   4935
   End
   Begin VB.Label LabelStatus 
      BackStyle       =   0  'Transparent
      Caption         =   "LabelStatus"
      Height          =   615
      Left            =   120
      TabIndex        =   2
      Top             =   960
      Width           =   4935
   End
   Begin VB.Label ProgBar 
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      Caption         =   "ProgBar"
      ForeColor       =   &H80000008&
      Height          =   375
      Left            =   120
      TabIndex        =   1
      Top             =   1440
      Width           =   4935
   End
   Begin VB.Menu localemenu 
      Caption         =   "Language/Idioma/界面语言"
      Begin VB.Menu esEs 
         Caption         =   "Espanol"
      End
      Begin VB.Menu enUs 
         Caption         =   "English"
      End
      Begin VB.Menu zhCn 
         Caption         =   "简体中文"
      End
   End
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Sub ButtonStart_Click()
If ServerStarted = False Then
ServerStarted = True
ButtonStart.Caption = ConstStr(2)
PBarLoad 1, Me.hWnd, ProgBar.Left \ Screen.TwipsPerPixelX, ProgBar.Top \ Screen.TwipsPerPixelY, ProgBar.Width \ Screen.TwipsPerPixelX, ProgBar.Height \ Screen.TwipsPerPixelY
PBarSetRange 1, 0, 100
PBarSetPos 1, 0
DoEvents
LabelStatus.Visible = True
LabelStatus.Caption = ConstStr(3)
If CheckFileExists(App.Path & "\httpd\bin\httpd.exe") = False Then
MsgBox ConstStr(4)
Exit Sub
End If
If CheckFileExists(App.Path & "\php\php.exe") = False Then
MsgBox ConstStr(5)
Exit Sub
End If
If CheckFileExists(App.Path & "\dnsagent\DNSAgent.exe") = False Then
MsgBox ConstStr(6)
Exit Sub
End If
PBarSetPos 1, 20
DoEvents
LabelStatus.Caption = ConstStr(7)
'remove httpd conf
Kill App.Path & "\httpd\conf\httpd.conf"
Open App.Path & "\httpd\conf\httpd.conf" For Output As #2
Print #2, "Define SRVROOT " & Chr(34) & App.Path & "\httpd" & Chr(34)
Print #2, "Define APPPATH " & Chr(34) & App.Path & "" & Chr(34)
Print #2, "ServerRoot " & Chr(34) & App.Path & "\httpd" & Chr(34)
Print #2, "Define ENABLE_TLS13 " & Chr(34) & "Yes" & Chr(34)
Print #2, "Listen 80"
Print #2, "LoadModule actions_module modules/mod_actions.so"
Print #2, "LoadModule alias_module modules/mod_alias.so"
Print #2, "LoadModule allowmethods_module modules/mod_allowmethods.so"
Print #2, "LoadModule asis_module modules/mod_asis.so"
Print #2, "LoadModule auth_basic_module modules/mod_auth_basic.so"
Print #2, "LoadModule authn_core_module modules/mod_authn_core.so"
Print #2, "LoadModule authn_file_module modules/mod_authn_file.so"
Print #2, "LoadModule authz_core_module modules/mod_authz_core.so"
Print #2, "LoadModule authz_groupfile_module modules/mod_authz_groupfile.so"
Print #2, "LoadModule authz_host_module modules/mod_authz_host.so"
Print #2, "LoadModule authz_user_module modules/mod_authz_user.so"
Print #2, "LoadModule autoindex_module modules/mod_autoindex.so"
Print #2, "LoadModule cgi_module modules/mod_cgi.so"
Print #2, "LoadModule dir_module modules/mod_dir.so"
Print #2, "LoadModule env_module modules/mod_env.so"
Print #2, "LoadModule http2_module modules/mod_http2.so"
Print #2, "LoadModule include_module modules/mod_include.so"
Print #2, "LoadModule info_module modules/mod_info.so"
Print #2, "LoadModule isapi_module modules/mod_isapi.so"
Print #2, "LoadModule log_config_module modules/mod_log_config.so"
Print #2, "LoadModule mime_module modules/mod_mime.so"
Print #2, "LoadModule negotiation_module modules/mod_negotiation.so"
Print #2, "LoadModule setenvif_module modules/mod_setenvif.so"
Print #2, "LoadModule socache_shmcb_module modules/mod_socache_shmcb.so"
Print #2, "LoadModule ssl_module modules/mod_ssl.so"
Print #2, "LoadModule status_module modules/mod_status.so"
Print #2, "LoadModule php7_module " & Chr(34) & App.Path & "\php\php7apache2_4.dll" & Chr(34)
Print #2, "<IfModule unixd_module>"
Print #2, "User daemon"
Print #2, "Group daemon"
Print #2, "</IfModule>"
Print #2, "ServerAdmin smmwe_cloud@outlook.com"
Print #2, "ServerName smmwe.online"
Print #2, "SetEnv LEANCLOUD_API_SERVER https://nbpj0bub.api.lncldglobal.com"
Print #2, "<Directory />"
    Print #2, "AllowOverride all"
    Print #2, "Require all granted"
Print #2, "</Directory>"
Print #2, "DocumentRoot " & Chr(34) & App.Path & "\htdocs" & Chr(34)
Print #2, "<Directory " & Chr(34) & App.Path & "\htdocs" & Chr(34) & ">"
    Print #2, "Options Indexes FollowSymLinks MultiViews"
    Print #2, "AllowOverride all"
    Print #2, "Require all granted"
Print #2, "</Directory>"
    Print #2, "<IfModule dir_module>"
    Print #2, "DirectoryIndex index.html index.php index.htm"
    Print #2, "</IfModule>"
Print #2, "ErrorLog " & Chr(34) & App.Path & "\logs\httpd.log" & Chr(34)
Print #2, "LogLevel warn"
Print #2, "<IfModule log_config_module>"
    Print #2, "LogFormat " & Chr(34) & "%h %l %u %t \" & Chr(34) & "%r\" & Chr(34) & " %>s %b \" & Chr(34) & "%{Referer}i\" & Chr(34) & " \" & Chr(34) & "%{User-Agent}i\" & Chr(34) & "" & Chr(34) & " combined"
    Print #2, "LogFormat " & Chr(34) & "%h %l %u %t \" & Chr(34) & "%r\" & Chr(34) & " %>s %b" & Chr(34) & " common"
    Print #2, "<IfModule logio_module>"
      Print #2, "LogFormat " & Chr(34) & "%h %l %u %t \" & Chr(34) & "%r\" & Chr(34) & " %>s %b \" & Chr(34) & "%{Referer}i\" & Chr(34) & " \" & Chr(34) & "%{User-Agent}i\" & Chr(34) & " %I %O" & Chr(34) & " combinedio"
    Print #2, "</IfModule>"
Print #2, "</IfModule>"
    Print #2, "<IfModule alias_module>"
    Print #2, "ScriptAlias /cgi-bin/ " & Chr(34) & App.Path & "\httpd\cgi-bin\" & Chr(34)
    Print #2, "</IfModule>"
    Print #2, "<IfModule cgid_module>"
    Print #2, "</IfModule>"
    
Print #2, "<Directory " & Chr(34) & App.Path & "\httpd\cgi-bin" & Chr(34) & ">"
    Print #2, "AllowOverride all"
    Print #2, "Options None"
    Print #2, "Require all granted"
Print #2, "</Directory>"

Print #2, "<IfModule mime_module>"
    Print #2, "TypesConfig conf/mime.types"
    Print #2, "AddType application/x-compress .Z"
    Print #2, "AddType application/x-gzip .gz .tgz"
    Print #2, "AddType application/x-httpd-php .php"
Print #2, "</IfModule>"
Print #2, "Include conf/extra/httpd-autoindex.conf"
Print #2, "Include conf/extra/httpd-info.conf"

Print #2, "<IfModule proxy_html_module>"
Print #2, "Include conf/extra/httpd-proxy-html.conf"
Print #2, "</IfModule>"

Print #2, "<IfModule ssl_module>"
Print #2, "Include conf/extra/httpd-ssl.conf"
Print #2, "#Include conf/extra/httpd-ahssl.conf"
Print #2, "SSLRandomSeed startup builtin"
Print #2, "SSLRandomSeed connect builtin"
Print #2, "</IfModule>"
Print #2, "<IfModule http2_module>"
    Print #2, "ProtocolsHonorOrder On"
    Print #2, "Protocols h2 h2c http/1.1"
Print #2, "</IfModule>"
Print #2, "<IfModule lua_module>"
  Print #2, "AddHandler lua-script .lua"
Print #2, "</IfModule>"
Print #2, "PHPIniDir " & Chr(34) & App.Path & "\php" & Chr(34)
Close #2
If CheckFileExists(App.Path & "\php\php.ini") = True Then Kill App.Path & "\php\php.ini"
FileCopy App.Path & "\php\phpdefault.ini", App.Path & "\php\php.ini"
Sleep 20
DoEvents
Open App.Path & "\php\php.ini" For Append As #2
Print #2, vbCrLf
Print #2, "extension_dir = " & Chr(34) & App.Path & "\php\ext" & Chr(34)
Close #2
Sleep 30
PBarSetPos 1, 40
DoEvents
LabelStatus.Caption = ConstStr(8)
If CheckExeIsRun("DNSAgent.exe") = False Then Shell App.Path & "\dnsagent\DNSAgent.exe", vbMinimizedNoFocus
'WinSock
LogSock.LocalPort = 6002
LogSock.Listen
LabelLog.Visible = True
LabelLog.Caption = ConstStr(13)
PBarSetPos 1, 60
DoEvents
LabelStatus.Caption = ConstStr(9)
Shell "cmd /c " & Chr(34) & App.Path & "\cert\mkcert.exe" & Chr(34) & " -install", vbMinimizedNoFocus
If CheckFileExists(App.Path & "\cert\smmwe.online.pem") Then Kill App.Path & "\cert\smmwe.online.pem"
If CheckFileExists(App.Path & "\cert\smmwe.online-key.pem") Then Kill App.Path & "\cert\smmwe.online-key.pem"
Sleep 10
DoEvents
Shell "cmd /c cd " & Chr(34) & App.Path & "\cert" & Chr(34) & " && " & Chr(34) & "mkcert.exe" & Chr(34) & " smmwe.online", vbMinimizedNoFocus
Sleep 1500
DoEvents
If CheckFileExists(App.Path & "\httpd\conf\ssl\server.crt") = True Then Kill App.Path & "\httpd\conf\ssl\server.crt"
If CheckFileExists(App.Path & "\httpd\conf\ssl\server.key") = True Then Kill App.Path & "\httpd\conf\ssl\server.key"
FileCopy App.Path & "\cert\smmwe.online.pem", App.Path & "\httpd\conf\ssl\server.crt"
FileCopy App.Path & "\cert\smmwe.online-key.pem", App.Path & "\httpd\conf\ssl\server.key"
PBarSetPos 1, 80
LabelStatus.Caption = ConstStr(10)
Sleep 200
If CheckFileExists(App.Path & "\logs\httpd.log") Then Kill App.Path & "\logs\httpd.log"
If CheckFileExists(App.Path & "\logs\access.log") Then Kill App.Path & "\logs\access.log"
If CheckFileExists(App.Path & "\logs\error.log") Then Kill App.Path & "\logs\error.log"
If CheckFileExists(App.Path & "\httpd\logs\ssl_request.log") Then Kill App.Path & "\httpd\logs\ssl_request.log"
Shell "cmd /c " & App.Path & "\httpd\bin\httpd.exe", vbHide
If GetDataSWE("https://smmwe.online/PrivateServer/test.html") <> "SMMWE Cloud Private Server is started!" Then
Form2.Show
Shell App.Path & "\cfg\kill-dnsagent.bat", vbMinimizedNoFocus
Shell "taskkill /f /im httpd.exe"
LogSock.Close
PBarUnload 1
LabelStatus.Visible = False
LabelLog.Visible = False
ServerStarted = False
ButtonStart.Caption = ConstStr(1)
Exit Sub
End If
PBarUnload 1
LabelStatus.Caption = ConstStr(11)
Else
Shell App.Path & "\cfg\kill-dnsagent.bat", vbMinimizedNoFocus
Shell "taskkill /f /im httpd.exe"
LogSock.Close
LabelStatus.Visible = False
LabelLog.Visible = False
ServerStarted = False
ButtonStart.Caption = ConstStr(1)
End If
End Sub

Private Sub enUs_Click()
Locale = "en-us"
If CheckFileExists(App.Path & "\cfg\locale.txt") Then Kill App.Path & "\cfg\locale.txt"
Open App.Path & "\cfg\locale.txt" For Output As #3
Print #3, "en-us"
Close #3
End Sub

Private Sub esEs_Click()
Locale = "es-es"
If CheckFileExists(App.Path & "\cfg\locale.txt") Then Kill App.Path & "\cfg\locale.txt"
Open App.Path & "\cfg\locale.txt" For Output As #3
Print #3, "es-es"
Close #3
End Sub


Private Sub Form_Load()
Open App.Path & "\cfg\locale.txt" For Input As #4
Line Input #4, Locale
Close #4
Version = "b1.7"
Open App.Path & "\cfg\lang-" & Locale & ".txt" For Input As #1
    LocaleTmp = ""
    LocaleTmp2 = ""
    Do While Not EOF(1)
    Line Input #1, LocaleTmp2
    LocaleTmp = LocaleTmp & LocaleTmp2 & vbCrLf
    Loop
    ConstStr = Split(LocaleTmp, vbCrLf)
    ReDim Preserve ConstStr(UBound(ConstStr) + 1)
Close #1
Form1.Caption = ConstStr(0) & " - " & Version
ButtonStart.Caption = ConstStr(1)
ProgBar.Caption = ""
ServerStarted = False
LabelStatus.Visible = False
LabelLog.Visible = False
End Sub

Private Sub Form_Unload(Cancel As Integer)
Shell "taskkill /f /im httpd.exe"
End Sub

Private Sub LogSock_DataArrival(ByVal bytesTotal As Long)
    Dim strData As String
    LogSock.GetData strData
    If Locale <> "en-us" Then
    strData = Replace(strData, "Parsing metadata of level", ConstStr(14))
    strData = Replace(strData, "Posting metadata", ConstStr(15))
    strData = Replace(strData, "to LeanCloud database", ConstStr(16))
    strData = Replace(strData, "Parsing metadata of level", ConstStr(17))
    strData = Replace(strData, "Downloading level", ConstStr(18))
    strData = Replace(strData, "Listing levels", ConstStr(19))
    strData = Replace(strData, "Trying to get level metadata for level", ConstStr(20))
    strData = Replace(strData, "Local metadata found", ConstStr(21))
    strData = Replace(strData, "Local metadata not found", ConstStr(22))
    strData = Replace(strData, "Synchronizing", ConstStr(23))
    strData = Replace(strData, "was logged into private server", ConstStr(24))
    strData = Replace(strData, "Loading course world", ConstStr(25))
    End If
     LogSock.Close
     LogSock.LocalPort = 6002
     LogSock.Listen
    LabelLog.Caption = strData
End Sub
Private Sub LogSock_ConnectionRequest(ByVal RequestlD As Long)
    If LogSock.State <> sckClosed Then
            LogSock.Close
            LogSock.Accept RequestlD
    End If
End Sub
Private Sub zhCn_Click()
Locale = "zh-cn"
If CheckFileExists(App.Path & "\cfg\locale.txt") Then Kill App.Path & "\cfg\locale.txt"
Open App.Path & "\cfg\locale.txt" For Output As #3
Print #3, "zh-cn"
Close #3
End Sub
