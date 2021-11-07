VERSION 5.00
Begin VB.Form DNSCFG 
   BackColor       =   &H80000014&
   Caption         =   "DNS Config"
   ClientHeight    =   3045
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   5370
   BeginProperty Font 
      Name            =   "Î¢ÈíÑÅºÚ"
      Size            =   12
      Charset         =   134
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "DNSCFG.frx":0000
   LinkTopic       =   "Form3"
   ScaleHeight     =   3045
   ScaleWidth      =   5370
   StartUpPosition =   3  '´°¿ÚÈ±Ê¡
   Begin VB.CommandButton Command2 
      Caption         =   "Command1"
      Height          =   495
      Left            =   2760
      TabIndex        =   4
      Top             =   1800
      Width           =   2415
   End
   Begin VB.CommandButton Command1 
      Caption         =   "Command1"
      Height          =   495
      Left            =   120
      TabIndex        =   3
      Top             =   1800
      Width           =   2415
   End
   Begin VB.TextBox Text1 
      Height          =   435
      Left            =   120
      TabIndex        =   2
      Text            =   "127.0.0.1"
      Top             =   1200
      Width           =   5055
   End
   Begin VB.ComboBox Combo1 
      Height          =   435
      ItemData        =   "DNSCFG.frx":1BEA
      Left            =   120
      List            =   "DNSCFG.frx":1BEC
      TabIndex        =   1
      Top             =   600
      Width           =   5055
   End
   Begin VB.Label Label1 
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      Caption         =   "Label1"
      ForeColor       =   &H80000008&
      Height          =   495
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   5055
   End
End
Attribute VB_Name = "DNSCFG"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Declare Function InitCommonControls Lib "Comctl32.dll" () As Long
Private Sub Combo1_Click()
If Combo1.Text = ConstStr(33) Then
Text1.ForeColor = RGB(0, 0, 0)
Text1.Locked = False
Else
Text1.ForeColor = RGB(150, 150, 150)
Text1.Locked = True
End If
End Sub

Private Sub Command1_Click()
If Combo1.Text = ConstStr(33) Then
LANIP = Text1.Text
Else
LANIP = Combo1.Text
End If
DNSMode = "lan"
WriteCFG
Unload Me
End Sub

Private Sub Command2_Click()
Unload Me
End Sub
Private Sub Form_Initialize()
InitCommonControls
End Sub
Private Sub Form_Load()
Command1.Caption = ConstStr(34)
Command2.Caption = ConstStr(35)

Label1.Caption = ConstStr(32)
Text1.ForeColor = RGB(150, 150, 150)
Text1.Locked = True
strIP = GetIpAddrTable
For i = 0 To (UBound(strIP) - LBound(strIP))
Combo1.AddItem strIP(i)
Next
Combo1.AddItem ConstStr(33)
Combo1.Text = strIP(0)
End Sub
