<?php
set_time_limit(0);
global $config;
global $domain;
//load config
$config = include($_SERVER['DOCUMENT_ROOT'] . "/config.php");
$domain = include($_SERVER['DOCUMENT_ROOT'] . "/domain.php");
if ($config['linux_mode'] == true) {
    require_once("autoload-linux.php");
} else {
    require_once("autoload.php");
};

use \LeanCloud\Client;
use \LeanCloud\LeanObject;
use \LeanCloud\Query;

define('etiquetas', [
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

define('etiquetas_en', [
    'Standard',
    'Puzzle-solving',
    'Speedrun',
    'Autoscroll',
    'Auto-mario',
    'Short and Sweet',
    'Multiplayer Versus',
    'Themed',
    'Music',
    'Art',
    'Technical',
    'Shooter',
    'Boss battle',
    'Single player',
    'Link'
]);

function get_level($level_name)
{
    global $config;
    global $domain;
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/level_cache/" . $level_name . ".swe")) {
        $return_data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/level_cache/" . $level_name . ".swe");
        return $return_data;
    } else {
        //download the level
        logtovb("Downloading level " . $level_name . " ...");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $domain['smmwe_cloud_url_root'] . rawurlencode($level_name) . ".swe");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $return_data = str_replace("\0", "", curl_exec($curl));
        curl_close($curl);
        if ($config['cache_levels'] == true) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/level_cache/" . $level_name . ".swe", $return_data);
        };
        return $return_data;
    };
};

function parse_level_metadata($level_name, $level_data_args)
{
    logtovb("Parsing metadata of level " . $level_name . " ...");
    //calculate level id
    $level_id = strtoupper(strval(substr(md5($level_data_args), 8, 16)));
    $level_id = strval(substr($level_id, 0, 4) . "-" . substr($level_id, 4, 4) . "-" . substr($level_id, 8, 4) . "-" . substr($level_id, 12, 4));
    //default
    $level_date = "01/01/1970";
    //check duplicate
    if (is_null(get_metadata_by_id($level_id, "level_name")) == false) {
        $level_id = strtoupper(strval(substr(md5($level_name), 8, 16)));
        $level_id = strval(substr($level_id, 0, 4) . "-" . substr($level_id, 4, 4) . "-" . substr($level_id, 8, 4) . "-" . substr($level_id, 12, 4));
    };
    $level_data = json_decode(base64_decode(substr($level_data_args, 0, strlen($level_data_args) - 40)), true);
    $level_author = $level_data["MAIN"]["AJUSTES"][0]['user'];

    if (is_null($level_data) == true) {
        $level_apariencia = "3";
    } else {
        $level_apariencia = strval($level_data["MAIN"]["AJUSTES"][0]['apariencia']);
    };
    
    if (is_null($level_data) == true) {
        $level_date = "01/01/1970";
    } else {
        $level_date = stripslashes(strval($level_data["MAIN"]["AJUSTES"][0]['date']));
    };

    $level_label1 = intval($level_data["MAIN"]["AJUSTES"][0]['etiqueta1']);
    if ($level_label1 === -1) {
        $level_label1 = "---";
    } else {
        $level_label1 = etiquetas[$level_label1];
    };
    $level_label2 = intval($level_data["MAIN"]["AJUSTES"][0]['etiqueta2']);
    if ($level_label2 === -1) {
        $level_label2 = "---";
    } else {
        $level_label2 = etiquetas[$level_label2];
    };
    if (($level_label1 === "Tradicional") && ($level_label2 === "Tradicional")) {
        $level_label2 = "---";
    };
    if (strlen($level_author) === 0) {
        $level_author = "SMMWE Cloud";
    };
    if ($level_author === "0") {
        $level_author = "SMMWE Cloud";
    };
    $metadatas["level_name"] = $level_name;
    $metadatas["level_author"] = $level_author;
    $metadatas["level_date"] = $level_date;
    $metadatas["level_id"] = $level_id;
    $metadatas["level_apariencia"] = $level_apariencia;
    $metadatas["level_label1"] = $level_label1;
    $metadatas["level_label2"] = $level_label2;
    return $metadatas;
};


function post_level_metadata($metadata)
{
    global $config;
    Client::initialize($config['leancloud_api_id'], $config['leancloud_api_key'], $config['leancloud_master_key']);
    logtovb("Posting metadata " . $metadata['level_id'] . " to LeanCloud database ...");
    $metadataObject = new LeanObject("Metadata");
    $metadataObject->set("level_name", strval($metadata['level_name']));
    $metadataObject->set("level_id", $metadata['level_id']);
    $metadataObject->set("level_author", strval($metadata['level_author']));
    $metadataObject->set("level_apariencia", strval($metadata['level_apariencia']));
    $metadataObject->set("level_date", $metadata['level_date']);
    $metadataObject->set("level_label1", $metadata['level_label1']);
    $metadataObject->set("level_label2", $metadata['level_label2']);
    $metadataObject->save();
};

function object_array($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
};

function get_metadata_by_id($level_id, $metadata)
{
    global $config;
    Client::initialize($config['leancloud_api_id'], $config['leancloud_api_key'], $config['leancloud_master_key']);
    $query = new Query("Metadata");
    $query->equalTo("level_id", $level_id);
    $query->select(strval($metadata));
    $metadatas_query = $query->first();
    $return_data = $metadatas_query->get(strval($metadata));
    return $return_data;
};

function get_metadata_by_name($level_name, $metadata)
{
    global $config;
    Client::initialize($config['leancloud_api_id'], $config['leancloud_api_key'], $config['leancloud_master_key']);
    $query = new Query("Metadata");
    $query->equalTo("level_name", $level_name);
    $query->select(strval($metadata));
    $metadatas_query = $query->first();
    $return_data = $metadatas_query->get(strval($metadata));
    return $return_data;
};

function gen_metadata_by_name($level_name)
{
    global $config;
    Client::initialize($config['leancloud_api_id'], $config['leancloud_api_key'], $config['leancloud_master_key']);
    $query = new Query("Metadata");
    $query->equalTo("level_name", $level_name);
    $query->select("-objectId", "-createdAt", "-updatedAt");
    $metadatas_query = $query->first();
    $return_data['level_name'] = $level_name;
    $return_data['level_id'] = $metadatas_query->get("level_id");
    $return_data['level_author'] = $metadatas_query->get("level_author");
    $return_data['level_apariencia'] = $metadatas_query->get("level_apariencia");
    $return_data['level_entorno'] = "0";
    $return_data['level_label1'] = $metadatas_query->get("level_label1");
    $return_data['level_label2'] = $metadatas_query->get("level_label2");
    $return_data['level_date'] = $metadatas_query->get("level_date");
    return $return_data;
};

function search_by_author($level_author)
{
    global $config;
    Client::initialize($config['leancloud_api_id'], $config['leancloud_api_key'], $config['leancloud_master_key']);
    $query = new Query("Metadata");
    $query->contains("level_author", $level_author);
    $query->select("-objectId", "-createdAt", "-updatedAt");
    $count=0;
    $metadatas_query = $query->find();
    foreach($metadatas_query as $v) {
        $level_label1=$v->get("level_label1");
        $level_label2=$v->get("level_label1");
        if (($level_label1 === "Tradicional") && ($level_label2 === "Tradicional")) {
            $level_label2 = "---";
        };
        $level_etiquetas = $level_label1 . "," . $level_label2;
        $result[$count] = array("name" => $v->get("level_name"), "img" => "https://smmwe.online/new_level.png", "likes" => "0", "downloads" => "1", "comments" => "0", "dislikes" => "0", "intentos" => "0", "muertes" => "0", "victorias" => "0", "apariencia" => $v->get("level_apariencia"), "entorno" => "0", "etiquetas" => $level_etiquetas, "featured" => "0", "user_data" => array("data" => "no", "completed" => "no", "liked" => "0"), "record" => array("record" => "no"), "date" => $v->get("level_date"), "author" => $v->get("level_author"), "authorimg" => "https://smmwe.online/favicon.png", "description" => urlencode("Sin Descripción"), "id" => $v->get("level_id"));
        $count=$count+1;
    };
    return $result;
};

function gen_metadata_by_id($level_id)
{
    global $config;
    Client::initialize($config['leancloud_api_id'], $config['leancloud_api_key'], $config['leancloud_master_key']);
    $query = new Query("Metadata");
    $query->equalTo("level_id", $level_id);
    $query->select("-objectId", "-createdAt", "-updatedAt");
    $metadatas_query = $query->first();
    $return_data['level_name'] = $metadatas_query->get("level_name");
    $return_data['level_id'] = $level_id;
    $return_data['level_author'] = $metadatas_query->get("level_author");
    $return_data['level_apariencia'] = $metadatas_query->get("level_apariencia");
    $return_data['level_entorno'] = "0";
    $return_data['level_label1'] = $metadatas_query->get("level_label1");
    $return_data['level_label2'] = $metadatas_query->get("level_label2");
    $return_data['level_date'] = $metadatas_query->get("level_date");
    return $return_data;
};

function list_levels_byname($page)
{
    global $domain;
    logtovb("Listing levels ...");
    $page_om = ceil($page / 20);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $domain['smmwe_cloud_url_root'] . "?apiv3-filename");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "pagenum=" . $page_om);
    $return_data = curl_exec($curl);
    $return_data = array_slice(explode("\n", $return_data), (($page - (($page_om - 1) * 20)) - 1) * 10, 10);
    curl_close($curl);
    return $return_data;
};

function list_levels_newarrival($page)
{
    global $domain;
    logtovb("Listing levels ...");
    $page_om = ceil($page / 20);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $domain['smmwe_cloud_url_root'] . "?apiv3-filename-time");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "pagenum=" . $page_om);
    $return_data = curl_exec($curl);
    $return_data = array_slice(explode("\n", $return_data), (($page - (($page_om - 1) * 20)) - 1) * 10, 10);
    curl_close($curl);
    return $return_data;
};

function upload_level($level_name, $level_data, $level_apariencia, $level_label1, $level_label2)
{
    global $domain;
    logtovb("Uploading level " . rawurldecode($level_name) . " ...");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $domain['smmwe_cloud_url_api'] . "?upload=" . $level_name . '.swe&key=yidaozhan-gq-franyer-farias-apiv2');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $level_data);
    error_log(curl_exec($curl));
    curl_close($curl);
    $metadatas["level_name"] = rawurldecode($level_name);
    $level_data_parsed = parse_level_metadata(rawurldecode($level_name), $level_data);
    $metadatas["level_author"] = $level_data_parsed['level_author'];
    $metadatas["level_date"] = $level_data_parsed['level_date'];
    $metadatas["level_id"] = $level_data_parsed['level_id'];
    $metadatas["level_apariencia"] = $level_apariencia;
    $metadatas["level_label1"] = $level_label1;
    $metadatas["level_label2"] = $level_label2;
    post_level_metadata($metadatas);
    return json_encode(array("message" => "Completado.", "error_type" => $metadatas["level_id"]));
};


function get_result($level_name)
{
    logtovb("Trying to get level metadata for level: " . $level_name . " ...");
    //check if cloud metadata is created
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $level_name . ".php")) {
        logtovb("Local metadata found: " . $level_name . ".php");
        $result = include($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $level_name . ".php");
        return $result;
        //exit function
    } else {
        logtovb("Local metadata not found: " . $level_name . ".php, Synchronizing ...");
        if (is_null(get_metadata_by_name($level_name, "level_id")) == true) {
            //create cloud metadata
            //get the level
            $level_data = get_level($level_name);
            if (is_null($level_data) == true) {
                //retry
                logtovb("Retry 1 " . $level_name . " ...");
                $level_data = get_level($level_name);
            };
            if (is_null($level_data) == true) {
                //retry 2
                logtovb("Retry 2 " . $level_name . " ...");
                $level_data = get_level($level_name);
            };
            if (is_null($level_data) == true) {
                //retry 3
                logtovb("Retry 3 " . $level_name . " ...");
                $level_data = get_level($level_name);
            };
            $metadata = parse_level_metadata($level_name, $level_data);
            post_level_metadata($metadata);
        } else {
            $metadata = gen_metadata_by_name($level_name);
        };
        $level_author = $metadata["level_author"];
        $level_id = $metadata["level_id"];
        $level_apariencia = $metadata["level_apariencia"];
        $level_entorno = "0";
        $level_label1 = $metadata["level_label1"];
        $level_label2 = $metadata["level_label2"];
        if (($level_label1 === "Tradicional") && ($level_label2 === "Tradicional")) {
            $level_label2 = "---";
        };
        $level_etiquetas = $level_label1 . "," . $level_label2;
        $uploaded_date = "01/01/2021";
        $uploaded_date = $metadata["level_date"];
        $result = array("name" => $level_name, "img" => "https://smmwe.online/new_level.png", "likes" => "0", "downloads" => "1", "comments" => "0", "dislikes" => "0", "intentos" => "0", "muertes" => "0", "victorias" => "0", "apariencia" => $level_apariencia, "entorno" => $level_entorno, "etiquetas" => $level_etiquetas, "featured" => "0", "user_data" => array("data" => "no", "completed" => "no", "liked" => "0"), "record" => array("record" => "no"), "date" => $uploaded_date, "author" => $level_author, "authorimg" => "https://smmwe.online/favicon.png", "description" => urlencode("Sin Descripción"), "id" => $level_id);
        //write local cache
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $level_name . ".php", "<?php\nreturn " . var_export($result, true) . ";\n");
    };
    return $result;
};

function get_result_by_id($level_id)
{
    logtovb("Trying to get level metadata for level: " . $level_id . " ...");
    $level_name = get_metadata_by_id($level_id, "level_name");
    if (is_null($level_name) == true) {
        $result = array("name" => "Error al nivel de busqueda", "img" => "https://smmwe.online/new_level.png", "likes" => "0", "downloads" => "1", "comments" => "0", "dislikes" => "0", "intentos" => "0", "muertes" => "0", "victorias" => "0", "apariencia" => "0", "entorno" => "0", "etiquetas" => "0", "featured" => "0", "user_data" => array("data" => "no", "completed" => "no", "liked" => "0"), "record" => array("record" => "no"), "date" => "01/01/2021", "author" => "Server Error", "authorimg" => "https://smmwe.online/favicon.png", "description" => "Puede ser que el nivel no se haya analizado en la base de datos.", "id" => $level_id);
        //exit
        return $result;
    };
    //check if cloud metadata is created
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $level_name . ".php")) {
        logtovb("get_result: Local metadata found: " . $level_name . ".php");
        $result = include($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $level_name . ".php");
    } else {
        logtovb("Local metadata not found: " . $level_name . ".php, Synchronizing ...");
        $metadata = gen_metadata_by_id($level_id);
        $level_author = $metadata["level_author"];
        $level_id = $metadata["level_id"];
        $level_apariencia = $metadata["level_apariencia"];
        $level_entorno = "0";
        $level_label1 = $metadata["level_label1"];
        $level_label2 = $metadata["level_label2"];
        if (($level_label1 === "Tradicional") && ($level_label2 === "Tradicional")) {
            $level_label2 = "---";
        };
        $level_etiquetas = $level_label1 . "," . $level_label2;
        $uploaded_date = $metadata["level_date"];
        $result = array("name" => $level_name, "img" => "https://smmwe.online/new_level.png", "likes" => "0", "downloads" => "1", "comments" => "0", "dislikes" => "0", "intentos" => "0", "muertes" => "0", "victorias" => "0", "apariencia" => $level_apariencia, "entorno" => $level_entorno, "etiquetas" => $level_etiquetas, "featured" => "0", "user_data" => array("data" => "no", "completed" => "no", "liked" => "0"), "record" => array("record" => "no"), "date" => $uploaded_date, "author" => $level_author, "authorimg" => "https://smmwe.online/favicon.png", "description" => urlencode("Sin Descripción"), "id" => $level_id);
        //write local cache
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $level_name . ".php", "<?php\nreturn " . var_export($result, true) . ";\n");
    };
    return $result;
};

function logtovb($log)
{
    //post log to SMMWEServerGUI
    global $config;
    if ($config['linux_mode'] == false) {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_block($socket);
        socket_connect($socket, '127.0.0.1', 6002);
        $return_data = socket_write($socket, $log, strlen($log));
        socket_close($socket);
    };
    error_log($log);
    if ($config['linux_mode'] == false) {
        return $return_data;
    };
};

function get_storage()
{
    global $domain;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $domain['smmwe_cloud_url_root'] . "?apiv3-diskspace");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $return_data = curl_exec($curl);
    curl_close($curl);
    return $return_data;
};

function get_max_files()
{
    global $domain;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $domain['smmwe_cloud_url_root'] . "?apiv3-maxfiles");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $return_data = curl_exec($curl);
    curl_close($curl);
    logtovb("Got " . $return_data . " levels.");
    return intval($return_data);
};

function get_file_counts($ff)
//https://blog.csdn.net/weixin_33525438/article/details/116183798
{
    $handle = opendir($ff);
    $i = 0;
    while (false !== $file = (readdir($handle))) {
        if ($file !== '.' && $file != '..') {
            $i++;
        }
    }
    closedir($handle);
    return $i;
}

header('Content-Type: application/json; charset=utf-8');
header('Connection: keep-alive');
$parameters = explode("&", $_SERVER['QUERY_STRING']);
$requests = array();

//parameters to array
foreach ($parameters as $v) {
    $requests = array_merge($requests, array(explode("=", $v)[0] => explode("=", $v)[1]));
};

//main function (request)
if ($requests['type'] === "login") {
    //login
    echo json_encode(array("username" => "Servidor privado", "alias" => $requests['username'], "id" => "530177024614989824", "usergroup" => 0, "ip" => "127.0.0.1", "auth_code" => "SMMWEPSVR"));
    logtovb($requests['username'] . " was logged into private server.");
    return;
} elseif ($requests['type'] === "stage") {
    //get stage details
    if ($requests["by"] === "detailed_search") {
        logtovb("Loading course world ...");
        if (is_null($requests['author'])) {
            $num_rows = get_max_files();
            $rows_perpage = 10;
            $max_pages = ceil($num_rows / 10);
            if ($requests['sort'] === "popular") {
                $level_list = list_levels_byname($requests['page']);
            } else {
                $level_list = list_levels_newarrival($requests['page']);
            };
            $result[0] = get_result(str_replace(".swe", "", $level_list[0]));
            $result[1] = get_result(str_replace(".swe", "", $level_list[1]));
            $result[2] = get_result(str_replace(".swe", "", $level_list[2]));
            $result[3] = get_result(str_replace(".swe", "", $level_list[3]));
            $result[4] = get_result(str_replace(".swe", "", $level_list[4]));
            $result[5] = get_result(str_replace(".swe", "", $level_list[5]));
            $result[6] = get_result(str_replace(".swe", "", $level_list[6]));
            $result[7] = get_result(str_replace(".swe", "", $level_list[7]));
            $result[8] = get_result(str_replace(".swe", "", $level_list[8]));
            $result[9] = get_result(str_replace(".swe", "", $level_list[9]));
            logtovb("All the metadatas were loaded, now you can refresh.");
            $result_tmp = substr(str_replace("\"result\":{", "\"result\":[{", urldecode(json_encode(array("type" => "detailed_search", "num_rows" => strval($num_rows), "rows_perpage" => strval($rows_perpage), "pages" => strval($max_pages), "result" => $result), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))), 0, -3) . "}]}";
            if ($config['tag_language'] == 'en') {
                for ($x = 0; $x <= 14; $x++) {
                    $result_tmp = str_replace(etiquetas[$x], etiquetas_en[$x], $result_tmp);
                };
            };
            echo $result_tmp;
            return;
        } else {
            $result=search_by_author($requests['author']);
            logtovb("All the metadatas were loaded, now you can refresh.");
            $result_tmp = substr(str_replace("\"result\":{", "\"result\":[{", urldecode(json_encode(array("type" => "detailed_search", "num_rows" => "1", "rows_perpage" => count($result), "pages" => "1", "result" =>$result), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))), 0, -3) . "}]}";
            if ($config['tag_language'] == 'en') {
                for ($x = 0; $x <= 14; $x++) {
                    $result_tmp = str_replace(etiquetas[$x], etiquetas_en[$x], $result_tmp);
                };
            };
            echo $result_tmp;
            return;
        };
    } elseif ($requests["by"] === "file") {
        echo json_encode(array("data" => get_level(get_metadata_by_id($requests['id'], "level_name"))));
        return;
    } elseif ($requests['by'] === "id") {
        logtovb("Search " . $requests['id'] . " ...");
        echo json_encode(array("type" => "id", "result" => get_result_by_id($requests['id'])));
        return;
    };
} elseif ($requests['type'] === "devel") {
    //devel param is just for test
    echo get_level($requests['id']);
    return;
} elseif ($requests['type'] === "stats") {
    echo json_encode(array("message" => "Error: Status system is not implemented.", "error_type" => "028"));
    error_log("Status system is not implemented, ignored.");
    return;
} elseif ($requests['type'] === "upload") {
    $level_label = explode(",", $requests['lvl_tags']);
    echo upload_level($requests['lvl_name'], str_replace("lvl_swe=", "", file_get_contents("php://input")), $requests['lvl_aparience'], $level_label[0], $level_label[1]);
    return;
} elseif ($requests['type'] === "statistics") {
    header("Content-Type: text/html; charset=utf-8");
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
    global $config;
    Client::initialize($config['leancloud_api_id'], $config['leancloud_api_key'], $config['leancloud_master_key']);
    $query = new Query("Metadata");
    if (preg_match("/zh/i", $lang)) {
        echo "<p>SMMWE Cloud 私服 统计数据</p>";

        echo "<table border=\"1\">";

        echo "<tr>";
        echo "<td>服务器版本</td>";
        echo "<td>" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/version.txt") . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>API 版本</td>";
        echo "<td>" . "V3" . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>云端关卡数量</td>";
        echo "<td>" . strval(get_max_files()) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>云端存储占用</td>";
        echo "<td>" . get_storage() . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>云端元数据数量</td>";
        echo "<td>" . strval($query->count()) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>缓存元数据数量</td>";
        echo "<td>" . strval(get_file_counts($_SERVER['DOCUMENT_ROOT'] . "/cache")) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>缓存关卡数量</td>";
        echo "<td>" . strval(get_file_counts($_SERVER['DOCUMENT_ROOT'] . "/level_cache")) . "</td>";
        echo "</tr>";

        echo "</table>";

        echo "<p>运行于 " . php_uname() . ", PHP版本 " . PHP_VERSION . "</p>";
    } elseif (preg_match("/en/i", $lang)) {
        echo "<p>SMMWE Cloud Private Server statistics</p>";

        echo "<table border=\"1\">";

        echo "<tr>";
        echo "<td>Server Version</td>";
        echo "<td>" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/version.txt") . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>API Version</td>";
        echo "<td>" . "V3" . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Level count</td>";
        echo "<td>" . strval(get_max_files()) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Online storage</td>";
        echo "<td>" . get_storage() . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Metadata count</td>";
        echo "<td>" . strval($query->count()) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Cached metadata count</td>";
        echo "<td>" . strval(get_file_counts($_SERVER['DOCUMENT_ROOT'] . "/cache")) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Cached level count</td>";
        echo "<td>" . strval(get_file_counts($_SERVER['DOCUMENT_ROOT'] . "/level_cache")) . "</td>";
        echo "</tr>";

        echo "</table>";

        echo "<p>Running on " . php_uname() . ", PHP Version " . PHP_VERSION . "</p>";
    } elseif (preg_match("/es/i", $lang)) {
        echo "<p>SMMWE Cloud Servidor Privado estadisticas</p>";

        echo "<table border=\"1\">";

        echo "<tr>";
        echo "<td>Servidor Version</td>";
        echo "<td>" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/version.txt") . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>API Version</td>";
        echo "<td>" . "V3" . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Conteo de niveles</td>";
        echo "<td>" . strval(get_max_files()) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Almacenamiento en linea</td>";
        echo "<td>" . get_storage() . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Conteo de metadatos</td>";
        echo "<td>" . strval($query->count()) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Conteo de metadatos en cache</td>";
        echo "<td>" . strval(get_file_counts($_SERVER['DOCUMENT_ROOT'] . "/cache")) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Conteo de niveles en cache</td>";
        echo "<td>" . strval(get_file_counts($_SERVER['DOCUMENT_ROOT'] . "/level_cache")) . "</td>";
        echo "</tr>";

        echo "</table>";

        echo "<p>El servidor se esta ejecutando en " . php_uname() . ", PHP Version " . PHP_VERSION . "</p>";
    };
    return;
} else {
    echo json_encode(array("message" => "No se aclara el tipo de solicitud.", "error_type" => "001"));
    logtovb("Request Error: No se aclara el tipo de solicitud.");
    return;
};
