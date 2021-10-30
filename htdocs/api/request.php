<?php
set_time_limit(0);
require_once("autoload.php");
use \LeanCloud\Client;
use \LeanCloud\LeanObject;
use \LeanCloud\Query;
const smmwe_cloud_apiurl="https://smmwe-cloud.vercel.app/main/";

define('etiquetas',[
    'Tradicional',
    'Puzles',
    'Contrarreloj',
    'Autoavance',
    'Automatismos',
    'Corto pero intenso',
    'Competitivo',
    'Tematico',
    'Música',
    'Artístico',
    'Habilidad',
    'Disparos',
    'Contra jefes',
    'En solitario',
    'Link'
]);

function get_level($level_name){
    logtovb("Downloading level ".$level_name." ...");
    $curl=curl_init();
    curl_setopt($curl, CURLOPT_URL, smmwe_cloud_apiurl . $level_name . ".swe");
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $return_data=curl_exec($curl);
    curl_close($curl);
    foreach (explode(PHP_EOL,$return_data) as $v) {
        if (substr($v, 0, 8) == "location") {
            $level_url=str_replace("location: ","",$v);
            //$level_url=substr($level_url,0,strlen($level_url)-1);
            $curl=curl_init();
            curl_setopt($curl, CURLOPT_URL, $level_url);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $return_data=curl_exec($curl);
            //echo $return_data;
            curl_close($curl);
            return explode("\0", explode(PHP_EOL,$return_data)[count(explode(PHP_EOL,$return_data))-1])[0];
        };
    };
};

function save_level_metadata($level_name){
    logtovb("Parsing metadata of level ".$level_name." ...");
Client::initialize("NBPj0BUbArYBILwBIrrlCESJ-MdYXbMMI", "Hw2jUi14SmR0oXjdwB8x2RNe", "YJSkfTWVnfr5N4jD7vEJiEzE");
$level_data="";
$level_data=get_level($level_name);
//calculate level id
$level_id=strtoupper(strval(substr(md5($level_data),8,16)));
$level_id=strval(substr($level_id,0,4)."-".substr($level_id,4,4)."-".substr($level_id,8,4)."-".substr($level_id,12,4));
//check duplicate
if (is_null(get_metadata_by_id($level_id,"level_name"))==false) {
$level_id=strtoupper(strval(substr($level_id,0,17).strval(dechex(rand(0,15))).strval(dechex(rand(0,15)))));
};
$level_data=json_decode(base64_decode(substr($level_data,0,strlen($level_data)-40)),true);
$level_author=$level_data["MAIN"]["AJUSTES"][0]['user'];
$level_apariencia=strval($level_data["MAIN"]["AJUSTES"][0]['apariencia']);
$level_date=stripslashes(strval($level_data["MAIN"]["AJUSTES"][0]['date']));
$level_label1=intval($level_data["MAIN"]["AJUSTES"][0]['etiqueta1']);
if ($level_label1===-1) {
    $level_label1="---";
} else {
    $level_label1=etiquetas[$level_label1];
};
$level_label2=intval($level_data["MAIN"]["AJUSTES"][0]['etiqueta2']);
if ($level_label2===-1) {
    $level_label2="---";
} else {
    $level_label2=etiquetas[$level_label2];
};
if (strlen($level_author)===0) {
    $level_author="SMMWE Cloud";
};
logtovb("Posting metadata ".$level_id." to LeanCloud database ...");
$metadataObject = new LeanObject("Metadata");
$metadataObject->set("level_name", strval($level_name));
$metadataObject->set("level_id", $level_id);
$metadataObject->set("level_author", strval($level_author));
$metadataObject->set("level_apariencia", strval($level_apariencia));
$metadataObject->set("level_date", $level_date);
$metadataObject->set("level_label1", $level_label1);
$metadataObject->set("level_label2", $level_label2);
$metadataObject->save();
};

function object_array($array) {  
    if(is_object($array)) {  
        $array = (array)$array;  
    } 
    if(is_array($array)) {
        foreach($array as $key=>$value) {  
            $array[$key] = object_array($value);  
        }  
    }  
    return $array;  
};

function get_metadata_by_id($level_id,$metadata) {
    Client::initialize("NBPj0BUbArYBILwBIrrlCESJ-MdYXbMMI", "Hw2jUi14SmR0oXjdwB8x2RNe", "YJSkfTWVnfr5N4jD7vEJiEzE");
    $query = new Query("Metadata");
    $query->equalTo("level_id", $level_id);
    $return_data = $query->first();
    $return_data = $query->find();
    return json_decode(str_replace("\\u0000","",json_encode(object_array($return_data[0]))),true)["LeanCloud\\LeanObject_data"][$metadata];
};

function get_metadata_by_name($level_name,$metadata) {
    Client::initialize("NBPj0BUbArYBILwBIrrlCESJ-MdYXbMMI", "Hw2jUi14SmR0oXjdwB8x2RNe", "YJSkfTWVnfr5N4jD7vEJiEzE");
    $query = new Query("Metadata");
    $query->equalTo("level_name", $level_name);
    $return_data = $query->first();
    $return_data = $query->find();
    return json_decode(str_replace("\\u0000","",json_encode(object_array($return_data[0]))),true)["LeanCloud\\LeanObject_data"][$metadata];
};

function gen_temp_metadata_by_name($level_name) {
    Client::initialize("NBPj0BUbArYBILwBIrrlCESJ-MdYXbMMI", "Hw2jUi14SmR0oXjdwB8x2RNe", "YJSkfTWVnfr5N4jD7vEJiEzE");
    $query = new Query("Metadata");
    $query->equalTo("level_name", $level_name);
    $return_data = $query->first();
    $return_data = $query->find();
    return json_decode(str_replace("\\u0000","",json_encode(object_array($return_data[0]))),true)["LeanCloud\\LeanObject_data"];
};

function gen_temp_metadata_by_id($level_id) {
    Client::initialize("NBPj0BUbArYBILwBIrrlCESJ-MdYXbMMI", "Hw2jUi14SmR0oXjdwB8x2RNe", "YJSkfTWVnfr5N4jD7vEJiEzE");
    $query = new Query("Metadata");
    $query->equalTo("level_id", $level_id);
    $return_data = $query->first();
    $return_data = $query->find();
    return json_decode(str_replace("\\u0000","",json_encode(object_array($return_data[0]))),true)["LeanCloud\\LeanObject_data"];
};

function list_levels($page) {
    logtovb("Listing levels ...");
    $page_om=ceil($page/20);
    $curl=curl_init();
    curl_setopt($curl, CURLOPT_URL, smmwe_cloud_apiurl."?filename");
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "pagenum=".$page_om);
    $return_data=curl_exec($curl);
    $return_data=array_slice(explode("\n",$return_data),(($page-(($page_om-1)*20))-1)*10,10);
    curl_close($curl);
    return $return_data;
};

function get_result($level_name) {
    logtovb("Trying to get level metadata for level: ".$level_name ." ...");
    //check if cloud metadata is created
    if (file_exists($_SERVER['DOCUMENT_ROOT']."\/cache\/".$level_name.".php")) {
        logtovb("get_result: Local metadata found: ".$level_name .".php");
    $result = include($_SERVER['DOCUMENT_ROOT']."\/cache\/".$level_name.".php");
    } else {
        logtovb("get_result: Local metadata not found: ".$level_name .".php, Synchronizing ...");
    if (is_null(get_metadata_by_name($level_name,"level_id"))==true) {
        //create cloud metadata
        save_level_metadata($level_name);
    };
    $metadata=gen_temp_metadata_by_name($level_name);
    $level_author=$metadata["level_author"];
    $level_id=$metadata["level_id"];
    $level_apariencia=$metadata["level_apariencia"];
    $level_entorno="0";
    $level_label1=$metadata["level_label1"];
    $level_label2=$metadata["level_label2"];
    if (($level_label1==="Tradicional") && ($level_label2==="Tradicional")) {
        $level_label2="---";
    };
    $level_etiquetas=$level_label1 . "," . $level_label2;
    $uploaded_date="01/01/2021";
    $uploaded_date=$metadata["level_date"];
    $result=array("name"=>$level_name,"img"=>"https://smmwe.online/new_level.png","likes"=>"0","downloads"=>"1","comments"=>"0","dislikes"=>"0","intentos"=>"0","muertes"=>"0","victorias"=>"0","apariencia"=>$level_apariencia,"entorno"=>$level_entorno,"etiquetas"=>$level_etiquetas,"featured"=>"0","user_data"=>array("data"=>"no","completed"=>"no","liked"=>"0"),"record"=>array("record"=>"no"),"date"=>$uploaded_date,"author"=>$level_author,"authorimg"=>"https://smmwe.online/favicon.png","description"=>urlencode("Sin Descripción"),"id"=>$level_id);
    //write local cache
    file_put_contents($_SERVER['DOCUMENT_ROOT']."\/cache\/".$level_name.".php", "<?php\nreturn " . var_export($result, true) . ";\n");
    };
    return $result;
};

function get_result_by_id($level_id) {
    logtovb("Trying to get level metadata for level: ".$level_id ." ...");
    $level_name=get_metadata_by_id($level_id,"level_name");
    if (is_null($level_name)==true) {
        $result=array("name"=>"Error al nivel de busqueda","img"=>"https://smmwe.online/new_level.png","likes"=>"0","downloads"=>"1","comments"=>"0","dislikes"=>"0","intentos"=>"0","muertes"=>"0","victorias"=>"0","apariencia"=>"0","entorno"=>"0","etiquetas"=>"0","featured"=>"0","user_data"=>array("data"=>"no","completed"=>"no","liked"=>"0"),"record"=>array("record"=>"no"),"date"=>"01/01/2021","author"=>"Server Error","authorimg"=>"https://smmwe.online/favicon.png","description"=>"Puede ser que el nivel no se haya analizado en la base de datos.","id"=>$level_id);
        //exit
        return $result;
    };
    //check if cloud metadata is created
    if (file_exists($_SERVER['DOCUMENT_ROOT']."\/cache\/".$level_name.".php")) {
        logtovb("get_result: Local metadata found: ".$level_name .".php");
    $result = include($_SERVER['DOCUMENT_ROOT']."\/cache\/".$level_name.".php");
    } else {
        logtovb("get_result: Local metadata not found: ".$level_name .".php, Synchronizing ...");
    $metadata=gen_temp_metadata_by_id($level_id);
    $level_author=$metadata["level_author"];
    $level_id=$metadata["level_id"];
    $level_apariencia=$metadata["level_apariencia"];
    $level_entorno="0";
    $level_label1=$metadata["level_label1"];
    $level_label2=$metadata["level_label2"];
    if (($level_label1==="Tradicional") && ($level_label2==="Tradicional")) {
        $level_label2="---";
    };
    $level_etiquetas=$level_label1 . "," . $level_label2;
    $uploaded_date=$metadata["level_date"];
    $result=array("name"=>$level_name,"img"=>"https://smmwe.online/new_level.png","likes"=>"0","downloads"=>"1","comments"=>"0","dislikes"=>"0","intentos"=>"0","muertes"=>"0","victorias"=>"0","apariencia"=>$level_apariencia,"entorno"=>$level_entorno,"etiquetas"=>$level_etiquetas,"featured"=>"0","user_data"=>array("data"=>"no","completed"=>"no","liked"=>"0"),"record"=>array("record"=>"no"),"date"=>$uploaded_date,"author"=>$level_author,"authorimg"=>"https://smmwe.online/favicon.png","description"=>urlencode("Sin Descripción"),"id"=>$level_id);
    //write local cache
    file_put_contents($_SERVER['DOCUMENT_ROOT']."\/cache\/".$level_name.".php", "<?php\nreturn " . var_export($result, true) . ";\n");
    };
    return $result;
};
function logtovb ($log){
    //post log to SMMWEServerGUI
$socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
socket_set_block($socket);
socket_connect($socket,'127.0.0.1',6002);
$return_data=socket_write($socket,$log,strlen($log));
socket_close($socket);
error_log($log);
return $return_data;
};

function get_max_files() {
    $curl=curl_init();
    curl_setopt($curl, CURLOPT_URL, smmwe_cloud_apiurl."?maxfiles");
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $return_data=curl_exec($curl);
    curl_close($curl);
    logtovb("Got ".$return_data." levels.");
    return intval($return_data);
};

header('Content-Type: application/json; charset=utf-8');
header('Connection: keep-alive');
$parameters=explode("&",$_SERVER['QUERY_STRING']);
$requests=array();

foreach($parameters as $v){
    $requests = array_merge($requests , array(explode("=",$v)[0] => explode("=",$v)[1]) );
};
//check client
if ($requests['test']!=="true"){
if ($_SERVER['HTTP_USER_AGENT']!=="GameMaker HTTP"){
header_remove("Content-Type");
header_remove("Connection");
header("HTTP/1.1 301 Moved Permanently"); 
header("Location: https://cloud.smmwe.ml/");
echo json_encode(array("message"=>"Utilice SMMWE Cloud para la version web.","error_type"=>"004"));
};
};

if ($requests['type']==="login") {
    //login
    echo json_encode(array("username"=>"Servidor privado","alias"=>$requests['username'],"id"=>"530177024614989824","usergroup"=>0,"ip"=>"127.0.0.1","auth_code"=>"SMMWEPSVR"));
    logtovb($requests['username']." was logged into private server.");
    return;
} elseif ($requests['type']==="stage") {
    //get stage details
        if ($requests["by"]==="detailed_search") {
        logtovb("Loading course world ...");
        $num_rows=get_max_files();
        $rows_perpage=10;
        $max_pages=ceil(get_max_files()/10);
        $level_list=list_levels($requests['page']);
            $result[0]=get_result(str_replace(".swe","",$level_list[0]));
            $result[1]=get_result(str_replace(".swe","",$level_list[1]));
            $result[2]=get_result(str_replace(".swe","",$level_list[2]));
            $result[3]=get_result(str_replace(".swe","",$level_list[3]));
            $result[4]=get_result(str_replace(".swe","",$level_list[4]));
            $result[5]=get_result(str_replace(".swe","",$level_list[5]));
            $result[6]=get_result(str_replace(".swe","",$level_list[6]));
            $result[7]=get_result(str_replace(".swe","",$level_list[7]));
            $result[8]=get_result(str_replace(".swe","",$level_list[8]));
            $result[9]=get_result(str_replace(".swe","",$level_list[9]));
        echo substr(str_replace("\"result\":{","\"result\":[{",urldecode(stripslashes(json_encode(array("type"=>"detailed_search","num_rows"=>strval($num_rows),"rows_perpage"=>strval($rows_perpage),"pages"=>strval($max_pages),"result"=>$result))))),0,-3) . "}]}";
        return;
        } elseif ($requests["by"]==="file") {
        echo json_encode(array("data"=>get_level(get_metadata_by_id($requests['id'],"level_name"))));
        return;
        } elseif ($requests['by']==="id") {
            logtovb("Loading course world ...");
            echo json_encode(array("type"=>"id","result"=>get_result_by_id($requests['id'])));
            return;
        };
} elseif ($requests['type']==="info") {
    //echo log_to_vb("LogSystem test");
    return;
} elseif ($requests['type']==="stats") {
    echo json_encode(array("message"=>"PrivateServer Error: Not implemented.","error_type"=>"028"));
    error_log("Status system isn't implemented, ignored.");
    return;
} else {
    echo json_encode(array("message"=>"No se aclara el tipo de solicitud.","error_type"=>"001"));
    logtovb("Request Error: No se aclara el tipo de solicitud.");
    return;
};
?>