<?php

namespace App\Libraries;

class Table {

    private $param_pagination = array(
        'total_rows' => 0,
        'page' => 1,
        'per_page' => 10,
        'total_side_link' => 5,
        'base_url' => '',
        'go_to_page' => false,
        'next' => "Next",
        'previous' => "Prev",
        'first' => "First",
        'class' => '',
        'last' => 'Last',
        'reverse_paging' => false,
        'query_string' => true
    );
    private $base_url = '';
    private $config = array();
    private $tr_config = array();
    private $columns = array();
    private $data = array();
    private $tfoot = '';


    function set_data ($data) {
        $this->data = $data;
        return $this;
    }

    function set_tr_config($config){
        $this->tr_config = $config;
        return $this;
    }

    function set_config ($config) {
        $this->config = $config;
        return $this;
    }

    function set_columns ($columns) {
        $this->columns = $columns;
        return $this;
    }

    function build_pagingBtn ($pagination_config) {
        $content = '';
        if ( count($pagination_config) > 0 ) {
            $this->set_pagination($pagination_config);
            $content = $this->link_pagination();
        }
        return $content;
    }

    function create_list ($config = false, $data = false, $columns = false, $pagination_config = array()) {
        $this->set_pagination($pagination_config);
        if ( $config === false )
            $config = $this->config;
        if ( $data === false )
            $data = $this->data;
        if ( $columns === false )
            $columns = $this->columns;
        $content = '';
        if ( is_array($columns) ) {
            $content = '<table';
            foreach ( $config as $attr => $value )
                $content .= " $attr=\"$value\"";
            $content .= '>';
            $content .= "<thead>
                            <tr>";
            $row_data = '"';
            $array_link = array();
            foreach ( $columns as $column ) {
                $column['type'] = (isset($column['type'])) ? $column['type'] : '';
                if (isset($column['type']) &&  $column['type'] == 'checkbox')
                    $content .= '<th '. ((isset($column['class'])) ? ' class="' . $column['class'] . '"' : '') .' ' . ((isset($column['width'])) ? ' style="width:' . $column['width'] . ';"' : '') . '>
                                    <input type="checkbox" id="check_all_'.$column['name'].'"/></th>';
                else
                    $content .= (is_array($column)) ? "<th ". ((isset($column['class'])) ? ' class="' . $column['class'] . '"' : '')  ." " . ((isset($column['width'])) ? ' style="width:' . $column['width'] . ';"' : '') . ">" . $column['header'] . "</th>" : "<th>$column</th>";
                switch ( $column['type'] ) {
                    case 'checkbox':
                        $row_data .= '<td '.((isset($column['class'])) ? ' class=\'' . $column['class'] . '\'' : '').' ' . ((isset($column['align'])) ? ' align=\'' . $column['align'] . '\'' : '') . ' ' . ((isset($column['valign'])) ? ' valign=\'' . $column['valign'] . '\'' : '') . '><input type=\'checkbox\' rel=\''.$column['name'].'\' name=\''.$column['name'].'[]\' value=\''.(isset($column['value']) ? '{"'.$column['value'].'"}' : '' ).'\'/></td>';
                        break;
                    case 'link' :
                        $matches = explode('|', $column['data']);
                        //$row_data .= '<td class=\"admdk-align-c admdk-width180\" ' . ((isset($column['align'])) ? ' align="' . $column['align'] . '"' : '') . ' ' . ((isset($column['valign'])) ? ' valign="' . $column['valign'] . '"' : '') . '>';
                        $row_data .= '<td '.((isset($column['class'])) ? ' class=\'' . $column['class'] . '\'' : '').'  ' . ((isset($column['align'])) ? ' align="' . $column['align'] . '"' : '') . ' ' . ((isset($column['valign'])) ? ' valign="' . $column['valign'] . '"' : '') . '>';
                        $row_data .='<div class=\"btn-group\">';
                        foreach ( $matches as $value )
                        {
                            $tmp = '';
                            if (isset($column['format'][$value]['attr']) && is_array($column['format'][$value]['attr']))
                                foreach ($column['format'][$value]['attr'] as $attr => $attr_val)
                                    $tmp .= $attr.'=\"'.$attr_val.'\" ';

                            $array_link[] = '<a '.$tmp.' href=\"' . (isset($column['format'][$value]['href']) ? $column['format'][$value]['href'] : '#') . '\" ' . (isset($column['format'][$value]['onclick']) ? 'onclick=\"' . str_replace('"', "'", $column['format'][$value]['onclick']) . '\"' : '') . ' ' . (isset($column['format'][$value]['class']) ? 'class=\"' . $column['format'][$value]['class'] . '\"' : '') . '>' . (isset($column['format'][$value]['label']) ? $column['format'][$value]['label'] : $value) . '</a>';
                        }
                        //$row_data .= implode(' | ',$array_link)."</div></td>";
                        $row_data .= implode('', $array_link) . "</div></td>";
                        break;
                    case 'number' :
                    default :
                        if ( is_array($column) )
                            $value = ($column['type'] == 'number') ? '{"number_i"}' : ((isset($column['data'])) ? '{"' . $column['data'] . '"}' : '');
                        else
                            $value = ((isset($column)) ? '{"' . $column . '"}' : '');
                        $row_data .= (is_array($column)) ? '<td '.((isset($column['class'])) ? ' class=\'' . $column['class'] . '\'' : '').' ' . ((isset($column['align'])) ? '  style=\'text-align:' . $column['align'] . '\'' : '') . ' ' . ((isset($column['valign'])) ? ' valign=\'' . $column['valign'] . '\'' : '') . '>' . /* ((isset($column['escape']) && $column['escape'] == true) ? htmlentities(((isset($row[$column['data']])) ? $row[$column['data']] : '')) : ((isset($row[$column['data']])) ? $row[$column['data']] : '')) */$value . '</td>' : "<td>" . $value . "</td>";
                }
            }
            $row_data .= '"';
            $content .= "</tr>
                            </thead>
                            <tbody>";
            if ( is_array($data) && count($data) > 0 && count($pagination_config) > 0 ) {
                $i = ( $this->param_pagination['page'] - 1 ) * $this->param_pagination['per_page'];
                //print_r($data);die();
                foreach ( $data as $row ) {
                    $i++;
                    $row['number_i'] = $i;
                    $class = ($i % 2 == 0) ? 'even' : 'odd';
                    $addAttr = array();
                    foreach ($this->tr_config as $attr => $attrval) {
                        $search_field = array();
                        foreach ($row as $field => $value) {
                            $search_field[] = "{".strtoupper($field)."}";
                        }
                        $addAttr[] = $attr.'="'.str_replace($search_field, $row, $attrval).'"';
                    }
                    $content .= "<tr class=\"$class\" ".implode(' ', $addAttr)." \">";
                    eval('$content .= ' . preg_replace('/{"(\w+)"}/', '".((isset($row[\'\1\'])) ? $row[\'\1\'] : \'\')."', $row_data) . ';'); //preg_replace('/{"(\w+)"}/','$row[\'\1\']',$row_data);
                    //echo "<br />".$row_data;
                    //echo $content;
                    //die();
                    $content .= "</tr>";
                }
            }
            elseif ( is_array($data) && count($data) > 0 ) {
                $i = 0;
                foreach ( $data as $row ) {
                    $i++;
                    $row['number_i'] = $i;
                    $class = ($i % 2 == 0) ? 'even' : 'odd';
                    $tr_class = (isset($row['tr_class']) ? $row['tr_class'] : '');
                    $content .= "<tr class=\"$class $tr_class\">";
                    eval('$content .= ' . preg_replace('/{"(\w+)"}/', '".((isset($row[\'\1\'])) ? $row[\'\1\'] : \'\')."', $row_data) . ';'); //preg_replace('/{"(\w+)"}/','$row[\'\1\']',$row_data);
                    //echo "<br />".$row_data;
                    //echo $content;
                    //die();
                    $content .= "</tr>";
                }
            }
            else
                $content .= '<tr><td colspan="' . count($columns) . '">No records found !</td></tr>';
            $content .= "</tbody>";
            if ($this->getTFoot())
                $content .= '<tfoot>'.$this->getTFoot().'</tfoot>';
            $content .= "</table>";
        }
        if ( count($pagination_config) > 0 ) {
            $content .= $this->link_pagination();
        }
        return $content;
    }

    function getTFoot(){
        return $this->tfoot;
    }

    function setTFoot($content){
        $this->tfoot = $content;
        return $this;
    }

    function set_pagination ($param) {
        if ( is_array($param) )
            foreach ( $param as $key => $value )
                $this->param_pagination[$key] = $value;
        return $this;
    }

    function getURLParameter () {
        if ( $this->param_pagination['base_url'] ) {
            #preg_match('/(.+)\?(.+)/',$this->param_pagination['base_url'],$match);
            preg_match('/(.+)\&(.+)/', $this->param_pagination['base_url'], $match);
            parse_str($match[2], $param);
            $this->base_url = $match[1];
        }
        else
            $param = array();
        return $param;
    }

    function link_pagination () {
        if ( $this->param_pagination['total_rows'] == 0 OR $this->param_pagination['per_page'] == 0 )
            return '';
        // hitung jumlah halaman
        //print_r($this->param_pagination);
        $num_pages = ceil($this->param_pagination['total_rows'] / $this->param_pagination['per_page']);
        if ( $num_pages == 1 )
            return '';
        //echo $num_pages;
        $num_pages = $this->param_pagination['reverse_paging'] ? $num_pages - 2 : $num_pages;

        if ( !is_numeric($this->param_pagination['page']) || $this->param_pagination['page'] < 0 )
            $this->param_pagination['page'] = 1;

        if ( $this->param_pagination['page'] > $this->param_pagination['total_rows'] )
            $this->param_pagination['page'] = $num_pages - 1;

        $uri_page_number = $this->param_pagination['page'];
        //cari awal dan akhir dari link yang ditampilkan

        $start = (($this->param_pagination['page'] - $this->param_pagination['total_side_link']) > 0) ? $this->param_pagination['page'] - ($this->param_pagination['total_side_link']) : 1;
        $end = (($this->param_pagination['page'] + $this->param_pagination['total_side_link']) < $num_pages) ? $this->param_pagination['page'] + $this->param_pagination['total_side_link'] : $num_pages;
        $selisih_right = $selisih_left = 0;
        if ( $this->param_pagination['page'] - $start < $this->param_pagination['total_side_link'] ) {
            $selisih_left = $this->param_pagination['total_side_link'] - ($this->param_pagination['page'] - $start);
            $end = (($end + $selisih_left) < $num_pages) ? ($end + $selisih_left) : $num_pages;
        }
        if ( $end - $this->param_pagination['page'] < $this->param_pagination['total_side_link'] ) {
            $selisih_right = $this->param_pagination['total_side_link'] - ($end - $this->param_pagination['page']);
            $start = (($this->param_pagination['page'] - ($this->param_pagination['total_side_link'] + $selisih_right)) > 0) ? ($this->param_pagination['page'] - ($this->param_pagination['total_side_link'] + $selisih_right)) : 1;
        }
        //echo $end;
        //echo $this->param_pagination['total_rows'].' / '.$this->param_pagination['per_page'].', '.$num_pages;die();
        $go_to_page = '';
        if ( $this->param_pagination['query_string'] ) {
            if(strpos(trim($this->param_pagination['base_url']), '?'))
                $this->param_pagination['base_url'] = trim($this->param_pagination['base_url']) . '&page=';
            else
                $this->param_pagination['base_url'] = trim($this->param_pagination['base_url']) . '?&page=';
            if ( $this->param_pagination['go_to_page'] ) {
                $link_param = $this->getURLParameter();
                $hidden_input = "";
                foreach ( $link_param as $k => $v )
                    if ( $k != 'page' )
                        $hidden_input .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
                $go_to_page = '<div class="page-jump"><form action="' . $this->base_url . '" method="get">' . $hidden_input . '<label for="jump_page">Page</label> : <input type="text" name="page" size="3" value="' . $this->param_pagination['page'] . '" id="jump_page"/><input type="submit" value="Go" onclick="var page = document.getElementById(\'jump_page\').value; if (page <= ' . $num_pages . ') return true; else {alert(\'Page does not exist. Maximum page is ' . $num_pages . '\');return false;}"/></form></div> &nbsp;<div class="lg-paging">';
            }
        }
        $output = $go_to_page. '<ul class="pagination '.$this->param_pagination['class'].'">';
        // first link
        if ( $this->param_pagination['page'] > ($this->param_pagination['total_side_link'] + 1) && $this->param_pagination['first'] != '' )
            $output .= '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . '1' : str_replace('{PAGE}', '', $this->param_pagination['base_url']) ) . '">' . $this->param_pagination['first'] . '</a></li>';

        if ( $this->param_pagination['reverse_paging'] ) {
            // link prev
            if ( $this->param_pagination['page'] > 1 && $this->param_pagination['previous'] != '' ) {
                $pg = ($num_pages + 2) - $this->param_pagination['page'];
                $tmp = $pg == 1 || $pg == $num_pages ? '' : $pg + 1;
                $output .= '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . ((($num_pages + 2) - $this->param_pagination['page']) - 1) : str_replace('{PAGE}', $tmp, $this->param_pagination['base_url']) ) . '">' . $this->param_pagination['previous'] . '</a></li>';
            }
        }
        else {
            // link prev
            if ( $this->param_pagination['page'] > 1 && $this->param_pagination['previous'] != '' ) {
                //$tmp = ($this->param_pagination['page'] + 1 == 1) ? '' : ($this->param_pagination['page'] + 1) ;
                $tmp = ($this->param_pagination['page'] - 1 == 1) ? '' : ($this->param_pagination['page'] - 1);
                $output .= '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . ($this->param_pagination['page'] - 1) : str_replace('{PAGE}', $tmp, $this->param_pagination['base_url']) ) . '">' . $this->param_pagination['previous'] . '</a></li>';
            }
        }

        // tampilkan link angka
        //			$end = ($end<=2) ? 1:$end;
        for ( $loop = ($start); ($loop) <= ($end); $loop++ ) {
            if ( $this->param_pagination['reverse_paging'] ) {
                $tmp = ($loop == 1) ? '' : ($num_pages + 2) - $loop;
                $output .= ($this->param_pagination['page'] == $loop) ? '<li class="paginate_button active"><a href="#">' . ($loop) . '</a></li>' : '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . ($loop) : str_replace('{PAGE}', $tmp, $this->param_pagination['base_url']) ) . '">' . ($loop) . '</a></li>';
            }
            else {
                $tmp = ($loop == 1) ? '' : $loop;
                //$output .= (($num_pages-$this->param_pagination['page']) == $loop) ? '&nbsp;<span class="selected">' . ($loop) . '</span>&nbsp;' : '&nbsp;<a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'].($loop) : str_replace('{PAGE}',$tmp,$this->param_pagination['base_url']) ) . '">' . ($loop) . '</a>&nbsp;';
                $output .= ($this->param_pagination['page'] == $loop) ? '<li class="paginate_button active"><a href="#">' . ($loop) . '</a></li>' : '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . ($loop) : str_replace('{PAGE}', $tmp, $this->param_pagination['base_url']) ) . '">' . ($loop) . '</a></li>';
            }
        }

        if ( $this->param_pagination['reverse_paging'] ) {
            // link next
            if ( $this->param_pagination['page'] < $num_pages && $this->param_pagination['next'] != '' )
                $output .= '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . (($num_pages - $this->param_pagination['page']) + 1) : str_replace('{PAGE}', (($num_pages - $this->param_pagination['page']) + 1), $this->param_pagination['base_url']) ) . '" class="link_next btn">' . $this->param_pagination['next'] . '</a></li>';

            // last link
            if ( ($this->param_pagination['page'] + $this->param_pagination['total_side_link']) < $num_pages && $this->param_pagination['last'] != '' )
                $output .= '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . '2' : str_replace('{PAGE}', '2', $this->param_pagination['base_url']) ) . '" class="link_last btn">' . $this->param_pagination['last'] . '</a>&nbsp;';
        }else {
            // link next
            if ( $this->param_pagination['page'] < $num_pages && $this->param_pagination['next'] != '' )
                $output .= '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . ($this->param_pagination['page'] + 1) : str_replace('{PAGE}', ($this->param_pagination['page'] + 1), $this->param_pagination['base_url']) ) . '" class="link_next btn">' . $this->param_pagination['next'] . '</a></li>';

            // last link
            if ( ($this->param_pagination['page'] + $this->param_pagination['total_side_link']) < $num_pages && $this->param_pagination['last'] != '' )
                $output .= '<li class="paginate_button"><a href="' . (($this->param_pagination['query_string']) ? $this->param_pagination['base_url'] . $num_pages : str_replace('{PAGE}', $num_pages, $this->param_pagination['base_url']) ) . '" class="link_last btn">' . $this->param_pagination['last'] . '</a></li>';
        }
        
        // Add the wrapper HTML if exists
        if ( $go_to_page )
            $output .= '</ul><div style="clear:both;"></div>';
        $output .= '</ul>';
        return $output;
    }

}

?>