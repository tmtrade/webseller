<?
/**
 * 重新定义url规则
 */

//定义商标详情规则(旧规则，需要支持)
$rules[] = array(
    '#/p-#',
    array('mod' => 'quotation', 'action' => 'view'),
    array(
        'short' => '#(\d+-\d+)#',
        ),
);