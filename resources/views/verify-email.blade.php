@component('mail::message')
# Email Verification

Hello {{$user['name']}},
## Your registered email-id is {{$user['email']}}
## Please click on the blow link to verify email account

@component('mail::button', ['url'=>url('api/user/verify', $user->verifyUser->token)])
Click Here
@endcomponent


Best, <br>
Bararkos
@endcomponent
