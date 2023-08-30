<?php

//функция принимает как аргумент три строки — фамилию, имя и отчество. 
//Возвращает как результат их же, но склеенные через пробел.

function getFullnameFromParts($surname,$name,$patronomyc){

    $fullname = $surname.' '.$name.' '.$patronomyc;

    return $fullname;
}

//функция принимает как аргумент одну строку — склеенное ФИО. 
//Возвращает как результат массив из трёх элементов с ключами ‘name’, ‘surname’ и ‘patronomyc’.

function getPartsFromFullname($fullname){

    $partsArray = [
        'surname' => explode(' ',$fullname)[0],
        'name' => explode(' ',$fullname)[1],
        'patronomyc' => explode(' ',$fullname)[2],
    ] ;
     
    return $partsArray;
}

//функция принимает как аргумент строку, содержащую ФИО вида «Иванов Иван Иванович» и 
//возвращает строку вида «Иван И.», где сокращается фамилия и отбрасывается отчество.

function getShortName($fullname){

    $partsArray = getPartsFromFullname($fullname);
    $shortName = $partsArray['name'].' '.mb_substr($partsArray['surname'],0,1).'.';

    return $shortName;
}

//функция для определения пола по фамилии
function getGenderFromName($fullname){

    $partsArray = getPartsFromFullname($fullname);
    $genderIndication = 0;
    //проверка на признаки женского пола
    if (mb_substr($partsArray['patronomyc'],-3,3)==='вна'){
        $genderIndication--;
    }
    if (mb_substr($partsArray['name'],-1,1)==='а'){
        $genderIndication--;
    }
    if (mb_substr($partsArray['surname'],-2,2)==='ва'){
        $genderIndication--;
    }
    //проверка на признаки мужского пола
    if (mb_substr($partsArray['patronomyc'],-2,2)==='ич'){
        $genderIndication++;
    }
    if (mb_substr($partsArray['name'],-1,1)==='й'||mb_substr($partsArray['name'],-1,1)==='н'){
        $genderIndication++;
    }
    if (mb_substr($partsArray['surname'],-1,1)==='в'){
        $genderIndication++;
    }

    return $genderIndication<=>0;
}

//Функция для определения полового состава аудитории
function getGenderDescription($array){

    $male = array_filter($array, function($array) {
        return getGenderFromName($array['fullname']) === 1;
    });

    $female = array_filter($array, function($array) {
        return getGenderFromName($array['fullname']) === -1;
    });

    $notDefined = array_filter($array, function($array) {
        return getGenderFromName($array['fullname']) === 0;
    });

    $malePart =  round(count($male) / count($array) * 100, 1);
    $femalePart = round(count($female) / count($array) * 100, 1);
    $notDefinedPart = round(count($notDefined) / count($array)  * 100, 1);

echo <<<STATISTICS
    Гендерный состав аудитории:
    ---------------------------
    Мужчины - $malePart %
    Женщины - $femalePart %
    Не удалось определить - $notDefinedPart %
STATISTICS;
}

//функция для определения «идеальной» пары.

function getPerfectPartner($surname, $name, $patronomyc, $array){

    //Определение ФИО вводимого пользователя и его пола
    $fullName_half1 = mb_convert_case(getFullnameFromParts(trim($surname),trim($name),trim($patronomyc)), MB_CASE_TITLE);
    $gender_half1 = getGenderFromName($fullName_half1);

    //получение случайного пользователя из массива и определение его пола
    $keyRand = array_rand($array);
    $fullName_half2 = $array[$keyRand]['fullname'];
    $gender_half2 = getGenderFromName($fullName_half2);

    //если не удалось определить пол введенного пользователя
    if ($gender_half1 === 0){

    $couple = 'Для '.getShortName($fullName_half1).' пары не найдено';

echo <<<PERFECTCOUPLE
    $couple
    \u{1F61E}
PERFECTCOUPLE;
    //подбираем пару пока не найден вариант с противоположным полом
    }else {
        while ($gender_half1 === $gender_half2 || $gender_half2 === 0){
            $keyRand = array_rand($array);
            $fullName_half2 = $array[$keyRand]['fullname'];
            $gender_half2 = getGenderFromName($fullName_half2);
        }
    
        $couple = getShortName($fullName_half1).' + '.getShortName($fullName_half2).' =';
        $randPercent = rand(5000,10000)/100;
    
echo <<<PERFECTCOUPLE
    $couple
    \u{2661} Идеально на $randPercent% \u{2661}
PERFECTCOUPLE;
    }
}

?>