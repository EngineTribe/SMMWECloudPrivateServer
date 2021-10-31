VERSION 5.00
Object = "{3B7C8863-D78F-101B-B9B5-04021C009402}#1.2#0"; "RICHTX32.OCX"
Begin VB.Form Form2 
   BackColor       =   &H80000014&
   Caption         =   "Error"
   ClientHeight    =   5655
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   9015
   BeginProperty Font 
      Name            =   "Î¢ÈíÑÅºÚ"
      Size            =   12
      Charset         =   134
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "Form2.frx":0000
   LinkTopic       =   "Form2"
   ScaleHeight     =   5655
   ScaleWidth      =   9015
   StartUpPosition =   3  '´°¿ÚÈ±Ê¡
   Begin VB.CommandButton Command1 
      Caption         =   "Command1"
      Height          =   495
      Left            =   2400
      TabIndex        =   1
      Top             =   5040
      Width           =   4095
   End
   Begin RichTextLib.RichTextBox RichTextBox1 
      Height          =   4815
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   8775
      _ExtentX        =   15478
      _ExtentY        =   8493
      _Version        =   393217
      TextRTF         =   $"Form2.frx":25CA
   End
End
Attribute VB_Name = "Form2"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Declare Function InitCommonControls Lib "Comctl32.dll" () As Long
Private Sub Command1_Click()
Shell "control ncpa.cpl", vbNormalFocus
End Sub

Private Sub Form_Load()
Form2.Caption = ConstStr(36)
Command1.Caption = ConstStr(12)
RichTextBox1.FileName = App.Path & "\cfg\setdns-" & Locale & ".rtf"
End Sub

Private Sub Form_Initialize()
InitCommonControls
End Sub
