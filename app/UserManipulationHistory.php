<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserManipulationHistory extends Model
{
    protected $fillable = [
        'id',
        'wx_nickname',
        'current_manipulating_corporation',
        'previous_manipulated_corporation',
    ];

        /** 
        * 保存企业信息用于对话
        *
        * @param  array $config
        *
        */

        // public function set(string $corporation)
        // {
        //     $this->update(
        //         [
        //             'previous_manipulated_corporation' => $this->attribute('current_manipulating_corporation') ?? '无',
        //             'current_manipulating_corporation' => $corporation]
        //         ]
        //     );
        // }
    }
