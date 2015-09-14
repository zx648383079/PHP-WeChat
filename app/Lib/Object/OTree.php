<?php
namespace App\Lib\Object;
/******
分类树类
@ 来源：http://www.cnblogs.com/rainman/p/3837671.html
*******/

class OTree implements IBase
{
    
    public function get_tree_child($data, $fid) {
        $result = array();
        $fids = array($fid);
        do {
            $cids = array();
            $flag = false;
            foreach($fids as $fid) {
                for($i = count($data) - 1; $i >=0 ; $i--) {
                    $node = $data[$i];
                    if($node['fid'] == $fid) {
                        array_splice($data, $i , 1);
                        $result[] = $node['id'];
                        $cids[] = $node['id'];
                        $flag = true;
                    }
                }
            }
            $fids = $cids;
        } while($flag === true);
        return $result;
    }
    
    public function get_tree_parent($data, $id) {
        $result = array();
        $obj = array();
        foreach($data as $node) {
            $obj[$node['id']] = $node;
        }    
    
        $value = isset($obj[$id]) ? $obj[$id] : null;    
        while($value) {
            $id = null;
            foreach($data as $node) {
                if($node['id'] == $value['fid']) {
                    $id = $node['id'];
                    $result[] = $node['id'];
                    break;
                }
            }
            if($id === null) {
                $result[] = $value['fid'];
            }
            $value = isset($obj[$id]) ? $obj[$id] : null;
        }
        unset($obj);
        return $result;
    }
    
    public function get_tree_ul($data, $fid) {
        $stack = array($fid);
        $child = array();
        $added_left = array();
        $added_right= array();
        $html_left     = array();
        $html_right    = array();
        $obj = array();
        $loop = 0;
        foreach($data as $node) {
            $pid = $node['fid'];
            if(!isset($child[$pid])) {
                $child[$pid] = array();
            }
            array_push($child[$pid], $node['id']);
            $obj[$node['id']] = $node;
        }
    
        while (count($stack) > 0) {    
            $id = $stack[0];
            $flag = false;
            $node = isset($obj[$id]) ? $obj[$id] : null;
            if (isset($child[$id])) {
                $cids = $child[$id];
                $length = count($cids);
                for($i = $length - 1; $i >= 0; $i--) {
                    array_unshift($stack, $cids[$i]);
                }
                $obj[$cids[$length - 1]]['isLastChild'] = true;
                $obj[$cids[0]]['isFirstChild'] = true;
                $flag = true;
            }
            if ($id != $fid && $node && !isset($added_left[$id])) {
                if(isset($node['isFirstChild']) && isset($node['isLastChild']))  {
                    $html_left[] = '<li class="first-child last-child">';
                } else if(isset($node['isFirstChild'])) {
                    $html_left[] = '<li class="first-child">';
                } else if(isset($node['isLastChild'])) {
                    $html_left[] = '<li class="last-child">';
                } else {
                    $html_left[] = '<li>';
                }            
                $html_left[] = ($flag === true) ? "<div>{$node['title']}</div><ul>" : "<div>{$node['title']}</div>";
                $added_left[$id] = true;
            }    
            if ($id != $fid && $node && !isset($added_right[$id])) {
                $html_right[] = ($flag === true) ? '</ul></li>' : '</li>';
                $added_right[$id] = true;
            }
    
            if ($flag == false) {
                if($node) {
                    $cids = $child[$node['fid']];
                    for ($i = count($cids) - 1; $i >= 0; $i--) {
                        if ($cids[$i] == $id) {
                            array_splice($child[$node['fid']], $i, 1);
                            break;
                        }
                    } 
                    if(count($child[$node['fid']]) == 0) {
                        $child[$node['fid']] = null;
                    }
                }
                array_push($html_left, array_pop($html_right));
                array_shift($stack);
            }
            $loop++;
            if($loop > 5000) return $html_left;
        }
        unset($child);
        unset($obj);
        return implode('', $html_left);
    }
    
    public function get_tree_option($data, $fid) {
        $stack = array($fid);
        $child = array();
        $added = array();
        $options = array();
        $obj = array();
        $loop = 0;
        $depth = -1;
        foreach($data as $node) {
            $pid = $node['fid'];
            if(!isset($child[$pid])) {
                $child[$pid] = array();
            }
            array_push($child[$pid], $node['id']);
            $obj[$node['id']] = $node;
        }
    
        while (count($stack) > 0) {    
            $id = $stack[0];
            $flag = false;
            $node = isset($obj[$id]) ? $obj[$id] : null;
            if (isset($child[$id])) {
                for($i = count($child[$id]) - 1; $i >= 0; $i--) {
                    array_unshift($stack, $child[$id][$i]);
                }
                $flag = true;
            }
            if ($id != $fid && $node && !isset($added[$id])) {
                $node['depth'] = $depth;
                $options[] = $node;
                $added[$id] = true;
            }
            if($flag == true){
                $depth++;
            } else {
                if($node) {
                    for ($i = count($child[$node['fid']]) - 1; $i >= 0; $i--) {
                        if ($child[$node['fid']][$i] == $id) {
                            array_splice($child[$node['fid']], $i, 1);
                            break;
                        }
                    } 
                    if(count($child[$node['fid']]) == 0) {
                        $child[$node['fid']] = null;
                        $depth--;
                    }
                }
                array_shift($stack);
            }
            $loop++;
            if($loop > 5000) return $options;
        }
        unset($child);
        unset($obj);
        return $options;
    }
    
    /**
    * 此方法由@Tonton 提供
    * http://my.oschina.net/u/918697
    * @date 2012-12-12 
    */
    function genTree5($items) { 
        foreach ($items as $item) 
            $items[$item['pid']]['son'][$item['id']] = &$items[$item['id']]; 
        return isset($items[0]['son']) ? $items[0]['son'] : array(); 
    } 
    
    /**
    * 将数据格式化成树形结构
    * @author Xuefen.Tong
    * @param array $items
    * @return array 
    */
    function genTree9($items) {
        $tree = array(); //格式化好的树
        foreach ($items as $item)
            if (isset($items[$item['pid']]))
                $items[$item['pid']]['son'][] = &$items[$item['id']];
            else
                $tree[] = &$items[$item['id']];
        return $tree;
    }
}