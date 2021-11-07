<?php
header("Content-Type: text/html; charset=utf-8");
$lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,4);
if (preg_match("/zh-c/i",$lang)) {
echo "<p>SMMWE Cloud 私服正在工作！</p>";
echo "<p>如果你想要访问SMMWE的官方网站（smmwe.online），那就请前往<a href=\"https://online.smmwe.ml\">online.smmwe.ml</a></p>";
} elseif (preg_match("/zh/i",$lang)) {
    echo "<p>SMMWE Cloud 私服正在工作！</p>";
    echo "<p>如果你想要访问SMMWE的官方网站（smmwe.online），那就请前往<a href=\"https://online.smmwe.ml\">online.smmwe.ml</a>。</p>";
} elseif (preg_match("/en/i",$lang)) {
    echo "<p>SMMWE Cloud Private Server is working!</p>";
    echo "<p>If you want to visit SMMWE's official website (smmwe.online), then go to <a href=\"https://online.smmwe.ml\">online.smmwe.ml</a> .</p>";
} elseif (preg_match("/es/i",$lang)) {
    echo "<p>¡SMMWE Cloud Private Server está funcionando!</p>";
    echo "<p>Si desea visitar el sitio web oficial de SMMWE (smmwe.online), vaya a <a href=\"https://online.smmwe.ml\">online.smmwe.ml</a> .</p>";
}
?>
