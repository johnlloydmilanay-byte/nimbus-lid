<?php

namespace App\UserClass;

class Tool
{
    // utilities
    public static function year_generator($range, $selected)
    {
        $html = '';
        if ($selected != 0) {
            $year = $selected;
        } else {
            $year = intval(date('Y'));
        }
        if ($selected == 0) {
            $y = $year;
        } else {
            $y = $selected;
        }
        for ($i = $year; $i > 2023; $i--) {
            $html .= '<option value="'.$i.'" '.($i == $y ? 'selected' : '').'>'.$i.' - '.($i + 1).'<//option>';
        }

        return $html;
    }
}
