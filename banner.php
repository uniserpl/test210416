<?php

/** 
 * Тестовое задание
 * 
 * @author Uniser <uniserpl@gmail.com>
 * @since 2021.04.16 17:17
 */

/**
 * @see https://www.php.net/manual/ru/book.mysqli.php
 * 
 * @staticvar \mysqli $_link
 * @return \mysqli
 */
function getConnection() {
    
    static $_link = null;
    
    if (is_null($_link)) {
        $_link = new mysqli('127.0.0.1:33076', 'root', '', 'db');
        if ($_link->connect_error) {
            die('Connect Error ('.$_link->connect_errno.') '.$_link->connect_error);
        }
        if ( ! $_link->set_charset("utf8")) {
            die("Error loading character set utf8");
        }
    }
    
    return $_link;
}

/**
 * SQL запрос к БД
 * 
 * используйте %s для подстановки параметров
 * 
 * @param string $sql
 * @param string[] $params
 * @param boolean $query SELECT
 * @return array[]
 */
function sql($sql,$params=[],$query=true) {
    $db = getConnection();
    foreach ($params as $key=>$value) {
        $params[$key] = $db->real_escape_string($value);
    }
    /* @var $result \mysqli_result */
    $result = $db->query(vsprintf($sql, $params));
    if ($result) {
        if ($query) return $result->fetch_all(MYSQLI_ASSOC);
        return $db->affected_rows;
    }
    die('Query Error ['.$db->errno.'] '.$db->error);
}

// Превращаем IPv4 в unsigned int
function getRawIp($str_ip) {
    $long = ip2long(trim($str_ip));
    if ($long===false) return null;
    return sprintf("%u", $long);
}
    
/**
 * Обрезаем длинный хвост.
 * Хеш доклеиваем для сохранения уникальности
 * 
 * @param string $agent
 * @return string
 */
function croppedAgent($agent) {
    return substr($agent,0,100).md5($agent);
}
    
/**
 * Запрос в БД на увеличение, либо создание счётчика
 * 
 * PRIMARY KEY (ip_address, user_agent, page_url)
 * 
 */
function incrementViews() {
    sql(<<<'SQLTEXT'
        INSERT INTO visitor (ip_address, user_agent, view_date, page_url, views_count)
        VALUES (%s,"%s",%s,"%s",1)
        ON DUPLICATE KEY UPDATE view_date = %s, views_count = views_count+1
SQLTEXT
    ,[
        getRawIp($_SERVER['REMOTE_ADDR']??'') ?: 0,
        croppedAgent($_SERVER['HTTP_USER_AGENT']??''),
        $now = time(),
        $_SERVER['HTTP_REFERER']??'',
        $now
    ]
    ,false);
}

if (php_sapi_name()=='cli') {
    
    // Test from command line interface
    var_export(sql('SELECT * FROM visitor'));
    
} else {
    
    // Меняем счётчик и отдаём картинку
    incrementViews();
    header('Content-type: image/png');
    readfile(__DIR__.DIRECTORY_SEPARATOR.'banner.png');
    
}

