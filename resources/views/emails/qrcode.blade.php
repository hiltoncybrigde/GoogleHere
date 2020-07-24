@component('mail::message')
# Welcome to our world

this is your qr code please do not share this code to anyone

{{$data}}

<img src="{{ base64_decode($data) }}">


Thanks,<br>
Hilton
@endcomponent
