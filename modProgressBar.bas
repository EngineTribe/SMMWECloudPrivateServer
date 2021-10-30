Attribute VB_Name = "modProgressBar"
' Progress Bar without using ActiveX controls

Const ICC_PROGRESS_CLASS = &H20

Private Const WM_USER = &H400
Private Const CCM_FIRST = &H2000
Private Const CCM_SETBKCOLOR = (CCM_FIRST + 1)
Private Const PBM_SETPOS = (WM_USER + 2)
Private Const PBM_SETRANGE = (WM_USER + 1)
Private Const PBM_SETRANGE32 = (WM_USER + 6)
Private Const PBM_SETSTEP = (WM_USER + 4)
Private Const PBM_SETBARCOLOR = (WM_USER + 9)
Private Const PBM_SETBKCOLOR = CCM_SETBKCOLOR
Private Const PBM_GETRANGE = (WM_USER + 7)
Private Const PBM_GETPOS = (WM_USER + 8)
Private Const PBS_VERTICAL = &H4
Private Const WS_VISIBLE = &H10000000
Private Const WS_CHILD = &H40000000

Private Type InitCommonControlsExType
    dwSize As Long
    dwICC As Long
End Type

Private Type tipPBarData
    Handle As Long
    RangeLow As Long
    RangeHigh As Long
    Position As Long
    WinHandle As Long
    Left As Long
    Top As Long
    Width As Long
    Height As Long
End Type

Private Declare Sub InitCommonControls Lib "comctl32" ()
Private Declare Function InitCommonControlsEx Lib "comctl32" (init As InitCommonControlsExType) As Boolean
Private Declare Function CreateWindowEx Lib "User32" Alias "CreateWindowExA" (ByVal dwExStyle As Long, ByVal lpClassName As String, ByVal lpWindowName As String, ByVal dwStyle As Long, ByVal X As Long, ByVal Y As Long, ByVal nWidth As Long, ByVal nHeight As Long, ByVal hWndParent As Long, ByVal hMenu As Long, ByVal hInstance As Long, lpParam As Any) As Long
Private Declare Function DestroyWindow Lib "User32" (ByVal hWnd As Long) As Long
Private Declare Function PostMessage Lib "User32" Alias "PostMessageA" (ByVal hWnd As Long, ByVal wMsg As Long, ByVal wParam As Long, ByVal lParam As Long) As Long

'Supports up to 255 progress bars :)
Dim PBarData(1 To 255) As tipPBarData

Public Sub PBarSetPos(Number As Long, ByVal Value As Long)
    If PBarData(Number).Position = Value Then
        Exit Sub
    End If

    PostMessage PBarData(Number).Handle, PBM_SETPOS, Value, 0
    PBarData(Number).Position = Value
End Sub

Public Sub PBarSetRange(Number As Long, Low As Long, High As Long)
    PostMessage PBarData(Number).Handle, PBM_SETRANGE32, Low, High
    PBarData(Number).RangeLow = Low
    PBarData(Number).RangeHigh = High
End Sub

Public Sub PBarLoad(Number As Long, hWnd As Long, Left As Long, Top As Long, Width As Long, Height As Long)
    Const IE3_INSTALLED = True

    If IE3_INSTALLED = True Then
        Dim initcc As InitCommonControlsExType
        
        initcc.dwSize = Len(initcc)
        initcc.dwICC = ICC_PROGRESS_CLASS
        InitCommonControlsEx initcc
    Else
        InitCommonControls
    End If

    PBarData(Number).Handle = CreateWindowEx(0, "msctls_progress32", "Progress Bar", WS_VISIBLE Or WS_CHILD, Left, Top, Width, Height, hWnd, ByVal 0&, ByVal 0&, ByVal 0&)
    PBarData(Number).WinHandle = hWnd
    PostMessage PBarData(Number).Handle, PBM_SETRANGE32, 0, 100
    PostMessage PBarData(Number).Handle, PBM_SETPOS, 0, 0
    
    With PBarData(Number)
        .Position = 0
        .RangeLow = 0
        .RangeHigh = 100
        .Left = Left
        .Top = Top
        .Width = Width
        .Height = Height
    End With
End Sub

Public Sub PBarUnload(Number As Long)
    DestroyWindow PBarData(Number).Handle
    PBarData(Number).Handle = 0
End Sub

Public Sub PBarMove(Number As Long, Left As Long, Top As Long, Width As Long, Height As Long)
    Const IE3_INSTALLED = True
    
    With PBarData(Number)
        If (.Left = Left) And (.Top = Top) And (.Width = Width) And (Height = .Height) Then
            Exit Sub
        End If
    End With

    If IE3_INSTALLED = True Then
        Dim initcc As InitCommonControlsExType
        
        initcc.dwSize = Len(initcc)
        initcc.dwICC = ICC_PROGRESS_CLASS
        InitCommonControlsEx initcc
    Else
        InitCommonControls
    End If
    
    PBarUnload Number
    PBarData(Number).Handle = CreateWindowEx(0, "msctls_progress32", "Progress Bar", WS_VISIBLE Or WS_CHILD, Left, Top, Width, Height, PBarData(Number).WinHandle, ByVal 0&, ByVal 0&, ByVal 0&)
    PostMessage PBarData(Number).Handle, PBM_SETRANGE32, PBarData(Number).RangeLow, PBarData(Number).RangeHigh
    PBarSetPos Number, PBarData(Number).Position
    
    With PBarData(Number)
        .Left = Left
        .Top = Top
        .Width = Width
        .Height = Height
    End With
End Sub



