VERSION 5.00
Object = "{3B7C8863-D78F-101B-B9B5-04021C009402}#1.2#0"; "RICHTX32.OCX"
Begin VB.Form Form3 
   BackColor       =   &H80000014&
   Caption         =   "Form3"
   ClientHeight    =   5835
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   8400
   Icon            =   "Form3.frx":0000
   LinkTopic       =   "Form3"
   ScaleHeight     =   5835
   ScaleWidth      =   8400
   StartUpPosition =   3  '´°¿ÚÈ±Ê¡
   Begin RichTextLib.RichTextBox RichTextBox1 
      Height          =   5535
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   8175
      _ExtentX        =   14420
      _ExtentY        =   9763
      _Version        =   393217
      Enabled         =   -1  'True
      TextRTF         =   $"Form3.frx":25CA
   End
End
Attribute VB_Name = "Form3"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Declare Function InitCommonControls Lib "Comctl32.dll" () As Long
Private Sub Form_Load()
Form3.Caption = ConstStr(36)
RichTextBox1.FileName = App.Path & "\cfg\lanmode-" & Locale & ".rtf"
End Sub
Private Sub Form_Initialize()
InitCommonControls
End Sub
