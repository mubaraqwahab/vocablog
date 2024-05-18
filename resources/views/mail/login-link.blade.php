<x-mail::message>
# Log in as {{ $email }}

<x-mail::button :url="$url">
Log in
</x-mail::button>

The link expires in 30 minutes.

If you didn't request this email, you can safely ignore it.
</x-mail::message>
