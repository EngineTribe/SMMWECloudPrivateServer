Attribute VB_Name = "SMMWEServer"
Public Locale As String
Public DNSMode As String
Public LANIP As String
Public Mirror As String
Public ConstStr() As String
Public Version As String
Public ServerStarted As Boolean
Public Declare Sub Sleep Lib "kernel32" (ByVal dwMilliseconds As Long)
Private Declare Function GetIpAddrTable_API Lib "IpHlpApi" Alias "GetIpAddrTable" (pIPAddrTable As Any, pdwSize As Long, ByVal bOrder As Long) As Long
Public Declare Function ShellExecute Lib "shell32.dll" Alias "ShellExecuteA" (ByVal hWnd As Long, ByVal lpOperation As String, ByVal lpFile As String, ByVal lpParameters As String, ByVal lpDirectory As String, ByVal nShowCmd As Long) As Long

Public Function CheckFileExists(FilePath As String) As Boolean
    On Error GoTo ERR
    If Len(FilePath) < 2 Then CheckFileExists = False: Exit Function
            If Dir$(FilePath, vbAllFileAttrib) <> vbNullString Then CheckFileExists = True
    Exit Function
ERR:
    CheckFileExists = False
End Function

Public Function CheckExeIsRun(exeName As String) As Boolean
On Error GoTo ERR
Dim WMI
Dim Obj
Dim Objs
CheckExeIsRun = False
Set WMI = GetObject("WinMgmts:")
Set Objs = WMI.InstancesOf("Win32_Process")
For Each Obj In Objs
If (InStr(UCase(exeName), UCase(Obj.Description)) <> 0) Then
CheckExeIsRun = True
If Not Objs Is Nothing Then Set Objs = Nothing
If Not WMI Is Nothing Then Set WMI = Nothing
Exit Function
End If
Next
If Not Objs Is Nothing Then Set Objs = Nothing
If Not WMI Is Nothing Then Set WMI = Nothing
Exit Function
ERR:
If Not Objs Is Nothing Then Set Objs = Nothing
If Not WMI Is Nothing Then Set WMI = Nothing
End Function
Public Function GetDataSWE(ByVal Url As String) As String
  On Error GoTo ERR:
  Dim XMLHTTP As Object
  Set XMLHTTP = CreateObject("Microsoft.XMLHTTP")
  XMLHTTP.Open "GET", Url, True
  XMLHTTP.send
  While XMLHTTP.ReadyState <> 4
  Sleep 10
    DoEvents
  Wend
    GetDataSWE = XMLHTTP.ResponseText
  Set XMLHTTP = Nothing
  Exit Function
ERR:
  GetDataSWE = ""
End Function

Public Sub WriteCFG()
If CheckFileExists(App.Path & "\cfg\cfg.txt") Then Kill App.Path & "\cfg\cfg.txt"
Open App.Path & "\cfg\cfg.txt" For Output As #3
Print #3, Locale
Print #3, DNSMode
Print #3, LANIP
Print #3, Mirror
Close #3
End Sub
' Returns an array with the local IP addresses (as strings).
'Modified by YidaozhanYa: delete 127.0.0.1
' Author: Christian d'Heureuse, www.source-code.biz
Public Function GetIpAddrTable()
   Dim Buf(0 To 511) As Byte
   Dim BufSize As Long: BufSize = UBound(Buf) + 1
   Dim rc As Long
   rc = GetIpAddrTable_API(Buf(0), BufSize, 1)
   If rc <> 0 Then ERR.Raise vbObjectError, , "GetIpAddrTable failed with return value " & rc
   Dim NrOfEntries As Integer: NrOfEntries = Buf(1) * 256 + Buf(0)
   If NrOfEntries = 0 Then GetIpAddrTable = Array(): Exit Function
   ReDim IpAddrs(0 To NrOfEntries - 1) As String
   Dim i As Integer
   For i = 0 To NrOfEntries - 1
      Dim j As Integer, s As String: s = ""
      For j = 0 To 3: s = s & IIf(j > 0, ".", "") & Buf(4 + i * 24 + j): Next
      IpAddrs(i) = s
      Next
   GetIpAddrTable = Split(Replace(Join(IpAddrs, vbCrLf), "127.0.0.1" & vbCrLf, ""), vbCrLf)
   End Function
