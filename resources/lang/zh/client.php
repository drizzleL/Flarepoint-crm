<?php

return [

    'status' => [
        'assigned' => '已分配的客户',
    ],

    'titles' => [
        'create' => '创建新客户',
        'update' => '更新客户',
    ],

    'headers' => [
        'name' => '姓名',
        'company' => '公司',
        'mail' => '邮箱',
        'primary_number' => '主要联系号码',
        'secondary_number' => '次要联系号码',
        'full_address' => '地址 / 邮编 / 城市',
        'vat' => '增值税抬头',
        'industry' => '行业',
        'company_type' => '公司类型',
    ],

    'tabs' => [
        'tasks' => '任务',
        'leads' => '线索',
        'documents' => '文档',
        'invoices' => '发票',
        'all_tasks' => '全部任务',
        'all_leads' => '全部线索',
        'all_documents' => '全部文档',
        'max_size' => '文件不得大于 5MB 。',
        //Headers on tables in tables
            'headers' => [
            //Title && Leads
                'title' => '标题',
                'assigned' => '分配的用户',
                'created_at' => '创建时间',
                'deadline' => '最后期限',
                'new_task' => '添加新任务',
                'new_lead' => '添加新线索',
                //Documments
                'file' => '文件',
                'size' => '体积',
                //Invoices
                'id' => 'ID',
                'hours' => '小时',
                'total_amount' => '总金额',
                'invoice_sent' => '发票已发送',
                'payment_received' => '已收款',
            ],
    ],
];
