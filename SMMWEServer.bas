Attribute VB_Name = "SMMWEServer"
Public Locale As String
Public ConstStr() As String
Public Version As String
Public ServerStarted As Boolean
Public Declare Sub Sleep Lib "kernel32" (ByVal dwMilliseconds As Long)
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
  XMLHTTP.open "GET", Url, True
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
