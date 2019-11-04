@component('mail::message')
# {{ 'Hello ' . $user->name . '!' }}

You can now access Check Monitoring as {{ $user->access->name }}.

**Username**: {{ $user->username }} **Password**: {{ $password }}

@component('mail::button', ['url' => url(config('app.ui_url')), 'color' => 'primary'])
Go to App
@endcomponent

@component('mail::button', ['url' => url(config('app.ui_url') . '/reset-password'), 'color' => 'error'])
Reset Password
@endcomponent

Thank you for using our application!

@lang('Regards'),<br>
{{ config('app.name') }}
@endcomponent
