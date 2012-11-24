<?php
class d extends Module {
    
    public $groupchat = true;
    public $params_no = true; 
    
    public function run($params, $type, $xmpp) {
        if(count($params) == 2 && is_numeric($params[1])) {
            if((int)$params[1] > 0 && (int)$params[1] < 1000) {
                return "[".rand(1, $params[1])."]";
            }else{
                return "Число не входит в заданный диапазон 1-999!";
            }
            
        }elseif(count($params) == 3 && is_numeric($params[1]) && is_numeric($params[2])) {
            if((int)$params[1] > 0 && (int)$params[1] < 1000) {
                if((int)$params[2] > 0 && (int)$params[2] < 1000) {
                    $num = '';
                    $sum = 0;
                    for($i=1; $i<=$params[1]; $i++) {
                        $r = rand(1, $params[2]);
                        $num .= "[".$r."] ";
                        $sum += $r;
                    }
                    
                    return $num."| [".$sum."]";
                }else{
                    return "Второе число не входит в заданный диапазон 1-999!";
                }
            }else{
                return "Первое число не входит в заданный диапазон 1-999!";
            }
        }else{
             return "Количество параметров больше/меньше максимального или получено не-число!";
        }
    }
}
?>
