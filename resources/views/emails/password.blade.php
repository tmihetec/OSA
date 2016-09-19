<!-- resources/views/emails/password.blade.php -->

<h1>OSA.STS.HR</h1>
<h4>postavljanje lozinke</h4>

<p>Za ponovno postavljanje lozinke klikni na slijedeći link:<br /> <a href="{{ $link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a> </p>

<p>STS</p>