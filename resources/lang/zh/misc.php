<?php

return [

    'notifications' => [
        'task' => [
            'created' => ':creator 创建了 :title，并指派给你。',
            'status' => ':username 完成了 :title',
            'time' =>  ':username 为 :title 插入了一个新时间',
            'assign' => ':username 指派给你一个任务',
        ],
        'lead' => [
            'created' => ':creator 创建了 :title，并指派给你。',
            'status' => ':username 完成了 :title',
            'deadline' => ':username 更新了 :title 的最后期限',
            'assign' => ':username 指派给你一个线索',
        ],
        'client' => [
            'created' => '客户 :company 已经被指派给你',
        ]
    ],
    'log' => [
        'task' => [
            'created' => ':creator 创建了 :title，并指派给:assignee。',
            'status' => ':username 完成了任务',
            'time' =>  ':username 给任务插入了新的时间',
            'assign' => ':username 指派任务给 :assignee',
        ],
        'lead' => [
            'created' => ':title was created by :creator and assigned to :assignee',
            'status' => ':username 完成了线索',
            'deadline' => ':username 更新了线索的最后期限',
            'assign' => ':username 指派任务给 :assignee',
        ],
        'client' => [
            'created' => '客户 :company 被指派给 :assignee',
        ]
    ]
];
