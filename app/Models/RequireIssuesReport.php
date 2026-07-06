<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequireIssuesReport extends Model
{
    //
    protected $table = "require_issues_report";
    protected $fillable = [
        'name',
        'type',
        'level',
        'status',
        'sponsor',
        'assigned_to_user_id',
        'handler_user_id',
        'resolved_at',
        'description',
        'attachments',
        'reported_ip',
        'reported_device_fingerprint'
    ];
}
