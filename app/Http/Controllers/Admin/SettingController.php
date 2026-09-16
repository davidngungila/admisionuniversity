<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', ['settings'=>Setting::orderBy('group')->orderBy('key')->get()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings'=>['required','array'],
            'settings.*.key'=>['required','string'],
            'settings.*.value'=>['nullable','string'],
            'university_logo'=>['nullable','image','max:2048','mimes:png,jpg,jpeg,svg,webp'],
        ]);

        foreach ($data['settings'] as $item) {
            Setting::updateOrCreate(['key'=>$item['key']], ['value'=>$item['value']]);
        }

        if ($request->has('new_key') && $request->filled('new_key')) {
            Setting::create(['key'=>$request->new_key, 'value'=>$request->new_value ?? '', 'group'=>$request->new_group ?? 'general']);
        }

        if ($request->hasFile('university_logo')) {
            $path = $request->file('university_logo')->store('logos', 'public');
            Setting::setValue('university_logo', 'storage/'.$path, 'general');
        }

        return back()->with('success','Settings saved.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();
        return back()->with('success','Deleted.');
    }

    public function smsTest(Request $request)
    {
        $d = $request->validate([
            'to' => ['required', 'string', 'max:191'],
        ]);

        $provider = Setting::getValue('sms_provider', 'log');
        $testMode = (bool) Setting::getValue('sms_test_mode', true);
        $hasKey   = (string) Setting::getValue('sms_api_key', '') !== '';
        $enabled  = (bool) Setting::getValue('sms_enabled', true);

        if (! $enabled) {
            $msg = 'SMS is disabled — turn on "SMS Enabled" in the SMS tab first.';

            return $request->wantsJson() ? response()->json(['ok' => true, 'success' => false, 'message' => $msg]) : back()->with('error', $msg);
        }

        if ($provider === 'messaging' && ! $hasKey) {
            $msg = 'No Bearer Token configured — paste your API token in the SMS tab first.';

            return $request->wantsJson() ? response()->json(['ok' => true, 'success' => false, 'message' => $msg]) : back()->with('error', $msg);
        }

        $university = Setting::getValue('university_name', 'Online Admission System');
        $message    = 'This is a TEST SMS from '.$university.' Online Admission System. If you received this message, your SMS integration is working correctly.';

        $ok = app(\App\Services\SmsService::class)->send($d['to'], $message, 'test', 'Test SMS', auth()->id());

        $detail = $provider === 'messaging'
            ? ' (messaging-service.co.tz '.($testMode ? 'TEST' : 'LIVE').' endpoint)'
            : ' (log driver — recorded in SMS Logs, no delivery)';

        $msg = $ok
            ? 'Test SMS sent to '.$d['to'].' successfully'.$detail.'. Check the recipient phone or SMS Logs.'
            : 'Test SMS failed'.$detail.'. Check credentials, sender ID and network, then check SMS Logs for details.';

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'success' => $ok, 'message' => $msg]);
        }

        return $ok ? back()->with('success', $msg) : back()->with('error', $msg);
    }
}