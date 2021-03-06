<?php
require_once( FACEBOOK_PATH . 'libraries/phpanalysis/phpanalysis.class.php');
/*
 *  Copyright 2014 Frederich.
 *  All rights reserved.
 *
 */

/**
 * Description of classifier_helper
 *
 * @author Frederich
 */
function retrieveEnglish($str){
    $pattern = '/(\[[A-Za-z]+\])|([A-Za-z0-9]+)/';
    $result = array();
    $matches = array();
    preg_match_all($pattern, $str, $result, PREG_PATTERN_ORDER);
    foreach($result as $match){
        foreach($match as $token){
            $matches[]= $token;
        }
    }
    return $matches;
}

function retrieveChinese($str){

    $pattern = '/[\x{4e00}-\x{9fa5}]+/um';
    $result = array();
    $matches = array();
    preg_match_all($pattern, $str, $result, PREG_PATTERN_ORDER);
    foreach($result as $match){
        foreach($match as $token){
            $matches[]=$token;
        }
    }
    return $matches;
}

function partition($str){
    $do_fork = $do_unit = true;
    $do_multi = $do_prop = $pri_dict = false;

    if ($str != '') {
        //初始化类
        PhpAnalysis::$loadInit = false;
        $pa = new PhpAnalysis('utf-8', 'utf-8', $pri_dict);
        //载入词典
        $pa->LoadDict();

        //执行分词
        $pa->SetSource($str);
        $pa->differMax = $do_multi;
        $pa->unitWord = $do_unit;

        $pa->StartAnalysis($do_fork);

        $okresult = $pa->GetFinallyResult(' ', $do_prop);

        return preg_split('/\s/', $okresult);
    }
}

function getSegments($str){
    $str = preg_replace('/([0-9]{1,2})\/([0-9]{1,2})((\/[0-9]{2,4})?)/', '[DATE]', $str);
    $str = preg_replace('/(\$\s*[0-9]+)+/', '[PRICE]', $str);
    $str = preg_replace('/([1-2]([0-9]{3}))+/', '[YEAR]', $str);
    $str = preg_replace('/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/si', '[EMAIL]', $str);
    $str = preg_replace("#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si", '[LINK]', $str);
    $eng = retrieveEnglish($str);
    $chi = array();
    $chi_words = retrieveChinese($str);
    
    foreach($chi_words as $word){
        $tokens = partition($word);
        foreach($tokens as $token){
            $chi[] = $token;
        }
    }
    
    $segments = array_merge($eng, $chi);
    return $segments;
}

function printStructure($arr, $depth = 0){
    echo sprintf('<div style="margin-left: %dpx" >', $depth*10);
    if(is_array($arr)){
        foreach($arr as $key => $elem){
            echo $key.'=>';
            printStructure($elem, $depth+1);
        }
    }
    else{
        echo $arr;
    }
    echo '</div>';
}