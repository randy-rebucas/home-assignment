<x-mail::message>
# Introduction

Hi {{ $employee->user->name }},

Your been added by the manager with an email: {{ $employee->user->email }} and temporary password of qwerty12345.

Please save and change your password immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
