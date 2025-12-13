<?php

use App\Models\SettingModel;
use App\Services\SettingServiceStore;
use Illuminate\Http\Request;

useRefreshDatabaseConditionally();

it('saves structured emails settings payload', function () {
    $setting = SettingModel::create(['key'=>'emails','value'=>json_encode([])]);

    $payload = [
        'actions' => [
            'welcome' => ['enabled' => 'on', 'subject' => 'Hello', 'template' => 'send_mail', 'body' => 'Welcome {name}'],
            'approved' => ['enabled' => 'off', 'subject' => '', 'template' => '', 'body' => ''],
        ],
    ];

    // Simulate UpdateSettingRequest transformation
    $final = ['key' => 'emails', 'value' => json_encode([
        'welcome' => ['enabled'=>'on','subject'=>'Hello','template'=>'send_mail','body'=>'Welcome {name}'],
        'approved' => ['enabled'=>'off','subject'=>'','template'=>'send_mail','body'=>''],
    ])];

    $svc = app(SettingServiceStore::class);
    $svc->update($setting->id, Request::create('/dashboard/setting/emails', 'POST', $final));

    $rec = SettingModel::where('key','emails')->firstOrFail();
    $val = json_decode($rec->value, true);
    expect($val['welcome']['enabled'])->toBe('on')
        ->and($val['welcome']['subject'])->toBe('Hello');
});
